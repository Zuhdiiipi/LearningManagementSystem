<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\Pengumpulan;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    public function show(Tugas $tugas)
    {
        $this->pastikanAnggota($tugas->kelas);

        return view('mahasiswa.tugas.show', [
            'tugas' => $tugas,
            'kelas' => $tugas->kelas,
            'kumpul' => $tugas->pengumpulan()->where('mahasiswa_id', auth()->id())->first(),
        ]);
    }

    // Mengunggah jawaban. Lewat deadline tetap boleh, tetapi dicatat TERLAMBAT beserta lamanya.
    public function kumpul(Request $request, Tugas $tugas)
    {
        $this->pastikanAnggota($tugas->kelas);

        $data = $request->validate([
            'file' => 'required|file|max:20480|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,txt,png,jpg,jpeg',
            'catatan' => 'nullable|max:1000',
        ], ['file.max' => 'Ukuran file maksimal 20 MB.', 'file.required' => 'Pilih file jawaban dulu.', 'file.mimes' => 'Format file tidak didukung.']);

        $lama = Pengumpulan::where('tugas_id', $tugas->id)->where('mahasiswa_id', auth()->id())->first();
        if ($lama?->nilai !== null) {
            return back()->with('gagal', 'Tugas sudah dinilai, jawaban tidak bisa diganti.');
        }
        if ($lama) {
            Storage::delete($lama->file_path);
        }

        $kumpul = Pengumpulan::updateOrCreate(
            ['tugas_id' => $tugas->id, 'mahasiswa_id' => auth()->id()],
            [
                'file_path' => $request->file('file')->store('pengumpulan'),
                'file_name' => $request->file('file')->getClientOriginalName(),
                'catatan' => $data['catatan'] ?? null,
                'waktu_kumpul' => now(),
            ]
        );
        $kumpul->setRelation('tugas', $tugas);

        if ($kumpul->terlambat()) {
            LogAktivitas::catat('Mengumpulkan ' . $tugas->jenis . ' ' . $tugas->judul . ' (terlambat ' . $kumpul->lamaTerlambat() . ')');

            return back()->with('sukses', 'Jawaban berhasil dikumpulkan, tetapi TERLAMBAT ' . $kumpul->lamaTerlambat() . ' dari batas waktu.');
        }
        LogAktivitas::catat('Mengumpulkan ' . $tugas->jenis . ' ' . $tugas->judul);

        return back()->with('sukses', 'Jawaban berhasil dikumpulkan tepat waktu.');
    }

    // Tombol "Batal Kirim": menarik kembali jawaban selama belum dinilai dosen
    public function batal(Tugas $tugas)
    {
        $this->pastikanAnggota($tugas->kelas);
        $kumpul = Pengumpulan::where('tugas_id', $tugas->id)->where('mahasiswa_id', auth()->id())->firstOrFail();

        if ($kumpul->nilai !== null) {
            return back()->with('gagal', 'Tugas sudah dinilai, pengiriman tidak bisa dibatalkan.');
        }
        Storage::delete($kumpul->file_path);
        $kumpul->delete();
        LogAktivitas::catat('Membatalkan pengumpulan ' . $tugas->judul);

        return back()->with('sukses', 'Pengiriman dibatalkan. Kamu bisa mengunggah file lagi.');
    }
}
