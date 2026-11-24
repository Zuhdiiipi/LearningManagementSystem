<?php

namespace App\Http\Controllers;

use App\Models\HasilKuis;
use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\LogAktivitas;
use App\Models\Pengumpulan;
use App\Models\Tugas;
use App\Models\User;

class DashboardController extends Controller
{
    // /dashboard mengarahkan pengguna ke dashboard sesuai perannya
    public function index()
    {
        return redirect()->route(auth()->user()->role . '.dashboard');
    }

    public function admin()
    {
        return view('dashboard.admin', [
            'totalPengguna' => User::count(),
            'totalKelas' => Kelas::count(),
            'kelasAktif' => Kelas::where('status', 'aktif')->count(),
            'penggunaAktif' => User::where('is_active', true)->count(),
        ]);
    }

    public function dosen()
    {
        $idKelas = auth()->user()->kelasDiampu()->pluck('id');

        return view('dashboard.dosen', [
            'perluDinilai' => Pengumpulan::whereNull('nilai')
                ->whereHas('tugas', fn ($q) => $q->whereIn('kelas_id', $idKelas))->count(),
            'daftarKelas' => auth()->user()->kelasDiampu()->with(['mataKuliah', 'dosen'])
                ->withCount(['materi', 'tugas', 'mahasiswa'])->latest()->get(),
        ]);
    }

    public function mahasiswa()
    {
        $saya = auth()->id();
        $idKelas = auth()->user()->kelasDiikuti()->pluck('kelas.id');
        $sudahKumpul = Pengumpulan::where('mahasiswa_id', $saya)->pluck('tugas_id');
        $sudahKuis = HasilKuis::where('mahasiswa_id', $saya)->pluck('kuis_id');

        $tugasBelum = Tugas::with('kelas')->whereIn('kelas_id', $idKelas)->whereNotIn('id', $sudahKumpul)
            ->where('deadline', '>', now())->orderBy('deadline')->get();
        $kuisTersedia = Kuis::with('kelas')->whereIn('kelas_id', $idKelas)->whereNotIn('id', $sudahKuis)
            ->where('dibuka', true)->where('selesai', '>', now())->get();

        return view('dashboard.mahasiswa', [
            'jumlahKelas' => $idKelas->count(),
            'tugasBelum' => $tugasBelum,
            'kuisTersedia' => $kuisTersedia,
            'daftarKelas' => auth()->user()->kelasDiikuti()->where('status', 'aktif')->with(['mataKuliah', 'dosen'])->withCount(['materi', 'tugas'])->get(),
        ]);
    }
}
