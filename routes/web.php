<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

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
