@extends('layouts.panel')

@php
  $ubah = isset($pertemuan);
  $sesi = $ubah ? $pertemuan->sesiAbsensi : null;
@endphp
@section('title', $ubah ? 'Ubah Pertemuan' : 'Tambah Absensi')
@section('kembali', route('dosen.kelas.show', [$kelas, 'tab' => 'absensi']))

@section('content')
  <h1 class="m-title">{{ $ubah ? 'Ubah Pertemuan ' . $pertemuan->nomor : 'Tambah Absensi' }}</h1>
  <form method="POST" action="{{ $ubah ? route('dosen.absensi.update', $pertemuan) : route('dosen.absensi.store', $kelas) }}" class="m-box form m-form">
    @csrf
    @if ($ubah) @method('PUT') @endif
    <label for="nomor">Pertemuan ke</label>
    <input type="number" id="nomor" name="nomor" min="1" max="50" value="{{ old('nomor', $nomorBerikut) }}" required>

    <label for="tanggal">Hari</label>
    <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', $ubah && $pertemuan->tanggal ? $pertemuan->tanggal->format('Y-m-d') : now()->format('Y-m-d')) }}" required style="width:220px">
    <p class="petunjuk" id="nama-hari"></p>

    <div class="baris">
      <div>
        <label for="waktu_buka" style="margin-top:18px">Waktu Buka</label>
        <input type="time" id="waktu_buka" name="waktu_buka" value="{{ old('waktu_buka', $sesi ? $sesi->dibuka_pada->format('H:i') : '10:30') }}" required style="width:200px">
      </div>
      <div>
        <label for="waktu_tutup" style="margin-top:18px">Waktu Tutup</label>
        <input type="time" id="waktu_tutup" name="waktu_tutup" value="{{ old('waktu_tutup', $sesi ? $sesi->ditutup_pada->format('H:i') : '12:00') }}" required style="width:200px">
      </div>
    </div>

    <label for="judul">Topik Pertemuan (opsional)</label>
    <input type="text" id="judul" name="judul" value="{{ old('judul', $ubah ? $pertemuan->judul : '') }}" placeholder="Contoh: Migration dan Seeder">

    <p class="petunjuk" style="margin-top:14px">Mahasiswa hanya bisa menandai hadir di antara waktu buka dan waktu tutup.</p>
    <div class="bawah"><button class="m-btn navy">Simpan</button></div>
  </form>
@endsection

@push('scripts')
  <script>
    // Tampilkan nama hari dari tanggal yang dipilih
    const tgl = document.getElementById('tanggal'), hari = document.getElementById('nama-hari');
    const tulis = () => { if (tgl.value) hari.textContent = new Date(tgl.value + 'T00:00').toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }); };
    tgl.addEventListener('change', tulis); tulis();
  </script>
@endpush
