<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\LogAktivitas;
use App\Models\Pengumpulan;
use App\Models\Tugas;
use App\Support\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    public function create(Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);

        return view('dosen.tugas.form', ['kelas' => $kelas, 'tugas' => new Tugas(['jenis' => request('jenis', 'tugas')]), 'menuAktif' => 'tugas']);
    }

    public function edit(Tugas $tugas)
    {
        $this->pastikanPengampu($tugas->kelas);

        return view('dosen.tugas.form', ['kelas' => $tugas->kelas, 'tugas' => $tugas, 'menuAktif' => 'tugas']);
    }

    // Halaman "Tugas Selesai": file yang dikumpulkan mahasiswa + kotak nilai
    public function terkumpul(Tugas $tugas)
    {
        $kelas = $tugas->kelas;
        $this->pastikanPengampu($kelas);

        return view('dosen.tugas.terkumpul', [
            'tugas' => $tugas,
            'kelas' => $kelas,
            'menuAktif' => 'tugas',
            'pengumpulan' => $tugas->pengumpulan()->with('mahasiswa')->get()->sortBy('mahasiswa.name'),
            'belum' => $kelas->mahasiswa()->whereNotIn('users.id', $tugas->pengumpulan()->pluck('mahasiswa_id'))->orderBy('name')->get(),
        ]);
    }

    // Menerbitkan tugas atau modul praktikum lengkap dengan deadline
    public function store(Request $request, Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);
        $data = $this->validasi($request, $kelas);

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('tugas');
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }
        unset($data['file']);

        $tugas = $kelas->tugas()->create($data);
        LogAktivitas::catat('Menerbitkan ' . $tugas->jenis . ' ' . $tugas->judul);

        Notifikasi::kirim(
            $kelas->mahasiswa,
            ucfirst($tugas->jenis) . ' baru: ' . $tugas->judul,
            ucfirst($tugas->jenis) . ' baru pada ' . $kelas->nama . '. Batas pengumpulan: '
                . $tugas->deadline->translatedFormat('d M Y H:i') . '.',
            route('mahasiswa.tugas.show', $tugas)
        );

        return redirect()->route('dosen.kelas.show', [$kelas, 'tab' => 'tugas'])
            ->with('sukses', ucfirst($tugas->jenis) . ' diterbitkan dan mahasiswa diberi notifikasi email.');
    }

    // Detail tugas: daftar pengumpulan mahasiswa + form penilaian
    public function show(Tugas $tugas)
    {
        $kelas = $tugas->kelas;
        $this->pastikanPengampu($kelas);

        return view('dosen.tugas.show', [
            'tugas' => $tugas->loadCount('pengumpulan'),
            'kelas' => $kelas,
            'menuAktif' => 'tugas',
            'jumlahMahasiswa' => $kelas->mahasiswa()->count(),
        ]);
    }

    public function update(Request $request, Tugas $tugas)
    {
        $this->pastikanPengampu($tugas->kelas);
        $data = $this->validasi($request, $tugas->kelas);

        if ($request->hasFile('file')) {
            if ($tugas->file_path) {
                Storage::delete($tugas->file_path);
            }
            $data['file_path'] = $request->file('file')->store('tugas');
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }
        unset($data['file']);
        // Jika deadline diundur, pengingat boleh dikirim lagi
        $data['pengingat_terkirim'] = false;
        $tugas->update($data);

        return redirect()->route('dosen.tugas.show', $tugas)->with('sukses', ucfirst($tugas->jenis) . ' diperbarui.');
    }

    public function destroy(Tugas $tugas)
    {
        $kelas = $tugas->kelas;
        $this->pastikanPengampu($kelas);
        $tugas->delete();

        return redirect()->route('dosen.kelas.show', [$kelas, 'tab' => 'tugas'])->with('sukses', 'Tugas dihapus.');
    }

    // Memberi nilai dan umpan balik pada satu pengumpulan
    public function nilai(Request $request, Pengumpulan $pengumpulan)
    {
        $tugas = $pengumpulan->tugas;
        $this->pastikanPengampu($tugas->kelas);

        $pengumpulan->timestamps = false; // supaya waktu pengumpulan tidak berubah
        $pengumpulan->update($request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|max:1000',
        ], ['nilai.max' => 'Nilai maksimal 100.', 'nilai.integer' => 'Nilai harus angka bulat.']));
        LogAktivitas::catat('Menilai tugas ' . $tugas->judul . ' milik ' . $pengumpulan->mahasiswa->name);

        Notifikasi::kirim(
            $pengumpulan->mahasiswa,
            'Nilai keluar: ' . $tugas->judul,
            'Tugas "' . $tugas->judul . '" sudah dinilai. Nilai kamu: ' . $pengumpulan->nilai . '.',
            route('mahasiswa.tugas.show', $tugas)
        );

        return back()->with('sukses', 'Nilai ' . $pengumpulan->mahasiswa->name . ' disimpan.');
    }

    private function validasi(Request $request, Kelas $kelas): array
    {
        return $request->validate([
            'jenis' => 'required|in:tugas,praktikum',
            'judul' => 'required|max:150',
            'instruksi' => 'required|max:5000',
            'deadline' => 'required|date',
            'file' => 'nullable|file|max:20480',
        ], [
            'jenis.required' => 'Pilih Tugas atau Praktikum.',
            'instruksi.required' => 'Deskripsi wajib diisi.',
            'file.max' => 'Ukuran file maksimal 20 MB.',
        ]);
    }
}
