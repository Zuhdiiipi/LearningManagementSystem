@extends('layouts.panel')
@php $judulHalaman = ($tugas->exists ? 'Ubah ' : 'Tambah ') . ($tugas->jenis === 'praktikum' ? 'Praktikum' : 'Tugas'); @endphp
@section('title', $judulHalaman)
@section('kembali', route('dosen.kelas.show', [$kelas, 'tab' => 'tugas']))

@section('content')
  <h1 class="m-title">{{ $judulHalaman }}</h1>
  <form method="POST" enctype="multipart/form-data" class="m-box form m-form"
        action="{{ $tugas->exists ? route('dosen.tugas.update', $tugas) : route('dosen.tugas.store', $kelas) }}">
    @csrf
    @if ($tugas->exists) @method('PUT') @endif

    <label for="judul">Judul</label>
    <input type="text" id="judul" name="judul" value="{{ old('judul', $tugas->judul) }}" required>

    <label for="deadline">Deadline</label>
    <input type="datetime-local" id="deadline" name="deadline" class="pendek" style="max-width:300px"
           value="{{ old('deadline', $tugas->deadline?->format('Y-m-d\TH:i')) }}" required>

    <label for="file">Masukkan File</label>
    <input type="file" id="file" name="file">
    <p class="petunjuk">Opsional, maksimal 20 MB.
      @if ($tugas->file_name) File sekarang: <b>{{ $tugas->file_name }}</b> (kosongkan kalau tidak diganti). @endif</p>

    <label for="instruksi">Deskripsi</label>
    <textarea id="instruksi" name="instruksi" required>{{ old('instruksi', $tugas->instruksi) }}</textarea>

    <div class="pilih" style="margin-top:24px">
      <label><input type="radio" name="jenis" value="tugas" @checked(old('jenis', $tugas->jenis) === 'tugas')> Tugas</label>
      <label><input type="radio" name="jenis" value="praktikum" @checked(old('jenis', $tugas->jenis) === 'praktikum')> Praktikum</label>
    </div>

    <div class="bawah"><button class="m-btn navy">Simpan</button></div>
  </form>
@endsection
