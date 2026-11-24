<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Tugas extends Model
{
    protected $table = 'tugas';
    protected $guarded = ['id'];
    protected $casts = ['deadline' => 'datetime', 'pengingat_terkirim' => 'boolean'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class);
    }

    public function pengumpulan()
    {
        return $this->hasMany(Pengumpulan::class);
    }

    public function sudahLewat(): bool
    {
        return now()->greaterThan($this->deadline);
    }
}
