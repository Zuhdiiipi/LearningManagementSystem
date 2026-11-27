<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\DataMasterController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dosen;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\ProfilController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');

Route::get('/login/{role}', [AuthController::class, 'showLogin'])
    ->whereIn('role', ['admin', 'dosen', 'mahasiswa'])
    ->name('login.form');

Route::post('/login/{role}', [AuthController::class, 'login'])
    ->whereIn('role', ['admin', 'dosen', 'mahasiswa'])
    ->name('login.submit');

Route::redirect('/login', '/')->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/file/materi/{materi}', [FileController::class, 'materi'])->name('file.materi');
    Route::get('/file/tugas/{tugas}', [FileController::class, 'tugas'])->name('file.tugas');
    Route::get('/file/pengumpulan/{pengumpulan}', [FileController::class, 'pengumpulan'])->name('file.pengumpulan');
    Route::get('/file/foto/{user}', [FileController::class, 'foto'])->name('file.foto');

    // Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset');

        Route::resource('kelas', Admin\KelasController::class)
            ->only(['index', 'show', 'edit', 'update', 'destroy'])
            ->parameters(['kelas' => 'kelas']);

        Route::get('master', [DataMasterController::class, 'index'])->name('master.index');
        Route::post('master/prodi', [DataMasterController::class, 'storeProdi'])->name('master.prodi.store');
        Route::put('master/prodi/{prodi}', [DataMasterController::class, 'updateProdi'])->name('master.prodi.update');
        Route::delete('master/prodi/{prodi}', [DataMasterController::class, 'destroyProdi'])->name('master.prodi.destroy');
        Route::post('master/matkul', [DataMasterController::class, 'storeMatkul'])->name('master.matkul.store');
        Route::put('master/matkul/{matkul}', [DataMasterController::class, 'updateMatkul'])->name('master.matkul.update');
        Route::delete('master/matkul/{matkul}', [DataMasterController::class, 'destroyMatkul'])->name('master.matkul.destroy');
        Route::post('master/tahun', [DataMasterController::class, 'storeTahun'])->name('master.tahun.store');
        Route::patch('master/tahun/{tahun}/aktifkan', [DataMasterController::class, 'aktifkanTahun'])->name('master.tahun.aktifkan');
        Route::delete('master/tahun/{tahun}', [DataMasterController::class, 'destroyTahun'])->name('master.tahun.destroy');

        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan');
    });

    // Dosen
    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dosen'])->name('dashboard');
        Route::get('profil', [ProfilController::class, 'show'])->name('profil');
        Route::put('profil', [ProfilController::class, 'update'])->name('profil.update');
        Route::post('profil/foto', [ProfilController::class, 'foto'])->name('profil.foto');

        // Kelas dan bobot penilaian
        Route::resource('kelas', Dosen\KelasController::class)->parameters(['kelas' => 'kelas']);
        Route::get('kelas/{kelas}/export-nilai', [Dosen\KelasController::class, 'exportNilai'])->name('kelas.export');
        Route::patch('kelas/{kelas}/bobot', [Dosen\KelasController::class, 'updateBobot'])->name('kelas.bobot.update');
        Route::delete('kelas/{kelas}/mahasiswa/{mahasiswa}', [Dosen\KelasController::class, 'keluarkan'])->name('kelas.keluarkan');

        // Materi
        Route::get('kelas/{kelas}/materi/tambah', [Dosen\MateriController::class, 'create'])->name('materi.create');
        Route::post('kelas/{kelas}/materi', [Dosen\MateriController::class, 'store'])->name('materi.store');
        Route::get('materi/{materi}', [Dosen\MateriController::class, 'show'])->name('materi.show');
        Route::get('materi/{materi}/ubah', [Dosen\MateriController::class, 'edit'])->name('materi.edit');
        Route::put('materi/{materi}', [Dosen\MateriController::class, 'update'])->name('materi.update');
        Route::delete('materi/{materi}', [Dosen\MateriController::class, 'destroy'])->name('materi.destroy');

        // Absensi
        Route::get('kelas/{kelas}/absensi/tambah', [Dosen\AbsensiController::class, 'create'])->name('absensi.create');
        Route::post('kelas/{kelas}/absensi', [Dosen\AbsensiController::class, 'store'])->name('absensi.store');
        Route::get('pertemuan/{pertemuan}/absensi', [Dosen\AbsensiController::class, 'show'])->name('absensi.show');
        Route::post('pertemuan/{pertemuan}/absensi', [Dosen\AbsensiController::class, 'simpan'])->name('absensi.simpan');
        Route::post('pertemuan/{pertemuan}/absensi/buka', [Dosen\AbsensiController::class, 'buka'])->name('absensi.buka');
        Route::post('pertemuan/{pertemuan}/absensi/tutup', [Dosen\AbsensiController::class, 'tutup'])->name('absensi.tutup');
        Route::delete('pertemuan/{pertemuan}', [Dosen\AbsensiController::class, 'destroy'])->name('absensi.destroy');
        Route::get('pertemuan/{pertemuan}/ubah', [Dosen\AbsensiController::class, 'edit'])->name('absensi.edit');
        Route::put('pertemuan/{pertemuan}', [Dosen\AbsensiController::class, 'update'])->name('absensi.update');
        Route::patch('pertemuan/{pertemuan}/urut/{arah}', [Dosen\AbsensiController::class, 'urut'])->name('absensi.urut');

        // Tugas dan praktikum
        Route::get('kelas/{kelas}/tugas/tambah', [Dosen\TugasController::class, 'create'])->name('tugas.create');
        Route::post('kelas/{kelas}/tugas', [Dosen\TugasController::class, 'store'])->name('tugas.store');
        Route::get('tugas/{tugas}', [Dosen\TugasController::class, 'show'])->name('tugas.show');
        Route::get('tugas/{tugas}/ubah', [Dosen\TugasController::class, 'edit'])->name('tugas.edit');
        Route::put('tugas/{tugas}', [Dosen\TugasController::class, 'update'])->name('tugas.update');
        Route::delete('tugas/{tugas}', [Dosen\TugasController::class, 'destroy'])->name('tugas.destroy');
        Route::get('tugas/{tugas}/terkumpul', [Dosen\TugasController::class, 'terkumpul'])->name('tugas.terkumpul');
        Route::patch('pengumpulan/{pengumpulan}/nilai', [Dosen\TugasController::class, 'nilai'])->name('pengumpulan.nilai');

        // Kuis
        Route::get('kelas/{kelas}/kuis/tambah', [Dosen\KuisController::class, 'create'])->name('kuis.create');
        Route::post('kelas/{kelas}/kuis', [Dosen\KuisController::class, 'store'])->name('kuis.store');
        Route::get('kuis/{kuis}', [Dosen\KuisController::class, 'skor'])->name('kuis.show');
        Route::get('kuis/{kuis}/ubah', [Dosen\KuisController::class, 'edit'])->name('kuis.edit');
        Route::put('kuis/{kuis}', [Dosen\KuisController::class, 'update'])->name('kuis.update');
        Route::delete('kuis/{kuis}', [Dosen\KuisController::class, 'destroy'])->name('kuis.destroy');
        Route::post('kuis/{kuis}/buka', [Dosen\KuisController::class, 'buka'])->name('kuis.buka');
        Route::post('kuis/{kuis}/tutup', [Dosen\KuisController::class, 'tutup'])->name('kuis.tutup');
        Route::post('kuis/{kuis}/umumkan', [Dosen\KuisController::class, 'umumkan'])->name('kuis.umumkan');
    });

    // Mahasiswa
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'mahasiswa'])->name('dashboard');

        Route::get('notifikasi', [Mahasiswa\NotifikasiController::class, 'index'])->name('notifikasi');
        Route::post('notifikasi/baca-semua', [Mahasiswa\NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
        Route::get('notifikasi/{id}', [Mahasiswa\NotifikasiController::class, 'buka'])->name('notifikasi.buka');

        Route::get('kelas', [Mahasiswa\KelasController::class, 'index'])->name('kelas.index');
        Route::post('kelas/gabung', [Mahasiswa\KelasController::class, 'gabung'])->name('kelas.gabung');
        Route::get('kelas/{kelas}', [Mahasiswa\KelasController::class, 'show'])->name('kelas.show');
        Route::get('materi/{materi}', [Mahasiswa\KelasController::class, 'materi'])->name('materi.show');

        Route::post('pertemuan/{pertemuan}/hadir', [Mahasiswa\AbsensiController::class, 'hadir'])->name('absensi.hadir');

        Route::get('tugas/{tugas}', [Mahasiswa\TugasController::class, 'show'])->name('tugas.show');
        Route::post('tugas/{tugas}/kumpul', [Mahasiswa\TugasController::class, 'kumpul'])->name('tugas.kumpul');
        Route::delete('tugas/{tugas}/kumpul', [Mahasiswa\TugasController::class, 'batal'])->name('tugas.batal');

        Route::get('kuis/{kuis}', [Mahasiswa\KuisController::class, 'kerjakan'])->name('kuis.kerjakan');
        Route::post('kuis/{kuis}', [Mahasiswa\KuisController::class, 'submit'])->name('kuis.submit');
        Route::get('kuis/{kuis}/hasil', [Mahasiswa\KuisController::class, 'hasil'])->name('kuis.hasil');

        Route::get('nilai', [Mahasiswa\NilaiController::class, 'index'])->name('nilai');

        Route::get('profil', [ProfilController::class, 'show'])->name('profil');
        Route::put('profil', [ProfilController::class, 'update'])->name('profil.update');
        Route::post('profil/foto', [ProfilController::class, 'foto'])->name('profil.foto');
    });
});
