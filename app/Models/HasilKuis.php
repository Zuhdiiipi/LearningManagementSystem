<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class HasilKuis extends Model
{
    protected $table = 'hasil_kuis';
    protected $guarded = ['id'];
    protected $casts = ['jawaban' => 'array'];

    public function kuis()
    {
        return $this->belongsTo(Kuis::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }
}
