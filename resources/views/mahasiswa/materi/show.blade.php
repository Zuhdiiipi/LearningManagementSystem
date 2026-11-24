@extends('layouts.panel', ['menuAktif' => 'materi'])

@section('title', $materi->judul)
@section('kembali', route('mahasiswa.kelas.show', [$kelas, 'tab' => 'materi']))

@section('content')
  <div class="m-detail-head">
    <span class="m-icon"><i class="fa-solid fa-book-open"></i></span>
    <div>
      <h1>{{ $materi->judul }}</h1>
      <p>{{ \App\Support\Waktu::singkat($materi->created_at) }}@if ($materi->pertemuan) · Pertemuan {{ $materi->pertemuan->nomor }}@endif</p>
    </div>
  </div>

  <div class="m-paper">
    <div class="isi">{{ $materi->deskripsi ?: 'Silakan buka materi di bawah ini.' }}</div>
    <div>
      @if ($materi->tipe === 'file')
        {{-- Klik kartu untuk membaca/melihat materi --}}
        @if ($materi->bisaDilihat() || $materi->boleh_unduh)
          @include('mahasiswa._file', ['judul' => $materi->judul, 'url' => route('file.materi', $materi), 'namaFile' => $materi->file_name, 'baruTab' => $materi->bisaDilihat()])
        @endif

        {{-- Sistem memeriksa izin unduh dari dosen --}}
        @if ($materi->boleh_unduh)
          <a href="{{ route('file.materi', [$materi, 'unduh' => 1]) }}" class="m-btn navy small m-unduh"><i class="fa-solid fa-download"></i> Unduh</a>
        @elseif ($materi->bisaDilihat())
          <p class="m-note" style="margin-top:8px"><i class="fa-solid fa-lock"></i> Materi ini hanya dapat dilihat. Dosen tidak mengizinkan unduhan.</p>
        @else
          <p class="m-note"><i class="fa-solid fa-lock"></i> Dosen tidak mengizinkan materi ini diunduh, dan jenis file ini tidak bisa ditampilkan di browser.</p>
        @endif
      @else
        @include('mahasiswa._file', ['judul' => $materi->judul, 'url' => $materi->url, 'link' => true])
      @endif
    </div>
  </div>
@endsection
