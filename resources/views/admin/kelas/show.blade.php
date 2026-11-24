@extends('layouts.panel')

@section('title', $kelas->mataKuliah->nama)

@section('content')
  <div class="a-head">
    <h1>{{ $kelas->mataKuliah->nama }}</h1>
    <a href="{{ route('admin.kelas.index') }}" class="m-btn outline"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
  </div>

  <div class="a-detail">
    <div class="info">
      <b>Nama Kelas</b><span>{{ $kelas->nama }}</span>
      <b>Kode Kelas</b><span>{{ $kelas->kode_kelas }}</span>
      <b>Mata Kuliah</b><span>{{ $kelas->mataKuliah->kode }} - {{ $kelas->mataKuliah->nama }}</span>
      <b>Program Studi</b><span>{{ $kelas->mataKuliah->programStudi->nama ?? '-' }}</span>
      <b>Dosen</b><span>{{ $kelas->dosen->name }}</span>
      <b>Tahun Akademik</b><span>{{ $kelas->tahunAjaran->nama ?? '-' }} ({{ ucfirst($kelas->semester) }})</span>
      <b>Status</b><span>{{ ucfirst($kelas->status) }}</span>
      <b>Isi Kelas</b><span>{{ $kelas->materi_count }} materi · {{ $kelas->tugas_count }} tugas/praktikum · {{ $kelas->kuis_count }} quiz · {{ $kelas->pertemuan_count }} absensi</span>
    </div>

    <h3 style="margin:28px 0 10px">Anggota ({{ $kelas->mahasiswa->count() }})</h3>
    @forelse ($kelas->mahasiswa as $i => $m)
      <p>{{ $i + 1 }}. {{ $m->name }} — {{ $m->nim_nidn ?? '-' }}</p>
    @empty
      <p class="m-muted">Belum ada mahasiswa.</p>
    @endforelse

    <div class="aksi-ikon" style="margin-top:26px">
      <a href="{{ route('admin.kelas.edit', $kelas) }}" class="m-btn navy small"><i class="fa-regular fa-pen-to-square"></i> Ubah Kelas</a>
    </div>
  </div>
@endsection
