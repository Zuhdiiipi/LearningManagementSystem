<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $guarded = ['id'];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function sesiAbsensi()
    {
        return $this->belongsTo(SesiAbsensi::class);
    }
}
