<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\LogAktivitas;
use App\Models\Materi;
use App\Support\Lampiran;
use App\Support\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        ];

        $materi += $files
            ? ['tipe' => 'file']
            : ['tipe' => 'link', 'url' => $data['url']];

        Lampiran::simpan(
            $files,
            'materi',
            fn(array $atribut) => Materi::create($materi + $atribut)
        );

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
            'materi' => $materi,
            'kelas' => $materi->kelas,
            'menuAktif' => 'materi',
        ]);
    }

    public function update(Request $request, Materi $materi)
    {
        $this->pastikanPengampu($materi->kelas);

        $data = $this->validasi($request, false);
        $files = Lampiran::dariInput($data);

        $ubah = [
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
        ];

        if ($files || ! empty($data['url'])) {
            $ubah += $files
                ? ['tipe' => 'file', 'url' => null]
                : ['tipe' => 'link', 'url' => $data['url']];

            $lampiranLama = Lampiran::simpan($files, 'materi', function (array $atribut) use ($materi, $ubah) {
                return DB::transaction(function () use ($materi, $ubah, $atribut) {
                    $terkunci = Materi::query()->lockForUpdate()->findOrFail($materi->id);
                    $lama = $terkunci->daftarLampiran();

                    $terkunci->update($ubah + $atribut);

                    return $lama;
                });
            });

            Lampiran::hapus($lampiranLama);
        } else {
            $materi->update($ubah);
        }

        return redirect()->route('dosen.kelas.show', [$materi->kelas, 'tab' => 'materi'])
            ->with('sukses', 'Materi diperbarui.');
    }

    public function akses(Request $request, Materi $materi)
    {
        $this->pastikanPengampu($materi->kelas);

        $request->validate(
            ['boleh_unduh' => 'required|in:0,1'],
            ['boleh_unduh.required' => 'Pilih izin unduh terlebih dahulu.']
        );

        $materi->update(['boleh_unduh' => $request->boolean('boleh_unduh')]);

        LogAktivitas::catat(
            'Mengatur izin unduh materi ' . $materi->judul . ': '
                . ($materi->boleh_unduh ? 'diizinkan' : 'tidak diizinkan')
        );

        $status = $materi->boleh_unduh ? 'boleh diunduh' : 'hanya dapat dilihat';

        return redirect()->route('dosen.kelas.show', [$materi->kelas, 'tab' => 'materi'])
            ->with('sukses', 'Izin unduh "' . $materi->judul . '" berhasil diperbarui: ' . $status . '.');
    }

    public function destroy(Materi $materi)
    {
        $kelas = $materi->kelas;
        $this->pastikanPengampu($kelas);

        $lampiran = DB::transaction(function () use ($materi) {
            $terkunci = Materi::query()->lockForUpdate()->findOrFail($materi->id);
            $lampiran = $terkunci->daftarLampiran();

            $terkunci->delete();

            return $lampiran;
        });

        Lampiran::hapus($lampiran);

        return redirect()->route('dosen.kelas.show', [$kelas, 'tab' => 'materi'])
            ->with('sukses', 'Materi dihapus.');
    }

    private function validasi(Request $request, bool $baru): array
    {
        $aturanFile = 'file|max:512000|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,txt,png,jpg,jpeg,mp4,webm';

        return $request->validate([
            'judul' => 'required|max:150',
            'deskripsi' => 'nullable|max:3000',
            'files' => ($baru ? 'required_without_all:url,file|' : '') . 'nullable|array',
            'files.*' => 'required|' . $aturanFile,
            'file' => 'nullable|' . $aturanFile,
            'jumlah_file' => 'nullable|integer|min:0',
            'url' => 'nullable|url',
        ], [
            'files.required_without_all' => 'Masukkan file materi atau isi link.',
            'files.*.max' => 'Ukuran setiap file maksimal 50 MB.',
            'files.*.mimes' => 'Ada format file yang tidak didukung.',
            'file.max' => 'Ukuran file maksimal 50 MB.',
            'file.mimes' => 'Format file tidak didukung.',
            'url.url' => 'Link harus diawali http:// atau https://',
        ]);
    }
}
