<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\LogAktivitas;
use App\Models\Pertemuan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function create(Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);

        return view('dosen.absensi.form', [
            'kelas' => $kelas,
            'menuAktif' => 'absensi',
            'nomorBerikut' => $kelas->pertemuan()->max('nomor') + 1,
        ]);
    }

    // Tambah Absensi: membuat pertemuan + jadwal absensi (tanggal, waktu buka, waktu tutup)
    public function store(Request $request, Kelas $kelas)
    {
        $this->pastikanPengampu($kelas);
        $data = $this->validasi($request);

        if ($kelas->pertemuan()->where('nomor', $data['nomor'])->exists()) {
            return back()->withInput()->withErrors(['nomor' => 'Pertemuan ke-' . $data['nomor'] . ' sudah ada.']);
        }

        $pertemuan = $kelas->pertemuan()->create([
            'nomor' => $data['nomor'],
            'judul' => $data['judul'] ?? null,
            'tanggal' => $data['tanggal'],
        ]);
        $pertemuan->sesiAbsensi()->create([
            'dibuka_pada' => Carbon::parse($data['tanggal'] . ' ' . $data['waktu_buka']),
            'ditutup_pada' => Carbon::parse($data['tanggal'] . ' ' . $data['waktu_tutup']),
        ]);
        LogAktivitas::catat('Membuat absensi pertemuan ' . $data['nomor'] . ' kelas ' . $kelas->nama);

        return redirect()->route('dosen.kelas.show', [$kelas, 'tab' => 'absensi'])->with('sukses', 'Absensi pertemuan ' . $data['nomor'] . ' dibuat.');
    }

    // Ubah pertemuan: nomor, topik, tanggal, waktu buka dan tutup
    public function edit(Pertemuan $pertemuan)
    {
        $this->pastikanPengampu($pertemuan->kelas);

        return view('dosen.absensi.form', [
            'kelas' => $pertemuan->kelas,
            'pertemuan' => $pertemuan,
            'menuAktif' => 'absensi',
            'nomorBerikut' => $pertemuan->nomor,
        ]);
    }

    public function update(Request $request, Pertemuan $pertemuan)
    {
        $kelas = $pertemuan->kelas;
        $this->pastikanPengampu($kelas);
        $data = $this->validasi($request);

        if ($kelas->pertemuan()->where('nomor', $data['nomor'])->where('id', '!=', $pertemuan->id)->exists()) {
            return back()->withInput()->withErrors(['nomor' => 'Pertemuan ke-' . $data['nomor'] . ' sudah ada.']);
        }

        $pertemuan->update(['nomor' => $data['nomor'], 'judul' => $data['judul'] ?? null, 'tanggal' => $data['tanggal']]);
        $pertemuan->sesiAbsensi()->updateOrCreate([], [
            'dibuka_pada' => Carbon::parse($data['tanggal'] . ' ' . $data['waktu_buka']),
            'ditutup_pada' => Carbon::parse($data['tanggal'] . ' ' . $data['waktu_tutup']),
        ]);
        LogAktivitas::catat('Mengubah pertemuan ' . $data['nomor'] . ' kelas ' . $kelas->nama);

        return redirect()->route('dosen.kelas.show', [$kelas, 'tab' => 'absensi'])->with('sukses', 'Pertemuan ' . $data['nomor'] . ' diperbarui.');
    }

    // Urutkan pertemuan: tukar nomor dengan pertemuan sebelum (naik) atau sesudahnya (turun)
    public function urut(Pertemuan $pertemuan, string $arah)
    {
        $kelas = $pertemuan->kelas;
        $this->pastikanPengampu($kelas);
        abort_unless(in_array($arah, ['naik', 'turun']), 404);

        $tetangga = $arah === 'naik'
            ? $kelas->pertemuan()->where('nomor', '<', $pertemuan->nomor)->reorder('nomor', 'desc')->first()
            : $kelas->pertemuan()->where('nomor', '>', $pertemuan->nomor)->reorder('nomor', 'asc')->first();

        if (! $tetangga) {
            return back()->with('gagal', 'Pertemuan ini sudah berada di urutan ' . ($arah === 'naik' ? 'paling atas.' : 'paling bawah.'));
        }

        [$a, $b] = [$pertemuan->nomor, $tetangga->nomor];
        $pertemuan->update(['nomor' => $b]);
        $tetangga->update(['nomor' => $a]);

        return redirect()->route('dosen.kelas.show', [$kelas, 'tab' => 'absensi'])
            ->with('sukses', 'Urutan diperbarui: "' . ($pertemuan->judul ?: 'pertemuan') . '" sekarang Pertemuan ' . $b . '.');
    }

    // Halaman Kehadiran: siapa yang hadir; dosen bisa mengubah status (izin/sakit/alpa)
    public function show(Pertemuan $pertemuan)
    {
        $kelas = $pertemuan->kelas;
        $this->pastikanPengampu($kelas);
        $sesi = $pertemuan->sesiAbsensi;

        return view('dosen.absensi.show', [
            'pertemuan' => $pertemuan,
            'kelas' => $kelas,
            'menuAktif' => 'absensi',
            'sesi' => $sesi,
            'mahasiswa' => $kelas->mahasiswa()->orderBy('name')->get(),
            'absensi' => $sesi ? $sesi->absensi->keyBy('mahasiswa_id') : collect(),
        ]);
    }

    public function simpan(Request $request, Pertemuan $pertemuan)
    {
        $kelas = $pertemuan->kelas;
        $this->pastikanPengampu($kelas);
        $sesi = $pertemuan->sesiAbsensi()->firstOrCreate([], ['dibuka_pada' => now(), 'ditutup_pada' => now()]);

        $status = $request->validate(['status' => 'array', 'status.*' => 'nullable|in:hadir,izin,sakit,alpa'])['status'] ?? [];
        $anggota = $kelas->mahasiswa()->pluck('users.id')->all();

        foreach ($status as $idMahasiswa => $nilai) {
            if ($nilai && in_array((int) $idMahasiswa, $anggota)) {
                Absensi::updateOrCreate(
                    ['sesi_absensi_id' => $sesi->id, 'mahasiswa_id' => $idMahasiswa],
                    ['status' => $nilai]
                );
            }
        }

        return back()->with('sukses', 'Kehadiran disimpan.');
    }

    // Buka sekarang (90 menit) / tutup sekarang, di luar jadwal
    public function buka(Pertemuan $pertemuan)
    {
        $this->pastikanPengampu($pertemuan->kelas);
        $pertemuan->sesiAbsensi()->updateOrCreate([], ['dibuka_pada' => now(), 'ditutup_pada' => now()->addMinutes(90)]);

        return back()->with('sukses', 'Absensi dibuka sekarang sampai ' . now()->addMinutes(90)->format('H:i') . '.');
    }

    public function tutup(Pertemuan $pertemuan)
    {
        $this->pastikanPengampu($pertemuan->kelas);
        $pertemuan->sesiAbsensi?->update(['ditutup_pada' => now()]);

        return back()->with('sukses', 'Absensi ditutup.');
    }

    public function destroy(Pertemuan $pertemuan)
    {
        $kelas = $pertemuan->kelas;
        $this->pastikanPengampu($kelas);
        $pertemuan->delete();

        return redirect()->route('dosen.kelas.show', [$kelas, 'tab' => 'absensi'])->with('sukses', 'Absensi pertemuan ' . $pertemuan->nomor . ' dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nomor' => 'required|integer|min:1|max:50',
            'tanggal' => 'required|date',
            'waktu_buka' => 'required|date_format:H:i',
            'waktu_tutup' => 'required|date_format:H:i|after:waktu_buka',
            'judul' => 'nullable|max:150',
        ], [
            'waktu_tutup.after' => 'Waktu tutup harus setelah waktu buka.',
        ]);
    }
}
