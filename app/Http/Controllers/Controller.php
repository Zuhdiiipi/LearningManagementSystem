<?php

namespace App\Http\Controllers;

use App\Models\Kelas;

abstract class Controller
{
    // Hanya dosen pengampu yang boleh mengelola kelas ini
    protected function pastikanPengampu(Kelas $kelas): void
    {
        abort_unless($kelas->dosen_id === auth()->id(), 403, 'Anda bukan pengampu kelas ini.');
    }

    // Hanya mahasiswa anggota kelas yang boleh membuka kelas ini
    protected function pastikanAnggota(Kelas $kelas): void
    {
        abort_unless(
            $kelas->mahasiswa()->where('users.id', auth()->id())->exists(),
            403,
            'Anda belum bergabung ke kelas ini.'
        );
    }
}
