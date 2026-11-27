<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class Lampiran
{
    public static function dariInput(array $data): array
    {
        $files = ($data['files'] ?? []) ?: (isset($data['file']) ? [$data['file']] : []);

        if (isset($data['jumlah_file']) && (int) $data['jumlah_file'] !== count($files)) {
            throw ValidationException::withMessages([
                'files' => 'Sebagian file tidak diterima server. Periksa batas unggahan server, lalu pilih ulang file.',
            ]);
        }

        return $files;
    }

    public static function simpan(array $files, string $folder, Closure $simpanData): mixed
    {
        $lampiran = [];

        try {
            foreach ($files as $file) {
                $path = $file->store($folder);

                if ($path === false) {
                    throw new RuntimeException('File gagal disimpan.');
                }

                $lampiran[] = [
                    'path' => $path,
                    'nama' => $file->getClientOriginalName(),
                ];
            }

            return $simpanData([
                'lampiran' => $lampiran,
                'file_path' => $lampiran[0]['path'] ?? null,
                'file_name' => $lampiran[0]['nama'] ?? null,
            ]);
        } catch (Throwable $error) {
            self::hapus($lampiran);

            throw $error;
        }
    }

    public static function hapus(array $lampiran): void
    {
        if ($lampiran === []) {
            return;
        }

        try {
            if (! Storage::delete(array_column($lampiran, 'path'))) {
                report(new RuntimeException('Sebagian file lama gagal dihapus dari penyimpanan.'));
            }
        } catch (Throwable $error) {
            report($error);
        }
    }
}
