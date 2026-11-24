<?php

namespace App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\Controller;
use App\Models\HasilKuis;
use App\Models\Kuis;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class KuisController extends Controller
{
    // Halaman mengerjakan kuis (hanya saat kuis dibuka dan belum pernah dikerjakan)
    public function kerjakan(Kuis $kuis)
    {
        $this->pastikanAnggota($kuis->kelas);

        if ($this->sudahMengerjakan($kuis)) {
            return redirect()->route('mahasiswa.kuis.hasil', $kuis);
        }
        if (! $kuis->sedangDibuka()) {
            return redirect()->route('mahasiswa.kelas.show', [$kuis->kelas, 'tab' => 'kuis'])
                ->with('gagal', $kuis->dibuka_pada ? 'Sesi quiz sudah ditutup.' : 'Quiz belum dibuka oleh dosen. Tunggu dosen membuka sesi quiz.');
        }

        // Catat kapan mulai mengerjakan, untuk batas durasi
        $kunci = 'kuis_mulai_' . $kuis->id;
        if (! session()->has($kunci)) {
            session([$kunci => now()->timestamp]);
        }
        $batas = min(session($kunci) + $kuis->durasi_menit * 60, $kuis->selesai->timestamp);

        return view('mahasiswa.kuis.kerjakan', [
            'kuis' => $kuis,
            'soal' => $kuis->soal()->get(),
            'sisaDetik' => max(0, $batas - now()->timestamp),
        ]);
    }

    // Penilaian otomatis: jawaban dicocokkan dengan kunci jawaban
    public function submit(Request $request, Kuis $kuis)
    {
        $this->pastikanAnggota($kuis->kelas);
        if ($this->sudahMengerjakan($kuis)) {
            return redirect()->route('mahasiswa.kuis.hasil', $kuis);
        }
        // Jawaban masih diterima sampai 1 menit setelah batas (untuk kirim otomatis saat waktu habis)
        if (! $kuis->dibuka_pada || now()->gt($kuis->selesai->copy()->addMinute())) {
            return redirect()->route('mahasiswa.kelas.show', [$kuis->kelas, 'tab' => 'kuis'])
                ->with('gagal', 'Sesi quiz sudah ditutup, jawaban tidak tersimpan.');
        }

        $jawaban = $request->input('jawaban', []);
        $soal = $kuis->soal()->get();
        $benar = $soal->filter(fn ($s) => ($jawaban[$s->id] ?? null) === $s->jawaban)->count();
        $jumlah = max($soal->count(), 1);

        HasilKuis::create([
            'kuis_id' => $kuis->id,
            'mahasiswa_id' => auth()->id(),
            'jawaban' => $jawaban,
            'benar' => $benar,
            'jumlah_soal' => $soal->count(),
            'skor' => round($benar / $jumlah * 100, 2),
        ]);
        session()->forget('kuis_mulai_' . $kuis->id);
        LogAktivitas::catat('Mengerjakan kuis ' . $kuis->judul);

        return redirect()->route('mahasiswa.kuis.hasil', $kuis)->with('sukses', 'Kuis selesai dan langsung dinilai.');
    }

    public function hasil(Kuis $kuis)
    {
        $this->pastikanAnggota($kuis->kelas);
        $hasil = HasilKuis::where('kuis_id', $kuis->id)->where('mahasiswa_id', auth()->id())->firstOrFail();

        return view('mahasiswa.kuis.hasil', [
            'kuis' => $kuis,
            'hasil' => $hasil,
            // Hasil per soal langsung tampil setelah selesai mengerjakan.
            // Kunci jawaban baru ditampilkan setelah sesi ditutup (agar tidak bocor ke teman).
            'soal' => $kuis->soal()->get(),
            'tampilKunci' => ! $kuis->sedangDibuka(),
        ]);
    }

    private function sudahMengerjakan(Kuis $kuis): bool
    {
        return HasilKuis::where('kuis_id', $kuis->id)->where('mahasiswa_id', auth()->id())->exists();
    }
}
