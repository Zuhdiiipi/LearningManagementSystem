@extends('layouts.panel')

@section('title', 'Ubah Kelas')

@section('content')
  <h1 class="m-title">Ubah Kelas</h1>
  <form method="POST" action="{{ route('admin.kelas.update', $kelas) }}" class="m-box lebar m-form" style="min-height:0">
    @csrf @method('PUT')

    <label for="nama">Nama Kelas</label>
    <input type="text" id="nama" name="nama" value="{{ old('nama', $kelas->nama) }}" required>

    <label for="mata_kuliah_id">Mata Kuliah</label>
    <select id="mata_kuliah_id" name="mata_kuliah_id" required>
      @foreach ($matkul as $m)
        <option value="{{ $m->id }}" @selected(old('mata_kuliah_id', $kelas->mata_kuliah_id) == $m->id)>{{ $m->kode }} - {{ $m->nama }}</option>
      @endforeach
    </select>

    <label for="dosen_id">Dosen Pengampu</label>
    <select id="dosen_id" name="dosen_id" required>
      @foreach ($dosen as $d)
        <option value="{{ $d->id }}" @selected(old('dosen_id', $kelas->dosen_id) == $d->id)>{{ $d->name }}</option>
      @endforeach
    </select>

    <span class="label">Semester</span>
    <div class="pilih">
      <label><input type="radio" name="semester" value="ganjil" @checked(old('semester', $kelas->semester) === 'ganjil')> Ganjil</label>
      <label><input type="radio" name="semester" value="genap" @checked(old('semester', $kelas->semester) === 'genap')> Genap</label>
    </div>

    <label for="tahun_ajaran_id">Tahun Akademik</label>
    <select id="tahun_ajaran_id" name="tahun_ajaran_id" class="pendek" required>
      @foreach ($tahun as $t)
        <option value="{{ $t->id }}" @selected(old('tahun_ajaran_id', $kelas->tahun_ajaran_id) == $t->id)>{{ $t->nama }}</option>
      @endforeach
    </select>

    <span class="label">Status Kelas</span>
    <div class="pilih">
      <label><input type="radio" name="status" value="aktif" @checked(old('status', $kelas->status) === 'aktif')> Aktif</label>
      <label><input type="radio" name="status" value="draft" @checked(old('status', $kelas->status) === 'draft')> Draft</label>
    </div>

    <div class="bawah tengah">
      <button class="m-btn navy" style="padding:10px 90px">Simpan</button>
      <p style="margin-top:12px"><a href="{{ route('admin.kelas.index') }}" class="m-muted">Batal</a></p>
    </div>
  </form>
@endsection
