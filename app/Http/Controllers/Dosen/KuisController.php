<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\LogAktivitas;
use App\Support\Notifikasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KuisController extends Controller
{
    public function create(Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);

        return view('dosen.kuis.form', ['kelas' => $kelas, 'kuis' => new Kuis(['durasi_menit' => 60]), 'soal' => collect(), 'menuAktif' => 'kuis', 'terkunci' => false]);
    }

    // Simpan quiz beserta semua soalnya sekaligus, lalu kirim pengumuman email
    public function store(Request $request, Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);
        [$dataKuis, $soal] = $this->validasi($request, true);

        $dataKuis['dibuka'] = false;
        $kuis = DB::transaction(function () use ($kelas, $dataKuis, $soal) {
            $kuis = $kelas->kuis()->create($dataKuis);
            $kuis->soal()->createMany($soal);

            return $kuis;
        });
        LogAktivitas::catat('Membuat quiz ' . $kuis->judul);

        Notifikasi::kirim(
            $kelas->mahasiswa,
            'Quiz baru: ' . $kuis->judul,
            'Quiz "' . $kuis->judul . '" pada ' . $kelas->mataKuliah->nama . ' dimulai '
                . $kuis->mulai->translatedFormat('l, j F Y H:i') . ' selama ' . $kuis->durasi_menit . ' menit.'
                . ' Quiz bisa dikerjakan setelah dosen membuka sesi.',
            route('mahasiswa.kelas.show', [$kelas, 'tab' => 'kuis'])
        );

        return redirect()->route('dosen.kelas.show', [$kelas, 'tab' => 'kuis'])
            ->with('sukses', 'Quiz disimpan (' . count($soal) . ' soal). Klik "Buka Sesi" agar mahasiswa bisa mengerjakan.');
    }

    public function edit(Kuis $kuis)
    {
        $this->pastikanPengampu($kuis->kelas);

        return view('dosen.kuis.form', [
            'kelas' => $kuis->kelas,
            'kuis' => $kuis,
            'soal' => $kuis->soal()->get(),
            'menuAktif' => 'kuis',
            // Soal tidak boleh diubah kalau sudah ada mahasiswa yang mengerjakan
            'terkunci' => $kuis->hasil()->exists(),
        ]);
    }

    public function update(Request $request, Kuis $kuis)
    {
        $this->pastikanPengampu($kuis->kelas);
        $terkunci = $kuis->hasil()->exists();
        [$dataKuis, $soal] = $this->validasi($request, ! $terkunci);
        if ($kuis->sedangDibuka()) {
            // Sesi sedang berjalan: jadwal tidak diubah, waktu tutup ikut durasi baru
            $dataKuis['selesai'] = $kuis->mulai->copy()->addMinutes((int) $dataKuis['durasi_menit']);
            unset($dataKuis['mulai']);
        }

        DB::transaction(function () use ($kuis, $dataKuis, $soal, $terkunci) {
            $kuis->update($dataKuis);
            if (! $terkunci) {
                $kuis->soal()->delete();
                $kuis->soal()->createMany($soal);
            }
        });

        return redirect()->route('dosen.kelas.show', [$kuis->kelas, 'tab' => 'kuis'])
            ->with('sukses', 'Quiz diperbarui.' . ($terkunci ? ' Soal tidak diubah karena sudah ada yang mengerjakan.' : ''));
    }

    public function destroy(Kuis $kuis)
    {
        $kelas = $kuis->kelas;
        $this->pastikanPengampu($kelas);
        $kuis->delete();

        return redirect()->route('dosen.kelas.show', [$kelas, 'tab' => 'kuis'])->with('sukses', 'Quiz dihapus.');
    }

    // Live Score: nilai mahasiswa yang sudah mengerjakan (halaman memuat ulang otomatis)
    public function skor(Kuis $kuis)
    {
        $kelas = $kuis->kelas;
        $this->pastikanPengampu($kelas);

        return view('dosen.kuis.skor', [
            'kuis' => $kuis,
            'kelas' => $kelas,
            'menuAktif' => 'kuis',
            'mahasiswa' => $kelas->mahasiswa()->orderBy('name')->get(),
            'hasil' => $kuis->hasil()->get()->keyBy('mahasiswa_id'),
        ]);
    }

    // Tombol "Buka Sesi": quiz mulai sekarang dan tertutup otomatis setelah durasi habis
    public function buka(Kuis $kuis)
    {
        $kelas = $kuis->kelas;
        $this->pastikanPengampu($kelas);
        if ($kuis->sedangDibuka()) {
            return back()->with('sukses', 'Sesi quiz sudah dibuka.');
        }

        $kuis->update([
            'dibuka' => true,
            'dibuka_pada' => now(),
            'mulai' => now(),
            'selesai' => now()->addMinutes($kuis->durasi_menit),
        ]);
        LogAktivitas::catat('Membuka sesi quiz ' . $kuis->judul);

        Notifikasi::kirim(
            $kelas->mahasiswa,
            'Quiz dibuka: ' . $kuis->judul,
            'Sesi quiz "' . $kuis->judul . '" pada ' . $kelas->mataKuliah->nama . ' sudah dibuka sampai pukul '
                . $kuis->selesai->format('H:i') . '. Silakan kerjakan sekarang.',
            route('mahasiswa.kelas.show', [$kelas, 'tab' => 'kuis'])
        );

        return back()->with('sukses', 'Sesi quiz dibuka sampai pukul ' . $kuis->selesai->format('H:i') . '. Mahasiswa sudah bisa mengerjakan.');
    }

    // Tombol "Tutup Sesi": mahasiswa tidak bisa mulai/melanjutkan quiz lagi
    public function tutup(Kuis $kuis)
    {
        $this->pastikanPengampu($kuis->kelas);
        $kuis->update(['dibuka' => false, 'selesai' => now()->min($kuis->selesai)]);
        LogAktivitas::catat('Menutup sesi quiz ' . $kuis->judul);

        return back()->with('sukses', 'Sesi quiz ditutup.');
    }

    // Kirim ulang pengumuman quiz ke email mahasiswa
    public function umumkan(Kuis $kuis)
    {
        $kelas = $kuis->kelas;
        $this->pastikanPengampu($kelas);

        Notifikasi::kirim(
            $kelas->mahasiswa,
            'Quiz: ' . $kuis->judul,
            'Quiz "' . $kuis->judul . '" pada ' . $kelas->mataKuliah->nama . ' dimulai '
                . $kuis->mulai->translatedFormat('l, j F Y H:i') . ' selama ' . $kuis->durasi_menit . ' menit.',
            route('mahasiswa.kelas.show', [$kelas, 'tab' => 'kuis'])
        );

        return back()->with('sukses', 'Pengumuman quiz dikirim ke email mahasiswa.');
    }

    // Waktu Mulai + Batas Waktu (menit). Quiz ditutup otomatis setelah batas waktu habis.
    private function validasi(Request $request, bool $denganSoal): array
    {
        $aturan = [
            'judul' => 'required|max:150',
            'mulai' => 'required|date',
            'durasi_menit' => 'required|integer|min:1|max:600',
        ];
        if ($denganSoal) {
            $aturan += [
                'soal' => 'required|array|min:1',
                'soal.*.pertanyaan' => 'required|max:2000',
                'soal.*.opsi_a' => 'required|max:255',
                'soal.*.opsi_b' => 'required|max:255',
                'soal.*.opsi_c' => 'nullable|max:255',
                'soal.*.opsi_d' => 'nullable|max:255',
                'soal.*.opsi_e' => 'nullable|max:255',
                'soal.*.jawaban' => 'required|in:a,b,c,d,e',
            ];
        }
        $data = $request->validate($aturan, [
            'soal.required' => 'Tambahkan minimal satu soal.',
            'soal.*.pertanyaan.required' => 'Ada soal yang pertanyaannya masih kosong.',
            'soal.*.opsi_a.required' => 'Setiap soal minimal punya pilihan pertama dan kedua.',
            'soal.*.opsi_b.required' => 'Setiap soal minimal punya pilihan pertama dan kedua.',
            'soal.*.jawaban.required' => 'Pilih jawaban benar (klik bulatan) di setiap soal.',
            'durasi_menit.required' => 'Isi batas waktu dalam menit.',
        ]);

        $mulai = Carbon::parse($data['mulai']);
        $kuis = [
            'judul' => $data['judul'],
            'mulai' => $mulai,
            'durasi_menit' => $data['durasi_menit'],
            'selesai' => $mulai->copy()->addMinutes((int) $data['durasi_menit']),
        ];

        $soal = [];
        foreach ($data['soal'] ?? [] as $s) {
            // Jawaban benar harus menunjuk ke pilihan yang terisi
            if (empty($s['opsi_' . $s['jawaban']])) {
                abort(back()->withInput()->withErrors(['soal' => 'Jawaban benar menunjuk ke pilihan yang masih kosong.']));
            }
            $soal[] = [
                'pertanyaan' => $s['pertanyaan'],
                'opsi_a' => $s['opsi_a'], 'opsi_b' => $s['opsi_b'],
                'opsi_c' => $s['opsi_c'] ?? null, 'opsi_d' => $s['opsi_d'] ?? null, 'opsi_e' => $s['opsi_e'] ?? null,
                'jawaban' => $s['jawaban'],
            ];
        }

        return [$kuis, $soal];
    }
}
