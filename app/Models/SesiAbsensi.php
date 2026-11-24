<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiAbsensi extends Model
{
    protected $table = 'sesi_absensi';
    protected $guarded = ['id'];
    protected $casts = ['dibuka_pada' => 'datetime', 'ditutup_pada' => 'datetime'];

    // Absensi terbuka bila sekarang berada di antara waktu buka dan waktu tutup
    public function sedangDibuka(): bool
    {
        return now()->between($this->dibuka_pada, $this->ditutup_pada);
    }

    public function sudahDitutup(): bool
    {
        return now()->greaterThan($this->ditutup_pada);
    }

    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}
