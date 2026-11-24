<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\HasilKuis;
use App\Models\SesiAbsensi;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\LogAktivitas;
use App\Models\MataKuliah;
use App\Models\Pengumpulan;
use App\Models\ProgramStudi;
use App\Models\TahunAjaran;
use App\Support\RekapNilai;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    // Daftar kelas ditampilkan di dashboard ("Kelas Saya")
    public function index()
    {
        return redirect()->route('dosen.dashboard');
    }

    public function create()
    {
        return view('dosen.kelas.form', $this->dataForm(new Kelas(['semester' => 'ganjil', 'status' => 'aktif'])));
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $data['dosen_id'] = auth()->id();
        $data['kode_kelas'] = Kelas::buatKode();

        $kelas = Kelas::create($data);
        LogAktivitas::catat('Membuat kelas ' . $kelas->nama);

        return redirect()->route('dosen.kelas.show', $kelas)
            ->with('sukses', 'Kelas dibuat. Bagikan kode ' . $kelas->kode_kelas . ' kepada mahasiswa.');
    }

    public function edit(Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);

        return view('dosen.kelas.form', $this->dataForm($kelas));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);
        $kelas->update($this->validasi($request));

        return redirect()->route('dosen.dashboard')->with('sukses', 'Kelas diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);
        $kelas->delete();
        LogAktivitas::catat('Menghapus kelas ' . $kelas->nama);

        return redirect()->route('dosen.dashboard')->with('sukses', 'Kelas dihapus.');
    }

    // Halaman kelas dengan menu samping
    public function show(Request $request, Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);
        $tab = in_array($request->get('tab'), ['materi', 'tugas', 'kuis', 'absensi', 'nilai', 'progres', 'anggota']) ? $request->get('tab') : 'materi';
        $kelas->load(['mataKuliah', 'pertemuan.sesiAbsensi']);
        $tugas = $kelas->tugas()->withCount('pengumpulan')->get();
        $kuis = $kelas->kuis()->withCount(['soal', 'hasil'])->get();

        $data = [
            'kelas' => $kelas,
            'tab' => $tab,
            'materi' => $kelas->materi()->get(),
            'tugas' => $tugas,
            'kuis' => $kuis,
            'anggota' => $kelas->mahasiswa()->orderBy('name')->get(),
        ];

        // Rekap nilai: nilai tiap tugas, praktikum, dan quiz per mahasiswa
        if ($tab === 'nilai') {
            $data['rekap'] = collect(RekapNilai::kelas($kelas))->keyBy('mahasiswa.id');
            $data['nilaiTugas'] = Pengumpulan::whereIn('tugas_id', $tugas->pluck('id'))->get()
                ->groupBy('mahasiswa_id')->map(fn ($g) => $g->keyBy('tugas_id'));
            $data['nilaiKuis'] = HasilKuis::whereIn('kuis_id', $kuis->pluck('id'))->get()
                ->groupBy('mahasiswa_id')->map(fn ($g) => $g->keyBy('kuis_id'));
        }

        // Progres belajar mahasiswa + aktivitas terbaru di kelas (FR-25, FR-26)
        if ($tab === 'progres') {
            $data['rekap'] = RekapNilai::kelas($kelas);
            $data['aktivitas'] = $this->aktivitasKelas($kelas, $tugas, $kuis);
        }

        return view('dosen.kelas.show', $data);
    }

    // Gabungan aktivitas mahasiswa di kelas: kumpul tugas, kerjakan quiz, absensi
    private function aktivitasKelas(Kelas $kelas, $tugas, $kuis)
    {
        $daftar = collect();
        foreach (Pengumpulan::with(['mahasiswa', 'tugas'])->whereIn('tugas_id', $tugas->pluck('id'))->latest('updated_at')->take(30)->get() as $p) {
            $daftar->push(['waktu' => $p->updated_at, 'nama' => $p->mahasiswa->name, 'ikon' => 'fa-file-arrow-up',
                'teks' => 'mengumpulkan ' . $p->tugas->jenis . ' "' . $p->tugas->judul . '"' . ($p->nilai !== null ? ' (nilai ' . $p->nilai . ')' : '')]);
        }
        foreach (HasilKuis::with(['mahasiswa', 'kuis'])->whereIn('kuis_id', $kuis->pluck('id'))->latest()->take(30)->get() as $h) {
            $daftar->push(['waktu' => $h->created_at, 'nama' => $h->mahasiswa->name, 'ikon' => 'fa-circle-question',
                'teks' => 'mengerjakan "' . $h->kuis->judul . '" (skor ' . (float) $h->skor . ')']);
        }
        $idSesi = SesiAbsensi::whereIn('pertemuan_id', $kelas->pertemuan->pluck('id'))->pluck('id');
        foreach (Absensi::with(['mahasiswa', 'sesiAbsensi.pertemuan'])->whereIn('sesi_absensi_id', $idSesi)->latest('updated_at')->take(30)->get() as $a) {
            $daftar->push(['waktu' => $a->updated_at, 'nama' => $a->mahasiswa->name, 'ikon' => 'fa-calendar-check',
                'teks' => 'tercatat ' . $a->status . ' di pertemuan ' . $a->sesiAbsensi->pertemuan->nomor]);
        }

        return $daftar->sortByDesc('waktu')->take(30)->values();
    }

    // Unduh rekap nilai dalam format CSV (bisa dibuka di Excel)
    public function exportNilai(Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);
        $rekap = RekapNilai::kelas($kelas);
        $namaFile = 'rekap-nilai-' . str($kelas->mataKuliah->nama . ' ' . $kelas->nama)->slug() . '.csv';

        return response()->streamDownload(function () use ($rekap) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['NIM', 'Nama', 'Tugas', 'Praktikum', 'Quiz', 'Kehadiran (%)', 'Nilai Akhir', 'Huruf'], ';');
            foreach ($rekap as $r) {
                fputcsv($out, [$r['mahasiswa']->nim_nidn, $r['mahasiswa']->name, $r['tugas'], $r['praktikum'],
                    $r['kuis'], $r['kehadiran'], $r['akhir'], $r['huruf']], ';');
            }
            fclose($out);
        }, $namaFile, ['Content-Type' => 'text/csv']);
    }

    // Keluarkan mahasiswa dari kelas
    public function keluarkan(Kelas $kelas, int $mahasiswa)
    {
        $this->pastikanPengampu($kelas);
        $kelas->mahasiswa()->detach($mahasiswa);

        return back()->with('sukses', 'Mahasiswa dikeluarkan dari kelas.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|max:100',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'semester' => 'required|in:ganjil,genap',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'status' => 'required|in:aktif,draft',
        ], [
            'mata_kuliah_id.required' => 'Pilih mata kuliah.',
            'tahun_ajaran_id.required' => 'Pilih tahun akademik.',
        ]);
    }

    private function dataForm(Kelas $kelas): array
    {
        return [
            'kelas' => $kelas,
            'tanpaMenu' => true,
            'prodi' => ProgramStudi::orderBy('nama')->get(),
            'matkul' => MataKuliah::orderBy('nama')->get(),
            'tahun' => TahunAjaran::orderByDesc('is_active')->orderByDesc('nama')->get(),
        ];
    }
}
