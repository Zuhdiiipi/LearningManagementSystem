<?php

namespace App\Support;

use App\Models\LogNotifikasi;
use App\Notifications\PemberitahuanLms;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Notifikasi
{
    // Kirim email ke satu atau banyak pengguna, lalu catat hasilnya di tabel log_notifikasi.
    // Jika pengaturan email (SMTP) bermasalah, aplikasi tetap berjalan dan status dicatat "Gagal".
    public static function kirim($penerima, string $judul, string $pesan, string $url): void
    {
        $jenis = match (true) {
            Str::startsWith($judul, 'Materi') => 'Materi',
            Str::startsWith($judul, ['Tugas', 'Praktikum']) => 'Tugas',
            Str::startsWith($judul, 'Pengingat') => 'Pengingat',
            Str::startsWith($judul, 'Nilai') => 'Nilai',
            Str::startsWith($judul, 'Absensi') => 'Absensi',
            default => 'Quiz',
        };

        foreach (collect($penerima instanceof \Illuminate\Database\Eloquent\Model ? [$penerima] : $penerima) as $user) {
            try {
                $user->notify(new PemberitahuanLms($judul, $pesan, $url, $jenis));
                $berhasil = true;
            } catch (\Throwable $e) {
                Log::warning('Gagal mengirim email notifikasi ke ' . $user->email . ': ' . $e->getMessage());
                $berhasil = false;
            }

            LogNotifikasi::create([
                'penerima_email' => $user->email,
                'subjek' => $judul,
                'jenis_notif' => $jenis,
                'status_pengiriman' => $berhasil,
                'waktu_kirim' => now(),
            ]);
        }
    }
}
