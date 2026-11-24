<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    // Kelola Kelas: semua kelas dalam bentuk kartu
    public function index(Request $request)
    {
        $kelas = Kelas::with(['mataKuliah', 'dosen'])
            ->withCount('mahasiswa')
            ->when($request->cari, fn ($q, $cari) => $q->where(fn ($q) => $q
                ->where('nama', 'like', "%$cari%")
                ->orWhereHas('mataKuliah', fn ($q) => $q->where('nama', 'like', "%$cari%"))
                ->orWhereHas('dosen', fn ($q) => $q->where('name', 'like', "%$cari%"))))
            ->latest()
            ->get();

        return view('admin.kelas.index', compact('kelas'));
    }

    // Detail satu kelas
    public function show(Kelas $kelas)
    {
        $kelas->load(['mataKuliah.programStudi', 'dosen', 'tahunAjaran', 'mahasiswa' => fn ($q) => $q->orderBy('name')])
              ->loadCount(['materi', 'tugas', 'kuis', 'pertemuan']);

        return view('admin.kelas.show', compact('kelas'));
    }

    // Form ubah kelas
    public function edit(Kelas $kelas)
    {
        return view('admin.kelas.form', [
            'kelas' => $kelas,
            'matkul' => MataKuliah::orderBy('nama')->get(),
            'dosen' => User::where('role', 'dosen')->orderBy('name')->get(),
            'tahun' => TahunAjaran::orderByDesc('nama')->get(),
        ]);
    }

    // Simpan perubahan kelas
    public function update(Request $request, Kelas $kelas)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'dosen_id' => ['required', Rule::exists('users', 'id')->where('role', 'dosen')],
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'semester' => 'required|in:ganjil,genap',
            'status' => 'required|in:aktif,draft',
        ]);

        $kelas->update($data);

        return redirect()->route('admin.kelas.index')->with('sukses', 'Kelas berhasil diperbarui.');
    }

    // Hapus kelas beserta isinya
    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return back()->with('sukses', 'Kelas berhasil dihapus.');
    }
}
