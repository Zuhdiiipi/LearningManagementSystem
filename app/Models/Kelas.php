<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $guarded = ['id'];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function mahasiswa()
    {
        return $this->belongsToMany(User::class, 'kelas_mahasiswa', 'kelas_id', 'mahasiswa_id')->withTimestamps();
    }

    public function pertemuan()
    {
        return $this->hasMany(Pertemuan::class)->orderBy('nomor');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class)->latest();
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class)->orderBy('deadline');
    }

    public function kuis()
    {
        return $this->hasMany(Kuis::class)->orderBy('mulai');
    }

    // Kode unik 6 karakter untuk bergabung ke kelas
    public static function buatKode(): string
    {
        do {
            $kode = strtoupper(Str::random(6));
        } while (self::where('kode_kelas', $kode)->exists());

        return $kode;
    }
}
