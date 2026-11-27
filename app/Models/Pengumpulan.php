<?php

namespace App\Models;

use App\Models\Concerns\MemilikiLampiran;
use Illuminate\Database\Eloquent\Model;

class Pengumpulan extends Model
{
    use MemilikiLampiran;

    protected $table = 'pengumpulan';

    protected $guarded = ['id'];

    protected $casts = [
        'waktu_kumpul' => 'datetime',
        'lampiran' => 'array',
    ];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function waktuKumpul()
    {
        return $this->waktu_kumpul ?? $this->created_at;
    }

    public function terlambat(): bool
    {
        return $this->waktuKumpul()->greaterThan($this->tugas->deadline);
    }

    public function lamaTerlambat(): string
    {
        $menit = (int) $this->tugas->deadline->diffInMinutes($this->waktuKumpul());

        $hari = intdiv($menit, 1440);
        $jam = intdiv($menit % 1440, 60);
        $sisaMenit = $menit % 60;

        $bagian = array_filter([
            $hari ? $hari . ' hari' : null,
            $jam ? $jam . ' jam' : null,
            $sisaMenit ? $sisaMenit . ' menit' : null,
        ]);

        return $bagian ? implode(' ', $bagian) : 'kurang dari 1 menit';
    }
}
