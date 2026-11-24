<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\LogAktivitas;
use App\Models\Materi;
use App\Support\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function create(Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);

        return view('dosen.materi.form', ['kelas' => $kelas, 'materi' => new Materi(), 'menuAktif' => 'materi']);
    }

    // Materi berupa file (PDF, PPT, DOC, video, dll) atau link (YouTube, Drive)
    public function store(Request $request, Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);
        $data = $this->validasi($request, true);

        $materi = ['kelas_id' => $kelas->id, 'judul' => $data['judul'], 'deskripsi' => $data['deskripsi'] ?? null, 'boleh_unduh' => $request->boolean('boleh_unduh')];
        if ($request->hasFile('file')) {
            $materi += ['tipe' => 'file', 'file_path' => $request->file('file')->store('materi'), 'file_name' => $request->file('file')->getClientOriginalName()];
        } else {
            $materi += ['tipe' => 'link', 'url' => $data['url']];
        }
        Materi::create($materi);
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

        return view('dosen.materi.show', ['materi' => $materi, 'kelas' => $materi->kelas, 'menuAktif' => 'materi']);
    }

    public function edit(Materi $materi)
    {
        $this->pastikanPengampu($materi->kelas);

        return view('dosen.materi.form', ['kelas' => $materi->kelas, 'materi' => $materi, 'menuAktif' => 'materi']);
    }

    public function update(Request $request, Materi $materi)
    {
        $this->pastikanPengampu($materi->kelas);
        $data = $this->validasi($request, false);

        $ubah = ['judul' => $data['judul'], 'deskripsi' => $data['deskripsi'] ?? null, 'boleh_unduh' => $request->boolean('boleh_unduh')];
        if ($request->hasFile('file')) {
            if ($materi->file_path) {
                Storage::delete($materi->file_path);
            }
            $ubah += ['tipe' => 'file', 'file_path' => $request->file('file')->store('materi'), 'file_name' => $request->file('file')->getClientOriginalName(), 'url' => null];
        } elseif (! empty($data['url'])) {
            $ubah += ['tipe' => 'link', 'url' => $data['url']];
        }
        $materi->update($ubah);

        return redirect()->route('dosen.kelas.show', [$materi->kelas, 'tab' => 'materi'])->with('sukses', 'Materi diperbarui.');
    }

    // Pengaturan akses: dosen menentukan materi boleh diunduh atau hanya dilihat (FR-11)
    public function akses(Request $request, Materi $materi)
    {
        $this->pastikanPengampu($materi->kelas);
        $request->validate(['boleh_unduh' => 'required|in:0,1'], ['boleh_unduh.required' => 'Pilih izin unduh terlebih dahulu.']);
        $materi->update(['boleh_unduh' => $request->boolean('boleh_unduh')]);
        LogAktivitas::catat('Mengatur izin unduh materi ' . $materi->judul . ': ' . ($materi->boleh_unduh ? 'diizinkan' : 'tidak diizinkan'));

        return redirect()->route('dosen.kelas.show', [$materi->kelas, 'tab' => 'materi'])
            ->with('sukses', 'Izin unduh "' . $materi->judul . '" berhasil diperbarui: ' . ($materi->boleh_unduh ? 'boleh diunduh.' : 'hanya dapat dilihat.'));
    }

    public function destroy(Materi $materi)
    {
        $this->pastikanPengampu($materi->kelas);
        if ($materi->file_path) {
            Storage::delete($materi->file_path);
        }
        $materi->delete();

        return redirect()->route('dosen.kelas.show', [$materi->kelas, 'tab' => 'materi'])->with('sukses', 'Materi dihapus.');
    }

    private function validasi(Request $request, bool $baru): array
    {
        return $request->validate([
            'judul' => 'required|max:150',
            'deskripsi' => 'nullable|max:3000',
            // Saat menambah: wajib ada file ATAU link
            'file' => ($baru ? 'required_without:url|' : '') . 'nullable|file|max:51200|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,txt,png,jpg,jpeg,mp4',
            'url' => 'nullable|url',
        ], [
            'file.required_without' => 'Masukkan file materi atau isi link.',
            'file.max' => 'Ukuran file maksimal 50 MB.',
            'file.mimes' => 'Format file tidak didukung.',
            'url.url' => 'Link harus diawali http:// atau https://',
        ]);
    }
}
