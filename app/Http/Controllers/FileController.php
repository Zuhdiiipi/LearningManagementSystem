<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Pengumpulan;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

// File disimpan di folder privat (storage/app/private), jadi hanya bisa
// diunduh lewat route ini setelah hak aksesnya dicek.
class FileController extends Controller
{
    public function materi(Materi $materi)
    {
        $this->bolehAksesKelas($materi->kelas);

        $bolehUnduh = auth()->user()->role === 'dosen' || $materi->boleh_unduh;

        // Tombol "Unduh" (?unduh=1): sistem memeriksa izin unduh dulu (FR-11)
        if (request()->boolean('unduh')) {
            abort_unless($bolehUnduh, 403, 'Dosen tidak mengizinkan materi ini diunduh.');

            return Storage::download($materi->file_path, $materi->file_name);
        }

        // Membaca / melihat materi langsung di browser
        if ($materi->bisaDilihat()) {
            return Storage::response($materi->file_path, $materi->file_name, [
                'Content-Disposition' => 'inline; filename="' . addslashes($materi->file_name) . '"',
            ]);
        }

        // File yang tidak bisa ditampilkan di browser (Word, PPT, ZIP) hanya bisa dibuka dengan diunduh
        abort_unless($bolehUnduh, 403, 'Dosen tidak mengizinkan materi ini diunduh.');

        return Storage::download($materi->file_path, $materi->file_name);
    }

    public function tugas(Tugas $tugas)
    {
        $this->bolehAksesKelas($tugas->kelas);
        abort_unless($tugas->file_path, 404);

        return Storage::download($tugas->file_path, $tugas->file_name);
    }

    public function pengumpulan(Pengumpulan $pengumpulan)
    {
        $user = auth()->user();
        $boleh = $pengumpulan->mahasiswa_id === $user->id
            || $pengumpulan->tugas->kelas->dosen_id === $user->id;
        abort_unless($boleh, 403);

        return Storage::download($pengumpulan->file_path, $pengumpulan->file_name);
    }

    // Foto profil pengguna (boleh dilihat semua pengguna yang sudah login)
    public function foto(User $user)
    {
        abort_unless($user->foto && Storage::exists($user->foto), 404);

        return Storage::response($user->foto);
    }

    private function bolehAksesKelas($kelas): void
    {
        if (auth()->user()->role === 'dosen') {
            $this->pastikanPengampu($kelas);
        } else {
            $this->pastikanAnggota($kelas);
        }
    }
}
