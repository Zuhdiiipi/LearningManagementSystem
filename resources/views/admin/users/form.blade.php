@extends('layouts.panel')

@section('title', $user->exists ? 'Ubah Pengguna' : 'Tambah Pengguna')

@section('content')
  <h1 class="m-title">{{ $user->exists ? 'Ubah Pengguna' : 'Tambah Pengguna' }}</h1>
  <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="m-box lebar m-form" style="min-height:0">
    @csrf
    @if ($user->exists) @method('PUT') @endif

    <label for="name">Nama Lengkap</label>
    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>

    <label for="nim_nidn">NIM / NIDN</label>
    <input type="text" id="nim_nidn" name="nim_nidn" value="{{ old('nim_nidn', $user->nim_nidn) }}">

    <label for="email">Email</label>
    <input type="text" id="email" name="email" value="{{ old('email', $user->email) }}" required>

    <span class="label">Peran</span>
    <div class="pilih">
      @foreach (['admin' => 'Admin', 'dosen' => 'Dosen', 'mahasiswa' => 'Mahasiswa'] as $r => $label)
        <label><input type="radio" name="role" value="{{ $r }}" @checked(old('role', $user->role ?? 'mahasiswa') === $r)> {{ $label }}</label>
      @endforeach
    </div>

    @unless ($user->exists)
      <label for="password">Password (minimal 8 karakter)</label>
      <input type="text" id="password" name="password" value="{{ old('password') }}" required>
    @endunless

    <div class="bawah tengah">
      <button class="m-btn navy" style="padding:10px 90px">Simpan</button>
      <p style="margin-top:12px"><a href="{{ route('admin.users.index') }}" class="m-muted">Batal</a></p>
    </div>
  </form>
@endsection
