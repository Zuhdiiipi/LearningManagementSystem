@extends('layouts.panel')

@section('title', $tugas->judul)
@section('kembali', route('dosen.kelas.show', [$kelas, 'tab' => 'tugas']))

@section('content')
  <div class="m-detail-head">
    <span class="m-icon"><i class="fa-solid {{ $tugas->jenis === 'tugas' ? 'fa-file-code' : 'fa-clipboard-list' }}"></i></span>
    <div style="flex:1">
      <h1>{{ $tugas->judul }}</h1>
      <p>{{ \App\Support\Waktu::singkat($tugas->created_at) }} &nbsp;|&nbsp; Batas: {{ $tugas->deadline->translatedFormat('l, j F Y') }}, {{ $tugas->deadline->format('H : i') }}</p>
    </div>
    <div class="aksi-ikon">
      <a href="{{ route('dosen.tugas.edit', $tugas) }}" class="ikon ubah" title="Ubah"><i class="fa-regular fa-pen-to-square"></i><span class="sr">Ubah</span></a>
      @include('dosen._hapus', ['aksi' => route('dosen.tugas.destroy', $tugas), 'tanya' => 'Hapus ' . $tugas->judul . '?'])
    </div>
  </div>

  <div class="m-paper">
    <div class="isi">{{ $tugas->instruksi }}</div>
    @if ($tugas->file_path)
      <div>@include('mahasiswa._file', ['judul' => $tugas->file_name, 'url' => route('file.tugas', $tugas), 'namaFile' => $tugas->file_name])</div>
    @endif
    <div class="bawah">
      <a href="{{ route('dosen.tugas.terkumpul', $tugas) }}" class="m-btn navy">Lihat yang Mengumpulkan ({{ $tugas->pengumpulan_count }}/{{ $jumlahMahasiswa }})</a>
    </div>
  </div>
@endsection
