<?php

namespace App\Models;

use App\Models\Concerns\MemilikiLampiran;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use MemilikiLampiran;

    protected $table = 'materi';

    protected $guarded = ['id'];

    protected $casts = [
        'boleh_unduh' => 'boolean',
        'lampiran' => 'array',
    ];

    private const MIME_VIDEO = [
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class);
    }


    public function bisaDilihat(?string $namaFile = null): bool
    {
        $ekstensi = strtolower(pathinfo($namaFile ?? $this->file_name ?? '', PATHINFO_EXTENSION));

        return isset(self::MIME_VIDEO[$ekstensi])
            || in_array($ekstensi, ['pdf', 'png', 'jpg', 'jpeg', 'txt'], true);
    }

    public function mimeVideo(string $namaFile): ?string
    {
        $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

        return self::MIME_VIDEO[$ekstensi] ?? null;
    }
}
