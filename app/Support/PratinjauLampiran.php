<?php

namespace App\Support;

use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PratinjauLampiran
{
    private const MIME = [
        'pdf' => 'application/pdf',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'txt' => 'text/plain',
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
    ];

    public static function mime(string $nama): ?string
    {
        $ekstensi = strtolower(pathinfo($nama, PATHINFO_EXTENSION));

        return self::MIME[$ekstensi] ?? null;
    }

    public static function mimeVideo(string $nama): ?string
    {
        $mime = self::mime($nama);

        return str_starts_with($mime ?? '', 'video/') ? $mime : null;
    }

    public static function excel(string $nama): bool
    {
        return strtolower(pathinfo($nama, PATHINFO_EXTENSION)) === 'xlsx';
    }

    public static function untukTugas(Request $request, Tugas $tugas): ?array
    {
        if (! $request->boolean('lihat')) {
            return null;
        }

        abort_unless($tugas->file_path && Storage::exists($tugas->file_path), 404);

        $nama = $tugas->file_name ?: basename($tugas->file_path);
        $xlsx = self::excel($nama);

        abort_unless($xlsx || self::mime($nama) !== null, 404);

        return [
            'xlsx' => $xlsx,
            'excel' => $xlsx
                ? PratinjauExcel::dariPermintaan($request, $tugas->file_path)
                : null,
        ];
    }
}
