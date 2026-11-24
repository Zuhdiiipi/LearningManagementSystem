<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Teks yang berbeda untuk tiap peran (sesuai desain Figma)
    private array $teks = [
        'admin' => [
            'judul' => 'Login Sebagai Admin',
            'deskripsi' => 'Masuk untuk mengelola sistem dan data LMS',
            'ikon' => 'fa-user-gear',
        ],
        'dosen' => [
            'judul' => 'Login Sebagai Dosen',
            'deskripsi' => 'Masuk untuk mengelola kelas dan memantau mahasiswa',
            'ikon' => 'fa-chalkboard-user',
        ],
        'mahasiswa' => [
            'judul' => 'Login Sebagai Mahasiswa',
            'deskripsi' => 'Masuk untuk mengakses materi, tugas/kuis, dan informasi perkuliahan',
            'ikon' => 'fa-user-graduate',
        ],
    ];

    // Menampilkan halaman login sesuai peran
    public function showLogin(string $role)
    {
        return view('auth.login', [
            'role' => $role,
            'teks' => $this->teks[$role],
        ]);
    }

    // Memproses form login
    public function login(Request $request, string $role)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Login hanya berhasil jika email, password, DAN peran cocok, serta akun aktif
        $berhasil = Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $role,
            'is_active' => true,
        ]);

        if (! $berhasil) {
            return back()
                ->withErrors(['email' => 'Email atau password salah, atau akun ini bukan akun ' . $role . '.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        LogAktivitas::catat('Login sebagai ' . $role);

        return redirect()->route('dashboard');
    }

    // Menampilkan halaman registrasi (khusus mahasiswa)
    public function showRegister()
    {
        return view('auth.register');
    }

    // Memproses form registrasi
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'nim_nidn' => 'required|string|max:20|unique:users,nim_nidn',
            'role' => 'required|in:mahasiswa,dosen',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'role.required' => 'Pilih peran terlebih dahulu.',
            'nim_nidn.unique' => 'NIM/NIDN sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        // Peran dipilih saat daftar: Mahasiswa atau Dosen. Akun Admin hanya dibuat oleh Admin.
        $user = User::create([
            'name' => $data['name'],
            'nim_nidn' => $data['nim_nidn'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
        ]);

        Auth::login($user);
        LogAktivitas::catat('Mendaftar sebagai ' . $data['role'] . ' baru');
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    // Keluar dari sistem
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
