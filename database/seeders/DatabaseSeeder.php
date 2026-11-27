<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Data contoh untuk mencoba semua fitur. Semua password: password
    public function run(): void
    {
        // ===== Akun =====
        User::create(['name' => 'Admin LMS', 'email' => 'admin@gmail.com', 'password' => 'password', 'role' => 'admin']);
        $dosen = User::create(['name' => 'Dr. Budi Santoso, M.Kom.', 'nim_nidn' => '0011223344', 'email' => 'dosen@gmail.com', 'password' => 'password', 'role' => 'dosen',
            'fakultas' => 'Teknik', 'program_studi' => 'Informatika', 'telepon' => '081355512345']);
        $mhs = collect([
           ['Mahasiswa Contoh', 'D0223000', 'mhs@gmail.com'],
            ['Ratri Pramudita', 'D0223321', 'ratri@gmail.com'],
            ['Ahmad Suardi', 'D0223011', 'suardi@gmail.com'], 
        ])->map(fn ($m) => User::create([
            'name' => $m[0], 'nim_nidn' => $m[1], 'email' => $m[2], 'password' => 'password', 'role' => 'mahasiswa',
            'fakultas' => 'Teknik', 'program_studi' => 'Informatika', 'angkatan' => '2023', 'telepon' => '081234567890',
        ]));

        // ===== Data master =====
        $prodi = ProgramStudi::create(['kode' => 'IF', 'nama' => 'Informatika']);
        ProgramStudi::create(['kode' => 'SI', 'nama' => 'Sistem Informasi']);
        $ppl = MataKuliah::create(['program_studi_id' => $prodi->id, 'kode' => 'IF501', 'nama' => 'Proyek Perangkat Lunak', 'sks' => 3]);
        MataKuliah::create(['program_studi_id' => $prodi->id, 'kode' => 'IF302', 'nama' => 'Basis Data', 'sks' => 3]);
        MataKuliah::create(['program_studi_id' => $prodi->id, 'kode' => 'IF304', 'nama' => 'Pemrograman Web', 'sks' => 3]);
        $tahun = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        TahunAjaran::create(['nama' => '2025/2026']);

        // ===== Kelas contoh =====
        $kelas = Kelas::create([
            'mata_kuliah_id' => $ppl->id,
            'tahun_ajaran_id' => $tahun->id,
            'dosen_id' => $dosen->id,
            'nama' => 'Informatika B 23',
            'kode_kelas' => 'PPL23B',
            'deskripsi' => 'Kelas Proyek Perangkat Lunak: dari Project Charter sampai aplikasi jadi.',
            'semester' => 'ganjil',
            'status' => 'aktif',
        ]);
        $kelas->mahasiswa()->attach($mhs->pluck('id'));

        // Pertemuan 1 sudah lewat, pertemuan 2 absensinya sedang dibuka sekarang
        $p1 = $kelas->pertemuan()->create(['nomor' => 1, 'judul' => 'Pengantar Proyek Perangkat Lunak', 'tanggal' => now()->subDays(7)]);
        $p1->sesiAbsensi()->create(['dibuka_pada' => now()->subDays(7)->setTime(10, 30), 'ditutup_pada' => now()->subDays(7)->setTime(12, 0)]);
        $p2 = $kelas->pertemuan()->create(['nomor' => 2, 'judul' => 'Software Requirements Specification (SRS)', 'tanggal' => now()]);
        $p2->sesiAbsensi()->create(['dibuka_pada' => now()->subMinutes(30), 'ditutup_pada' => now()->addHours(2)]);

        $kelas->materi()->create([
            'judul' => 'Video: Apa itu SDLC?',
            'deskripsi' => "Assalamu'alaikum.\n\nSilakan tonton video tentang SDLC berikut sebelum pertemuan berikutnya.",
            'tipe' => 'link',
            'url' => 'https://www.youtube.com/results?search_query=sdlc+waterfall',
        ]);

        $kelas->tugas()->create([
            'jenis' => 'tugas',
            'judul' => 'Menyusun Project Charter',
            'instruksi' => "Susun Project Charter untuk proyek kelompok.\nKumpulkan dalam format PDF.",
            'deadline' => now()->addDays(3),
        ]);
        $kelas->tugas()->create([
            'jenis' => 'praktikum',
            'judul' => 'Praktikum 1: Instalasi Laravel',
            'instruksi' => 'Install Laravel di Laragon, lalu kumpulkan screenshot halaman awal.',
            'deadline' => now()->addHours(20),
        ]);

        $kuis = $kelas->kuis()->create([
            'judul' => 'Kuis 1: Dasar SDLC',
            'deskripsi' => 'Pilih satu jawaban yang paling tepat.',
            'mulai' => now()->subMinutes(10),
            'selesai' => now()->addMinutes(110),
            'durasi_menit' => 120,
            'dibuka' => true,
            'dibuka_pada' => now()->subMinutes(10),
        ]);
        $kuis->soal()->createMany([
            ['pertanyaan' => 'Model pengembangan yang tahapannya berurutan seperti air terjun disebut...', 'opsi_a' => 'Scrum', 'opsi_b' => 'Waterfall', 'opsi_c' => 'Spiral', 'opsi_d' => 'Kanban', 'jawaban' => 'b'],
            ['pertanyaan' => 'Dokumen yang berisi kebutuhan fungsional dan non-fungsional adalah...', 'opsi_a' => 'SRS', 'opsi_b' => 'SDD', 'opsi_c' => 'Project Charter', 'opsi_d' => 'Test Report', 'jawaban' => 'a'],
            ['pertanyaan' => 'Framework PHP yang dipakai pada proyek LMS ini adalah...', 'opsi_a' => 'Django', 'opsi_b' => 'Express', 'opsi_c' => 'Laravel', 'opsi_d' => 'Spring', 'jawaban' => 'c'],
        ]);
    }
}
