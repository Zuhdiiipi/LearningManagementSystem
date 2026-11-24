<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertemuan extends Model
{
    protected $table = 'pertemuan';
    protected $guarded = ['id'];
    protected $casts = ['tanggal' => 'date'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function materi()
    {
        return $this->hasMany(Materi::class);
    }

    public function sesiAbsensi()
    {
        return $this->hasOne(SesiAbsensi::class);
    }
}
