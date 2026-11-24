<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Catatan setiap email notifikasi yang dikirim sistem (sesuai SDD: tabel log_notifikasi)
class LogNotifikasi extends Model
{
    protected $table = 'log_notifikasi';
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $casts = ['status_pengiriman' => 'boolean', 'waktu_kirim' => 'datetime'];
}
