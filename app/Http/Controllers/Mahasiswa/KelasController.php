<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\HasilKuis;
use App\Models\Kelas;
use App\Models\LogAktivitas;
use App\Models\Materi;
use App\Models\Pengumpulan;
use App\Support\PratinjauExcel;
use App\Support\RekapNilai;
use Illuminate\Http\Request;
use Throwable;

class KelasController extends Controller
{
    // Daftar kelas sudah ada di dashboard ("Kelas Saya")
    public function index()
    {
        return redirect()->route('mahasiswa.dashboard');
    }

    // Bergabung ke kelas memakai kode dari dosen
    public function gabung(Request $request)
    {
        $kode = strtoupper(trim($request->validate(['kode_kelas' => 'required|string'])['kode_kelas']));
        $kelas = Kelas::where('kode_kelas', $kode)->where('status', 'aktif')->first();

        if (! $kelas) {
            return back()->withErrors(['kode_kelas' => 'Kode kelas tidak ditemukan.'])->withInput();
        }

        $kelas->mahasiswa()->syncWithoutDetaching([auth()->id()]);
        LogAktivitas::catat('Bergabung ke kelas ' . $kelas->nama);

        return redirect()->route('mahasiswa.kelas.show', $kelas)->with('sukses', 'Berhasil bergabung ke ' . $kelas->mataKuliah->nama . '.');
    }

    // Halaman kelas dengan menu samping: Materi, Tugas, Quiz, Absensi, Nilai
    public function show(Request $request, Kelas $kelas)
    {
        $this->pastikanAnggota($kelas);
        $saya = auth()->id();
        $tab = in_array($request->get('tab'), ['materi', 'tugas', 'kuis', 'absensi', 'nilai']) ? $request->get('tab') : 'materi';
        $kelas->load(['mataKuliah', 'dosen', 'pertemuan.sesiAbsensi']);

        $tugas = $kelas->tugas()->get();
        $kuis = $kelas->kuis()->withCount('soal')->get();

        return view('mahasiswa.kelas.show', [
            'kelas' => $kelas,
            'tab' => $tab,
            'materi' => $kelas->materi()->get(),
            'tugas' => $tugas,
            'kuis' => $kuis,
            'pengumpulan' => Pengumpulan::where('mahasiswa_id', $saya)->whereIn('tugas_id', $tugas->pluck('id'))->get()->keyBy('tugas_id'),
            'hasilKuis' => HasilKuis::where('mahasiswa_id', $saya)->whereIn('kuis_id', $kuis->pluck('id'))->get()->keyBy('kuis_id'),
            'absensiSaya' => Absensi::where('mahasiswa_id', $saya)->get()->keyBy('sesi_absensi_id'),
            'rekap' => $tab === 'nilai' ? collect(RekapNilai::kelas($kelas))->firstWhere('mahasiswa.id', $saya) : null,
        ]);
    }

    // Membuka satu materi
    public function materi(Request $request, Materi $materi)
    {
        $kelas = $materi->kelas;
        $this->pastikanAnggota($kelas);

        $pratinjau = null;
        $excel = null;

        if ($materi->tipe === 'file' && $request->query('lihat') !== null) {
            $indeks = filter_var($request->query('lihat'), FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 0],
            ]);

            $file = $materi->daftarLampiran()[$indeks] ?? null;
            abort_if($indeks === false || $file === null, 404);

            $xlsx = strtolower(pathinfo($file['nama'], PATHINFO_EXTENSION)) === 'xlsx';
            abort_unless($xlsx || $materi->bisaDilihat($file['nama']), 404);

            $pratinjau = [
                'indeks' => $indeks,
                'nama' => $file['nama'],
                'xlsx' => $xlsx,
            ];

            if ($xlsx) {
                $lembar = filter_var($request->query('lembar'), FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 0],
                ]);

                $halaman = filter_var($request->query('halaman'), FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 1, 'max_range' => 20972],
                ]) ?: 1;

                $kolom = filter_var($request->query('kolom'), FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 1, 'max_range' => 631],
                ]) ?: 1;

                try {
                    $excel = PratinjauExcel::baca(
                        $file['path'],
                        $lembar === false ? null : $lembar,
                        $halaman,
                        $kolom
                    );
                } catch (Throwable $error) {
                    report($error);
                }
            }
        }

        return response()->view(
            'mahasiswa.materi.show',
            compact('materi', 'kelas', 'pratinjau', 'excel'),
            200,
            ['Cache-Control' => 'private, no-store']
        );
    }
}
