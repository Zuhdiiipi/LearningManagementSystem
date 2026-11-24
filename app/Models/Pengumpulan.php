<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Pengumpulan extends Model
{
    protected $table = 'pengumpulan';
    protected $guarded = ['id'];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    protected $casts = ['waktu_kumpul' => 'datetime'];

    // Waktu mahasiswa mengunggah jawaban (tidak berubah saat dosen memberi nilai)
    public function waktuKumpul()
    {
        return $this->waktu_kumpul ?? $this->created_at;
    }

    public function terlambat(): bool
    {
        return $this->waktuKumpul()->greaterThan($this->tugas->deadline);
    }

    // Contoh: "2 jam 15 menit"
    public function lamaTerlambat(): string
    {
        $menit = (int) $this->tugas->deadline->diffInMinutes($this->waktuKumpul());
        $hari = intdiv($menit, 1440); $jam = intdiv($menit % 1440, 60); $sisa = $menit % 60;
        $bagian = array_filter([$hari ? $hari . ' hari' : null, $jam ? $jam . ' jam' : null, $sisa ? $sisa . ' menit' : null]);

        return $bagian ? implode(' ', $bagian) : 'kurang dari 1 menit';
    }
}
