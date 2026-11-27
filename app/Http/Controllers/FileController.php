<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Pengumpulan;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Support\PratinjauLampiran;
use League\Flysystem\Local\LocalFilesystemAdapter;

class FileController extends Controller
{
    public function materi(Materi $materi)
    {
        $this->bolehAksesKelas($materi->kelas);
        abort_unless($materi->tipe === 'file', 404);

        $file = $this->pilihLampiran($materi->daftarLampiran());
        $headers = ['Cache-Control' => 'private, no-store'];

        if (! request()->boolean('unduh') && $materi->bisaDilihat($file['nama'])) {
            return Storage::response($file['path'], $file['nama'], $headers);
        }

        return Storage::download($file['path'], $file['nama'], $headers);
    }

    public function tugas(Tugas $tugas)
    {
        $this->bolehAksesKelas($tugas->kelas);

        abort_unless($tugas->file_path && Storage::exists($tugas->file_path), 404);

        return $this->responsLampiran(
            $tugas->file_path,
            $tugas->file_name ?: basename($tugas->file_path),
            request()->boolean('lihat') && ! request()->boolean('unduh')
        );
    }

    private function responsLampiran(string $path, string $nama, bool $lihat)
    {
        $headers = ['Cache-Control' => 'private, no-store'];
        $mime = $lihat ? PratinjauLampiran::mime($nama) : null;

        if ($mime === null) {
            return Storage::download($path, $nama, $headers);
        }

        $headers += [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
        ];

        if (
            str_starts_with($mime, 'video/')
            && Storage::getAdapter() instanceof LocalFilesystemAdapter
        ) {
            return response()->file(Storage::path($path), $headers)
                ->setPrivate()
                ->setContentDisposition('inline', $nama);
        }

        return Storage::response($path, $nama, $headers);
    }

    public function pengumpulan(Pengumpulan $pengumpulan)
    {
        $user = auth()->user();

        $boleh = $pengumpulan->mahasiswa_id === $user->id
            || $pengumpulan->tugas->kelas->dosen_id === $user->id;

        abort_unless($boleh, 403);

        $file = $this->pilihLampiran($pengumpulan->daftarLampiran());

        return Storage::download($file['path'], $file['nama']);
    }

    public function foto(User $user)
    {
        abort_unless($user->foto && Storage::exists($user->foto), 404);

        return Storage::response($user->foto);
    }

    private function pilihLampiran(array $lampiran): array
    {
        $indeks = filter_var(
            request()->query('lampiran', 0),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 0]]
        );

        abort_if($indeks === false || ! isset($lampiran[$indeks]), 404);

        $file = $lampiran[$indeks];

        abort_unless(Storage::exists($file['path']), 404);

        return $file;
    }

    private function bolehAksesKelas($kelas): void
    {
        if (auth()->user()->role === 'dosen') {
            $this->pastikanPengampu($kelas);
        } else {
            $this->pastikanAnggota($kelas);
        }
    }
}
