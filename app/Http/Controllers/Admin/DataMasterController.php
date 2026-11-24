<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DataMasterController extends Controller
{
    // Satu halaman berisi 3 tab: program studi, mata kuliah, tahun ajaran
    public function index(Request $request)
    {
        return view('admin.master.index', [
            'tab' => $request->get('tab', 'prodi'),
            'prodi' => ProgramStudi::withCount('mataKuliah')->orderBy('nama')->get(),
            'matkul' => MataKuliah::with('programStudi')->orderBy('nama')->get(),
            'tahun' => TahunAjaran::orderByDesc('id')->get(),
        ]);
    }

    // ===== Program studi =====
    public function storeProdi(Request $request)
    {
        $data = $request->validate([
            'kode' => 'required|max:20|unique:program_studi,kode',
            'nama' => 'required|max:100',
        ]);
        ProgramStudi::create($data);
        LogAktivitas::catat('Menambah program studi ' . $data['nama']);

        return redirect()->route('admin.master.index', ['tab' => 'prodi'])->with('sukses', 'Program studi ditambahkan.');
    }

    public function updateProdi(Request $request, ProgramStudi $prodi)
    {
        $prodi->update($request->validate([
            'kode' => ['required', 'max:20', Rule::unique('program_studi', 'kode')->ignore($prodi->id)],
            'nama' => 'required|max:100',
        ]));

        return redirect()->route('admin.master.index', ['tab' => 'prodi'])->with('sukses', 'Program studi diperbarui.');
    }

    public function destroyProdi(ProgramStudi $prodi)
    {
        $prodi->delete();

        return redirect()->route('admin.master.index', ['tab' => 'prodi'])->with('sukses', 'Program studi dihapus.');
    }

    // ===== Mata kuliah =====
    public function storeMatkul(Request $request)
    {
        $data = $request->validate([
            'program_studi_id' => 'required|exists:program_studi,id',
            'kode' => 'required|max:20|unique:mata_kuliah,kode',
            'nama' => 'required|max:100',
            'sks' => 'required|integer|min:1|max:6',
        ]);
        MataKuliah::create($data);
        LogAktivitas::catat('Menambah mata kuliah ' . $data['nama']);

        return redirect()->route('admin.master.index', ['tab' => 'matkul'])->with('sukses', 'Mata kuliah ditambahkan.');
    }

    public function updateMatkul(Request $request, MataKuliah $matkul)
    {
        $matkul->update($request->validate([
            'program_studi_id' => 'required|exists:program_studi,id',
            'kode' => ['required', 'max:20', Rule::unique('mata_kuliah', 'kode')->ignore($matkul->id)],
            'nama' => 'required|max:100',
            'sks' => 'required|integer|min:1|max:6',
        ]));

        return redirect()->route('admin.master.index', ['tab' => 'matkul'])->with('sukses', 'Mata kuliah diperbarui.');
    }

    public function destroyMatkul(MataKuliah $matkul)
    {
        $matkul->delete();

        return redirect()->route('admin.master.index', ['tab' => 'matkul'])->with('sukses', 'Mata kuliah dihapus.');
    }

    // ===== Tahun ajaran =====
    public function storeTahun(Request $request)
    {
        $data = $request->validate(['nama' => 'required|max:50']);
        TahunAjaran::create($data);

        return redirect()->route('admin.master.index', ['tab' => 'tahun'])->with('sukses', 'Tahun ajaran ditambahkan.');
    }

    // Hanya satu tahun ajaran yang aktif dalam satu waktu
    public function aktifkanTahun(TahunAjaran $tahun)
    {
        TahunAjaran::query()->update(['is_active' => false]);
        $tahun->update(['is_active' => true]);

        return redirect()->route('admin.master.index', ['tab' => 'tahun'])->with('sukses', $tahun->nama . ' sekarang aktif.');
    }

    public function destroyTahun(TahunAjaran $tahun)
    {
        $tahun->delete();

        return redirect()->route('admin.master.index', ['tab' => 'tahun'])->with('sukses', 'Tahun ajaran dihapus.');
    }
}
