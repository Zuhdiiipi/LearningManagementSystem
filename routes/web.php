<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DataMasterController;
use App\Http\Controllers\Admin\KelasController as AdminKelasController;

// Beranda LMS
Route::view('/', 'landing')->name('landing');

// Halaman dan proses login untuk tiga peran
Route::get('/login/{role}', [AuthController::class, 'showLogin'])
    ->whereIn('role', ['admin', 'dosen', 'mahasiswa'])
    ->name('login.form');

Route::post('/login/{role}', [AuthController::class, 'login'])
    ->whereIn('role', ['admin', 'dosen', 'mahasiswa'])
    ->name('login.submit');

// Tujuan pengalihan bagi pengguna yang belum login
Route::redirect('/login', '/')->name('login');

// Registrasi dan logout
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Semua rute di dalam grup ini perlu login
Route::middleware('auth')->group(function () {
    // Mengarahkan pengguna ke dashboard sesuai perannya
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    // Kelola Pengguna
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle', [UserController::class, 'toggleStatus'])
    ->name('users.toggle');
    Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])
    ->name('users.reset');

// Kelola Kelas oleh Admin
    Route::resource('kelas', AdminKelasController::class)
    ->only(['index', 'show', 'edit', 'update', 'destroy'])
    ->parameters(['kelas' => 'kelas']);

// Data Master Akademik
    Route::get('master', [DataMasterController::class, 'index'])->name('master.index');

    Route::post('master/prodi', [DataMasterController::class, 'storeProdi'])->name('master.prodi.store');
    Route::put('master/prodi/{prodi}', [DataMasterController::class, 'updateProdi'])->name('master.prodi.update');
    Route::delete('master/prodi/{prodi}', [DataMasterController::class, 'destroyProdi'])->name('master.prodi.destroy');

    Route::post('master/matkul', [DataMasterController::class, 'storeMatkul'])->name('master.matkul.store');
    Route::put('master/matkul/{matkul}', [DataMasterController::class, 'updateMatkul'])->name('master.matkul.update');
    Route::delete('master/matkul/{matkul}', [DataMasterController::class, 'destroyMatkul'])->name('master.matkul.destroy');

    Route::post('master/tahun', [DataMasterController::class, 'storeTahun'])->name('master.tahun.store');
    Route::patch('master/tahun/{tahun}/aktifkan', [DataMasterController::class, 'aktifkanTahun'])
        ->name('master.tahun.aktifkan');
    Route::delete('master/tahun/{tahun}', [DataMasterController::class, 'destroyTahun'])->name('master.tahun.destroy');
        // Rute kelola akun, data master, kelas, dan laporan tetap di grup ini.
    });

    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dosen'])->name('dashboard');

        // Rute fitur dosen tetap di grup ini.
    });

    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'mahasiswa'])->name('dashboard');

        // Rute fitur mahasiswa tetap di grup ini.
    });
});
