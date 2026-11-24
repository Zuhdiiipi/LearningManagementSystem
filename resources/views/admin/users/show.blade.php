@extends('layouts.panel')

@section('title', $user->name)

@section('content')
  <div class="a-head">
    <h1>Detail Pengguna</h1>
    <a href="{{ route('admin.users.index') }}" class="m-btn outline"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
  </div>

  <div class="a-detail">
    <div class="info">
      <b>Nama</b><span>{{ $user->name }}</span>
      <b>NIM / NIDN</b><span>{{ $user->nim_nidn ?? '-' }}</span>
      <b>Email</b><span>{{ $user->email }}</span>
      <b>Peran</b><span>{{ ucfirst($user->role) }}</span>
      <b>Status</b><span>{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
      @if ($user->role === 'mahasiswa')
        <b>Program Studi</b><span>{{ $user->program_studi ?? '-' }}</span>
        <b>Angkatan</b><span>{{ $user->angkatan ?? '-' }}</span>
        <b>Kelas diikuti</b><span>{{ $user->kelas_diikuti_count }}</span>
      @elseif ($user->role === 'dosen')
        <b>Kelas diampu</b><span>{{ $user->kelas_diampu_count }}</span>
      @endif
      <b>Telepon</b><span>{{ $user->telepon ?? '-' }}</span>
      <b>Terdaftar</b><span>{{ $user->created_at->translatedFormat('j F Y') }}</span>
    </div>

    <div class="aksi-ikon" style="margin-top:26px;flex-wrap:wrap">
      <a href="{{ route('admin.users.edit', $user) }}" class="m-btn navy small"><i class="fa-regular fa-pen-to-square"></i> Ubah Data</a>
      @if ($user->id !== auth()->id())
        <form method="POST" action="{{ route('admin.users.toggle', $user) }}" onsubmit="return confirm(@js(($user->is_active ? 'Nonaktifkan' : 'Aktifkan') . ' akun ' . $user->name . '?'))">
          @csrf @method('PATCH')
          <button class="m-btn outline small">{{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}</button>
        </form>
      @endif
      <form method="POST" action="{{ route('admin.users.reset', $user) }}" onsubmit="return confirm('Reset password {{ $user->name }} menjadi password123?')">
        @csrf @method('PATCH')
        <button class="m-btn outline small">Reset Kata Sandi</button>
      </form>
    </div>
  </div>
@endsection
