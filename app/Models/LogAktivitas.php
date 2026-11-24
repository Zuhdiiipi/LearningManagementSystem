<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Catat aktivitas pengguna yang sedang login
    public static function catat(string $aksi): void
    {
        self::create(['user_id' => auth()->id(), 'aksi' => $aksi]);
    }
}
