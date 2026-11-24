<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kuis extends Model
{
    protected $table = 'kuis';
    protected $guarded = ['id'];
    protected $casts = ['mulai' => 'datetime', 'selesai' => 'datetime', 'dibuka' => 'boolean', 'dibuka_pada' => 'datetime'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function soal()
    {
        return $this->hasMany(Soal::class);
    }

    public function hasil()
    {
        return $this->hasMany(HasilKuis::class);
    }

    // Mahasiswa hanya bisa mengerjakan saat dosen sudah menekan "Buka Sesi"
    // dan batas waktu (durasi) belum habis.
    public function sedangDibuka(): bool
    {
        return $this->dibuka && now()->lt($this->selesai);
    }

    // Teks status: Sedang dibuka / Belum dibuka / Sudah ditutup
    public function status(): string
    {
        if ($this->sedangDibuka()) {
            return 'Sedang dibuka';
        }

        return $this->dibuka_pada ? 'Sudah ditutup' : 'Belum dibuka';
    }
}
