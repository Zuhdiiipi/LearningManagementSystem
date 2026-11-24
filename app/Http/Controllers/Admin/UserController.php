<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Daftar akun, bisa dicari dan difilter per peran
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->cari, function ($q, $cari) {
                $q->where(function ($q) use ($cari) {
                    $q->where('name', 'like', "%$cari%")
                      ->orWhere('email', 'like', "%$cari%")
                      ->orWhere('nim_nidn', 'like', "%$cari%");
                });
            })
            ->when($request->role, fn ($q, $role) => $q->where('role', $role))
            ->orderByRaw("CASE role WHEN 'admin' THEN 1 WHEN 'dosen' THEN 2 ELSE 3 END")
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    // Detail akun
    public function show(User $user)
    {
        $user->loadCount(['kelasDiampu', 'kelasDiikuti']);

        return view('admin.users.show', compact('user'));
    }

    // Form tambah akun
    public function create()
    {
        return view('admin.users.form', ['user' => new User()]);
    }

    // Simpan akun baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'nim_nidn' => 'nullable|string|max:20|unique:users,nim_nidn',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,dosen,mahasiswa',
            'password' => 'required|min:8',
        ], $this->pesan());

        User::create($data);

        return redirect()->route('admin.users.index')->with('sukses', 'Akun berhasil ditambahkan.');
    }

    // Form ubah akun
    public function edit(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    // Simpan perubahan akun
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'nim_nidn' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,dosen,mahasiswa',
        ], $this->pesan());

        $user->update($data);

        return redirect()->route('admin.users.index')->with('sukses', 'Akun berhasil diperbarui.');
    }

    // Aktifkan / nonaktifkan akun
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('gagal', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return redirect()->route('admin.users.index')->with('sukses', 'Akun ' . $user->name . ' sekarang ' . ($user->is_active ? 'aktif.' : 'nonaktif. Akun ini tidak bisa login sampai diaktifkan lagi.'));
    }

    // Reset password menjadi password default
    public function resetPassword(User $user)
    {
        $user->update(['password' => 'password123']);

        return redirect()->route('admin.users.index')->with('sukses', 'Kata sandi ' . $user->name . ' direset menjadi: password123');
    }

    // Hapus akun
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('gagal', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('sukses', 'Akun berhasil dihapus.');
    }

    // Pesan kesalahan dalam Bahasa Indonesia (data akun tidak valid -> kembali ke formulir)
    private function pesan(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai akun lain.',
            'nim_nidn.unique' => 'NIM/NIDN sudah terdaftar.',
            'role.required' => 'Peran wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
        ];
    }
}
