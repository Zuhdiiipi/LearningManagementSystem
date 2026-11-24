@extends('layouts.panel')

@section('title', 'Dashboard Admin')

@section('content')
  @include('partials.banner', [
      'label' => 'Dashboard Admin', 'ikon' => 'fa-shield-halved', 'sapaan' => 'Admin',
      'judul' => 'Selamat datang Kembali!', 'sub' => 'Semoga hari ini terlihat semakin Menyenangkan.',
  ])

  <h2 class="a-section">Monitoring</h2>
  <div class="a-stats">
    <div><small>Total Pengguna</small><b>{{ $totalPengguna }}</b></div>
    <div><small>Total Kelas</small><b>{{ $totalKelas }}</b></div>
    <div><small>Kelas Aktif</small><b>{{ $kelasAktif }}</b></div>
    <div><small>Pengguna Aktif</small><b>{{ $penggunaAktif }}</b></div>
  </div>

  <h2 class="a-section">Kelola</h2>
  <a href="{{ route('admin.users.index') }}" class="a-link">Kelola Pengguna</a>
  <a href="{{ route('admin.kelas.index') }}" class="a-link">Kelola Kelas</a>
  <a href="{{ route('admin.master.index') }}" class="a-link">Data Master (Program Studi, Mata Kuliah, Tahun Ajaran)</a>
  {{-- <a href="{{ route('admin.laporan') }}" class="a-link">Laporan Aktivitas</a> --}}
@endsection
