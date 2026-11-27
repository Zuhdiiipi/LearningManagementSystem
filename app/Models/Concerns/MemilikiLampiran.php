<?php

namespace App\Models\Concerns;

trait MemilikiLampiran
{
    public function daftarLampiran(): array
    {
        if ($this->lampiran !== null) {
            return $this->lampiran;
        }

        if (! $this->file_path) {
            return [];
        }

        return [[
            'path' => $this->file_path,
            'nama' => $this->file_name ?: basename($this->file_path),
        ]];
    }
}
