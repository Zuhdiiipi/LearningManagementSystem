<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ===== Data master (dikelola Admin) =====
        Schema::create('program_studi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_studi_id')->constrained('program_studi')->cascadeOnDelete();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->unsignedTinyInteger('sks')->default(2);
            $table->timestamps();
        });

        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // contoh: 2026/2027 Ganjil
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // ===== Kelas (dibuat Dosen) =====
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama'); // contoh: Informatika B 23
            $table->string('kode_kelas', 10)->unique();
            $table->text('deskripsi')->nullable();
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->enum('status', ['aktif', 'draft'])->default('aktif'); // kelas draft belum bisa diikuti mahasiswa
            $table->timestamps();
        });

        Schema::create('kelas_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['kelas_id', 'mahasiswa_id']);
        });

        // ===== Pertemuan & materi =====
        Schema::create('pertemuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->unsignedTinyInteger('nomor');
            $table->string('judul')->nullable();
            $table->text('deskripsi')->nullable();
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });

        Schema::create('materi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('pertemuan_id')->nullable()->constrained('pertemuan')->nullOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['file', 'link']);
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('url')->nullable();
            $table->boolean('boleh_unduh')->default(true); // izin unduh dari dosen (FR-11)
            $table->timestamps();
        });

        // ===== Tugas & praktikum =====
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('pertemuan_id')->nullable()->constrained('pertemuan')->nullOnDelete();
            $table->enum('jenis', ['tugas', 'praktikum'])->default('tugas');
            $table->string('judul');
            $table->text('instruksi');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->dateTime('deadline');
            $table->boolean('pengingat_terkirim')->default(false);
            $table->timestamps();
        });

        Schema::create('pengumpulan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->text('catatan')->nullable();
            $table->dateTime('waktu_kumpul')->nullable(); // waktu mahasiswa mengunggah (SDD: waktu_kumpul)
            $table->unsignedTinyInteger('nilai')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->unique(['tugas_id', 'mahasiswa_id']);
        });

        // ===== Kuis pilihan ganda =====
        Schema::create('kuis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('mulai');
            $table->dateTime('selesai');
            $table->unsignedSmallInteger('durasi_menit')->default(30);
            // Sesi quiz dibuka manual oleh dosen (tombol Buka Sesi / Tutup Sesi)
            $table->boolean('dibuka')->default(false);
            $table->dateTime('dibuka_pada')->nullable();
            $table->timestamps();
        });

        Schema::create('soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuis_id')->constrained('kuis')->cascadeOnDelete();
            $table->text('pertanyaan');
            $table->string('opsi_a');
            $table->string('opsi_b');
            $table->string('opsi_c')->nullable();
            $table->string('opsi_d')->nullable();
            $table->string('opsi_e')->nullable();
            $table->enum('jawaban', ['a', 'b', 'c', 'd', 'e']);
            $table->timestamps();
        });

        Schema::create('hasil_kuis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuis_id')->constrained('kuis')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->json('jawaban');
            $table->unsignedSmallInteger('benar');
            $table->unsignedSmallInteger('jumlah_soal');
            $table->decimal('skor', 5, 2);
            $table->timestamps();
            $table->unique(['kuis_id', 'mahasiswa_id']);
        });

        // ===== Absensi =====
        Schema::create('sesi_absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertemuan_id')->unique()->constrained('pertemuan')->cascadeOnDelete();
            // Mahasiswa bisa menandai hadir hanya di antara waktu buka dan waktu tutup
            $table->dateTime('dibuka_pada');
            $table->dateTime('ditutup_pada');
            $table->timestamps();
        });

        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_absensi_id')->constrained('sesi_absensi')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa'])->default('hadir');
            $table->timestamps();
            $table->unique(['sesi_absensi_id', 'mahasiswa_id']);
        });

        // ===== Log aktivitas (untuk laporan Admin) =====
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aksi');
            $table->timestamps();
        });

        // ===== Log notifikasi email (SDD: log_notifikasi) =====
        Schema::create('log_notifikasi', function (Blueprint $table) {
            $table->id();
            $table->string('penerima_email', 100);
            $table->string('subjek');
            $table->string('jenis_notif', 20); // Materi, Tugas, Quiz, Nilai, Pengingat
            $table->boolean('status_pengiriman');  // true = sukses, false = gagal
            $table->dateTime('waktu_kirim');
        });

        // ===== Notifikasi di dalam aplikasi (ikon lonceng mahasiswa) =====
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['notifications', 'log_aktivitas', 'absensi', 'sesi_absensi', 'hasil_kuis', 'soal', 'kuis', 'pengumpulan', 'tugas',
                  'log_notifikasi', 'materi', 'pertemuan', 'kelas_mahasiswa', 'kelas', 'tahun_ajaran', 'mata_kuliah', 'program_studi'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
