<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\Tugas;
use App\Support\Lampiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Support\PratinjauLampiran;

class TugasController extends Controller
{
    public function show(Request $request, Tugas $tugas)
    {
        $this->pastikanAnggota($tugas->kelas);

        return response()->view('mahasiswa.tugas.show', [
            'tugas' => $tugas,
            'kelas' => $tugas->kelas,
            'kumpul' => $tugas->pengumpulan()
                ->where('mahasiswa_id', auth()->id())
                ->first(),
            'pratinjau' => PratinjauLampiran::untukTugas($request, $tugas),
        ], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function kumpul(Request $request, Tugas $tugas)
    {
        $this->pastikanAnggota($tugas->kelas);

        $aturanFile = 'file|max:20480|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,txt,png,jpg,jpeg';

        $data = $request->validate([
            'files' => 'required_without:file|nullable|array',
            'files.*' => 'required|' . $aturanFile,
            'file' => 'nullable|' . $aturanFile,
            'jumlah_file' => 'nullable|integer|min:0',
            'catatan' => 'nullable|max:1000',
        ], [
            'files.required_without' => 'Pilih minimal satu file jawaban.',
            'files.*.max' => 'Ukuran setiap file maksimal 20 MB.',
            'files.*.mimes' => 'Ada format file yang tidak didukung.',
            'file.max' => 'Ukuran file maksimal 20 MB.',
            'file.mimes' => 'Format file tidak didukung.',
        ]);

        $files = Lampiran::dariInput($data);

        [$kumpul, $lampiranLama] = Lampiran::simpan(
            $files,
            'pengumpulan',
            function (array $atribut) use ($tugas, $data) {
                return DB::transaction(function () use ($tugas, $data, $atribut) {
                    // Kunci tugas agar pengiriman bersamaan tidak membuat dua baris.
                    $tugasTerkunci = Tugas::query()->lockForUpdate()->findOrFail($tugas->id);

                    $kumpul = $tugasTerkunci->pengumpulan()
                        ->where('mahasiswa_id', auth()->id())
                        ->lockForUpdate()
                        ->first();

                    if ($kumpul?->nilai !== null) {
                        throw ValidationException::withMessages([
                            'files' => 'Tugas sudah dinilai, jawaban tidak bisa diganti.',
                        ]);
                    }

                    $lampiranLama = $kumpul?->daftarLampiran() ?? [];

                    $kumpul ??= $tugasTerkunci->pengumpulan()->make([
                        'mahasiswa_id' => auth()->id(),
                    ]);

                    $kumpul->fill($atribut + [
                        'catatan' => $data['catatan'] ?? null,
                        'waktu_kumpul' => now(),
                    ])->save();

                    return [$kumpul, $lampiranLama];
                });
            }
        );

        Lampiran::hapus($lampiranLama);

        $kumpul->setRelation('tugas', $tugas);

        if ($kumpul->terlambat()) {
            $terlambat = $kumpul->lamaTerlambat();

            LogAktivitas::catat(
                'Mengumpulkan ' . $tugas->jenis . ' ' . $tugas->judul
                    . ' (terlambat ' . $terlambat . ')'
            );

            return back()->with(
                'sukses',
                'Jawaban berhasil dikumpulkan, tetapi TERLAMBAT ' . $terlambat . ' dari batas waktu.'
            );
        }

        LogAktivitas::catat('Mengumpulkan ' . $tugas->jenis . ' ' . $tugas->judul);

        return back()->with('sukses', 'Jawaban berhasil dikumpulkan tepat waktu.');
    }

    public function batal(Tugas $tugas)
    {
        $this->pastikanAnggota($tugas->kelas);

        $lampiran = DB::transaction(function () use ($tugas) {
            $tugasTerkunci = Tugas::query()->lockForUpdate()->findOrFail($tugas->id);

            $kumpul = $tugasTerkunci->pengumpulan()
                ->where('mahasiswa_id', auth()->id())
                ->lockForUpdate()
                ->firstOrFail();

            if ($kumpul->nilai !== null) {
                return null;
            }

            $lampiran = $kumpul->daftarLampiran();
            $kumpul->delete();

            return $lampiran;
        });

        if ($lampiran === null) {
            return back()->with('gagal', 'Tugas sudah dinilai, pengiriman tidak bisa dibatalkan.');
        }

        Lampiran::hapus($lampiran);

        LogAktivitas::catat('Membatalkan pengumpulan ' . $tugas->jenis . ' ' . $tugas->judul);

        return back()->with('sukses', 'Pengiriman dibatalkan. Kamu bisa mengunggah file lagi.');
    }
}
