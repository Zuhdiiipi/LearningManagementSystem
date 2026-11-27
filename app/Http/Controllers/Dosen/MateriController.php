<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\LogAktivitas;
use App\Models\Materi;
use App\Support\Lampiran;
use App\Support\Notifikasi;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MateriController extends Controller
{
    public function create(Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);

        return view('dosen.materi.form', [
            'kelas' => $kelas,
            'materi' => new Materi(),
            'menuAktif' => 'materi',
        ]);
    }

    public function store(Request $request, Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);
        $data = $this->validasi($request, true);
        $files = Lampiran::dariInput($data);

        $materi = [
            'kelas_id' => $kelas->id,
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'tipe' => $files ? 'file' : 'link',
            'url' => $data['url'] ?? null,
        ];

        Lampiran::simpan($files, 'materi', fn(array $atribut) => Materi::create($materi + $atribut));
        LogAktivitas::catat('Mengunggah materi ' . $data['judul']);

        Notifikasi::kirim(
            $kelas->mahasiswa,
            'Materi baru: ' . $data['judul'],
            'Materi baru telah ditambahkan pada ' . $kelas->mataKuliah->nama . ' (' . $kelas->nama . ').',
            route('mahasiswa.kelas.show', $kelas)
        );

        return redirect()->route('dosen.kelas.show', [$kelas, 'tab' => 'materi'])
            ->with('sukses', 'Materi ditambahkan dan mahasiswa diberi notifikasi email.');
    }

    public function show(Materi $materi)
    {
        $this->pastikanPengampu($materi->kelas);

        return view('dosen.materi.show', [
            'materi' => $materi,
            'kelas' => $materi->kelas,
            'menuAktif' => 'materi',
        ]);
    }

    public function edit(Materi $materi)
    {
        $this->pastikanPengampu($materi->kelas);

        return view('dosen.materi.form', [
            'kelas' => $materi->kelas,
            'materi' => $materi,
            'menuAktif' => 'materi',
        ]);
    }

    public function update(Request $request, Materi $materi)
    {
        $this->pastikanPengampu($materi->kelas);
        $data = $this->validasi($request, false);
        $files = Lampiran::dariInput($data);
        $pilihanHapus = array_flip($data['hapus_lampiran'] ?? []);

        $lampiranDihapus = Lampiran::simpan(
            $files,
            'materi',
            function (array $atribut) use ($materi, $data, $pilihanHapus) {
                return DB::transaction(function () use ($materi, $data, $atribut, $pilihanHapus) {
                    $terkunci = Materi::query()->lockForUpdate()->findOrFail($materi->id);
                    $url = array_key_exists('url', $data) ? $data['url'] : $terkunci->url;

                    [$dihapus, $tersisa] = collect($terkunci->daftarLampiran())->partition(
                        fn(array $file) => $terkunci->tipe === 'file'
                            && isset($pilihanHapus[hash('sha256', $file['path'])])
                    );

                    $ubah = [
                        'judul' => $data['judul'],
                        'deskripsi' => $data['deskripsi'] ?? null,
                        'url' => $url,
                    ];

                    if ($atribut['lampiran'] || $dihapus->isNotEmpty()) {
                        $lampiran = [...$tersisa->all(), ...$atribut['lampiran']];

                        $ubah += [
                            'tipe' => $lampiran ? 'file' : 'link',
                            'lampiran' => $lampiran,
                            'file_path' => $lampiran[0]['path'] ?? null,
                            'file_name' => $lampiran[0]['nama'] ?? null,
                        ];
                    }

                    if (($ubah['tipe'] ?? $terkunci->tipe) === 'link' && blank($url)) {
                        throw ValidationException::withMessages([
                            'url' => 'Isi tautan atau unggah minimal satu file materi.',
                        ]);
                    }

                    $terkunci->update($ubah);

                    return $dihapus->all();
                });
            }
        );

        Lampiran::hapus($lampiranDihapus);

        return redirect()->route('dosen.kelas.show', [$materi->kelas, 'tab' => 'materi'])
            ->with('sukses', 'Materi diperbarui.');
    }

    public function destroy(Materi $materi)
    {
        $this->pastikanPengampu($materi->kelas);

        $lampiran = DB::transaction(function () use ($materi) {
            $terkunci = Materi::query()->lockForUpdate()->findOrFail($materi->id);
            $lampiran = $terkunci->daftarLampiran();
            $terkunci->delete();

            return $lampiran;
        });

        Lampiran::hapus($lampiran);

        return redirect()->route('dosen.kelas.show', [$materi->kelas, 'tab' => 'materi'])
            ->with('sukses', 'Materi dihapus.');
    }

    private function validasi(Request $request, bool $baru): array
    {
        $aturanFile = [
            'bail',
            'file',
            'max:512000',
            'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,txt,png,jpg,jpeg,mp4,webm',
            function (string $atribut, $file, Closure $gagal): void {
                if (
                    $file->getSize() > 50 * 1024 * 1024
                    && ! in_array($file->getMimeType(), ['video/mp4', 'video/webm'], true)
                ) {
                    $gagal('File selain video maksimal 50 MB.');
                }
            },
        ];

        $pesanBatas = 'Ukuran maksimal 500 MB untuk video dan 50 MB untuk file lainnya.';

        return $request->validate([
            'judul' => 'required|max:150',
            'deskripsi' => 'nullable|max:3000',
            'files' => ($baru ? 'required_without_all:url,file|' : '') . 'nullable|array',
            'files.*' => ['required', ...$aturanFile],
            'file' => ['nullable', ...$aturanFile],
            'jumlah_file' => 'nullable|integer|min:0',
            'url' => 'nullable|url|max:255',
            'hapus_lampiran' => 'sometimes|array',
            'hapus_lampiran.*' => ['required', 'string', 'regex:/^[0-9a-f]{64}$/D'],
        ], [
            'files.required_without_all' => 'Masukkan file materi atau isi link.',
            'files.*.max' => $pesanBatas,
            'files.*.mimes' => 'Ada format file yang tidak didukung.',
            'file.max' => $pesanBatas,
            'file.mimes' => 'Format file tidak didukung.',
            'url.url' => 'Link harus diawali http:// atau https://',
            'url.max' => 'Tautan materi maksimal 255 karakter.',
            'hapus_lampiran.*.regex' => 'Pilihan lampiran tidak valid. Buka ulang form untuk memilih file.',
        ]);
    }
}
