<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilKuis;
use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\LogAktivitas;
use App\Models\LogNotifikasi;
use App\Models\Materi;
use App\Models\Pengumpulan;
use App\Models\Tugas;
use App\Models\User;

class LaporanController extends Controller
{
    // Laporan penggunaan sistem untuk Admin
    public function index()
    {
        return view('admin.laporan', [
            'statistik' => [
                'Dosen' => User::where('role', 'dosen')->count(),
                'Mahasiswa' => User::where('role', 'mahasiswa')->count(),
                'Kelas' => Kelas::count(),
                'Materi' => Materi::count(),
                'Tugas & Praktikum' => Tugas::count(),
                'Pengumpulan Tugas' => Pengumpulan::count(),
                'Kuis' => Kuis::count(),
                'Kuis Dikerjakan' => HasilKuis::count(),
            ],
            'kelas' => Kelas::with(['mataKuliah', 'dosen'])->withCount('mahasiswa')->latest()->get(),
            'log' => LogAktivitas::with('user')->latest()->paginate(15),
            'email' => LogNotifikasi::latest('waktu_kirim')->take(30)->get(),
        ]);
    }
}
