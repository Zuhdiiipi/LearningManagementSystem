<?php

namespace App\Http\Controllers;

use App\Models\HasilKuis;
use App\Models\Materi;
use App\Models\Pengumpulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        if ($user->role === 'dosen') {
            $idKelas = $user->kelasDiampu()->pluck('id');

            $statistik = [
                'Kelas' => $idKelas->count(),
                'Mata Kuliah' => $user->kelasDiampu()->distinct()->count('mata_kuliah_id'),
                'Materi' => Materi::whereIn('kelas_id', $idKelas)->count(),
                'MHS Bimbingan' => DB::table('kelas_mahasiswa')
                    ->whereIn('kelas_id', $idKelas)
                    ->distinct()
                    ->count('mahasiswa_id'),
            ];
        } else {
            $kumpul = Pengumpulan::with('tugas')->where('mahasiswa_id', $user->id)->get();

            $statistik = [
                'Kelas Diikuti' => $user->kelasDiikuti()->count(),
                'Tugas Selesai' => $kumpul->filter(fn($k) => $k->tugas?->jenis === 'tugas')->count(),
                'Quiz Selesai' => HasilKuis::where('mahasiswa_id', $user->id)->count(),
                'Praktikum' => $kumpul->filter(fn($k) => $k->tugas?->jenis === 'praktikum')->count(),
            ];
        }

        return view('profil.show', compact('user', 'statistik'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $user->update($request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'fakultas' => 'nullable|max:100',
            'program_studi' => 'nullable|max:100',
            'angkatan' => 'nullable|digits:4',
            'telepon' => 'nullable|max:20',
        ], [
            'email.unique' => 'Email sudah dipakai akun lain.',
            'angkatan.digits' => 'Angkatan diisi tahun, contoh 2023.',
        ]));

        return back()->with('sukses', 'Data diri berhasil diperbarui.');
    }

    public function foto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|max:2048',
        ], [
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
            'foto.image' => 'File harus berupa gambar.',
        ]);

        $user = auth()->user();

        if ($user->foto) {
            Storage::delete($user->foto);
        }

        $user->update(['foto' => $request->file('foto')->store('foto')]);

        return back()->with('sukses', 'Foto profil diperbarui.');
    }
}
