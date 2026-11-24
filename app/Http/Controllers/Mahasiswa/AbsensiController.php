<?php

namespace App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Pertemuan;
class AbsensiController extends Controller
{
    // Mahasiswa menekan tombol "Hadir" saat sesi absensi dibuka dosen
    public function hadir(Pertemuan $pertemuan)
    {
        $this->pastikanAnggota($pertemuan->kelas);
        $sesi = $pertemuan->sesiAbsensi;

        if (! $sesi || ! $sesi->sedangDibuka()) {
            return back()->with('gagal', 'Absensi untuk pertemuan ini tidak sedang dibuka.');
        }

        Absensi::firstOrCreate(
            ['sesi_absensi_id' => $sesi->id, 'mahasiswa_id' => auth()->id()],
            ['status' => 'hadir']
        );

        return back()->with('sukses', 'Kehadiran pertemuan ' . $pertemuan->nomor . ' tercatat.');
    }
}
