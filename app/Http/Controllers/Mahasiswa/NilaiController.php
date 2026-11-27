<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Support\RekapNilai;

class NilaiController extends Controller
{
    public function index()
    {
        $daftar = auth()->user()->kelasDiikuti()->with('mataKuliah')->get()->map(fn($kelas) => [
            'kelas' => $kelas,
            'rekap' => collect(RekapNilai::kelas($kelas))->firstWhere('mahasiswa.id', auth()->id()),
        ]);

        return view('mahasiswa.nilai', ['daftar' => $daftar]);
    }
}
