<?php

namespace App\Support;

use App\Models\Absensi;
use App\Models\HasilKuis;
use App\Models\Kelas;
use App\Models\Pengumpulan;
use App\Models\SesiAbsensi;

class RekapNilai
{
    // Bobot awal untuk kelas yang belum memiliki pengaturan dosen
    public const BOBOT = ['tugas' => 30, 'praktikum' => 20, 'kuis' => 30, 'kehadiran' => 20];

    public static function bobot(Kelas $kelas): array
    {
        return $kelas->bobot_penilaian ?? self::BOBOT;
    }

    // Menghasilkan rekap nilai dan progres seluruh mahasiswa dalam satu kelas
    public static function kelas(Kelas $kelas): array
    {
        $bobot = self::bobot($kelas);
        $tugas = $kelas->tugas()->get();
        $idTugas = $tugas->where('jenis', 'tugas')->pluck('id');
        $idPraktikum = $tugas->where('jenis', 'praktikum')->pluck('id');
        $idKuis = $kelas->kuis()->pluck('id');

        // Hanya absensi yang sudah dibuka yang dihitung
        $idSesi = SesiAbsensi::whereIn('pertemuan_id', $kelas->pertemuan()->pluck('id'))
            ->where('dibuka_pada', '<=', now())
            ->pluck('id');

        $rekap = [];

        foreach ($kelas->mahasiswa()->orderBy('name')->get() as $mhs) {
            $kumpul = Pengumpulan::where('mahasiswa_id', $mhs->id)
                ->whereIn('tugas_id', $tugas->pluck('id'))
                ->get();
            $kuis = HasilKuis::where('mahasiswa_id', $mhs->id)->whereIn('kuis_id', $idKuis)->get();
            $hadir = Absensi::where('mahasiswa_id', $mhs->id)->whereIn('sesi_absensi_id', $idSesi)
                ->where('status', 'hadir')->count();

            // Tugas yang belum dikumpulkan dihitung 0
            $rataTugas = self::rata($kumpul->whereIn('tugas_id', $idTugas)->sum('nilai'), $idTugas->count());
            $rataPraktikum = self::rata($kumpul->whereIn('tugas_id', $idPraktikum)->sum('nilai'), $idPraktikum->count());
            $rataKuis = self::rata($kuis->sum('skor'), $idKuis->count());
            $kehadiran = $hadir > 0 ? 100.0 : 0.0;

            $akhir = (
                $rataTugas * $bobot['tugas']
                + $rataPraktikum * $bobot['praktikum']
                + $rataKuis * $bobot['kuis']
                + $kehadiran * $bobot['kehadiran']
            ) / 100;

            $rekap[] = [
                'mahasiswa' => $mhs,
                'tugas' => $rataTugas,
                'praktikum' => $rataPraktikum,
                'kuis' => $rataKuis,
                'kehadiran' => $kehadiran,
                'akhir' => round($akhir, 2),
                'huruf' => self::huruf($akhir),
                'dikumpulkan' => $kumpul->count(),
                'total_tugas' => $tugas->count(),
                'kuis_dikerjakan' => $kuis->count(),
                'total_kuis' => $idKuis->count(),
                'hadir' => $hadir,
                'total_sesi' => $idSesi->count(),
            ];
        }

        return $rekap;
    }

    private static function rata($jumlah, $banyak): float
    {
        return $banyak > 0 ? round($jumlah / $banyak, 2) : 0;
    }

    public static function huruf(float $nilai): string
    {
        return match (true) {
            $nilai >= 85 => 'A',
            $nilai >= 75 => 'B',
            $nilai >= 65 => 'C',
            $nilai >= 50 => 'D',
            default => 'E',
        };
    }
}
