@extends('layouts.panel')

@section('title', 'Kelola Pengguna')

@php
  // Sembunyikan sebagian email, contoh: budi********@gmail.com
  $sensor = function ($email) {
      [$nama, $domain] = array_pad(explode('@', $email, 2), 2, '');
      return mb_substr($nama, 0, 4) . str_repeat('*', 8) . '@' . $domain;
  };
@endphp

@section('content')
  <div class="a-head">
    <h1>Kelola Pengguna</h1>
    <div class="kanan">
      <form method="GET" class="a-cari">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama / NIM / email">
        <select name="role" onchange="this.form.submit()">
          <option value="">Semua peran</option>
          @foreach (['admin' => 'Admin', 'dosen' => 'Dosen', 'mahasiswa' => 'Mahasiswa'] as $r => $label)
            <option value="{{ $r }}" @selected(request('role') === $r)>{{ $label }}</option>
          @endforeach
        </select>
      </form>
      <a href="{{ route('admin.users.create') }}" class="m-btn navy small"><i class="fa-solid fa-plus"></i> Tambah</a>
      <span class="total">Total Pengguna : {{ $users->count() }}</span>
    </div>
  </div>

  <div class="a-tabel">
    <div class="kepala"><span>No</span><span>Nama</span><span>NIM/NIDN</span><span>Email</span><span>Peran</span><span></span></div>
    @forelse ($users as $i => $u)
      <div class="baris {{ $u->is_active ? '' : 'nonaktif' }}">
        <span>{{ $i + 1 }}.</span>
        <span>{{ $u->name }} @unless ($u->is_active) <small>(nonaktif)</small> @endunless</span>
        <span>{{ $u->nim_nidn ?? '-' }}</span>
        <span>{{ $sensor($u->email) }}</span>
        <span>{{ ucfirst($u->role) }}</span>
        <div class="aksi-ikon">
          <a href="{{ route('admin.users.show', $u) }}" class="ikon lihat" title="Lihat"><i class="fa-regular fa-eye"></i><span class="sr">Lihat</span></a>
          <a href="{{ route('admin.users.edit', $u) }}" class="ikon ubah" title="Ubah"><i class="fa-regular fa-pen-to-square"></i><span class="sr">Ubah</span></a>
          {{-- Reset kata sandi: admin memilih akun, mengonfirmasi, lalu sistem mereset --}}
          <form method="POST" action="{{ route('admin.users.reset', $u) }}" onsubmit="return confirm(@js('Reset kata sandi ' . $u->name . ' menjadi password123?'))">
            @csrf @method('PATCH')
            <button class="ikon kunci" title="Reset kata sandi"><i class="fa-solid fa-key"></i><span class="sr">Reset kata sandi</span></button>
          </form>
          @if ($u->id !== auth()->id())
            {{-- Nonaktifkan / aktifkan akun: admin memilih akun, mengonfirmasi, lalu sistem mengubah status --}}
            <form method="POST" action="{{ route('admin.users.toggle', $u) }}" onsubmit="return confirm(@js(($u->is_active ? 'Nonaktifkan' : 'Aktifkan') . ' akun ' . $u->name . '?'))">
              @csrf @method('PATCH')
              <button class="ikon {{ $u->is_active ? 'mati' : 'nyala' }}" title="{{ $u->is_active ? 'Nonaktifkan akun' : 'Aktifkan akun' }}"><i class="fa-solid fa-power-off"></i><span class="sr">{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</span></button>
            </form>
            {{-- @include('dosen._hapus', ['aksi' => route('admin.users.destroy', $u), 'tanya' => 'Hapus akun ' . $u->name . '?']) --}}
          @endif
        </div>
      </div>
    @empty
      <div class="baris"><span></span><span>Tidak ada pengguna.</span></div>
    @endforelse
  </div>
@endsection
