<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';
    protected $guarded = ['id'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class);
    }

    protected $casts = ['boleh_unduh' => 'boolean'];

    // File yang bisa dibuka langsung di browser (tanpa diunduh)
    public function bisaDilihat(): bool
    {
        return in_array(strtolower(pathinfo($this->file_name ?? '', PATHINFO_EXTENSION)), ['pdf', 'png', 'jpg', 'jpeg', 'mp4', 'txt']);
    }
}
