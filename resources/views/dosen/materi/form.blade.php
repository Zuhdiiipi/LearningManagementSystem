@extends('layouts.panel')

@section('title', $materi->exists ? 'Ubah Materi' : 'Tambah Materi')
@section('kembali', route('dosen.kelas.show', [$kelas, 'tab' => 'materi']))

@section('content')
  <h1 class="m-title">{{ $materi->exists ? 'Ubah Materi' : 'Tambah Materi' }}</h1>
  <form method="POST" enctype="multipart/form-data" class="m-box form m-form"
        action="{{ $materi->exists ? route('dosen.materi.update', $materi) : route('dosen.materi.store', $kelas) }}">
    @csrf
    @if ($materi->exists) @method('PUT') @endif

    <label for="judul">Judul</label>
    <input type="text" id="judul" name="judul" value="{{ old('judul', $materi->judul) }}" required>

    <label for="file">Masukkan File</label>
    <input type="file" id="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt,.png,.jpg,.jpeg,.mp4">
    <p class="petunjuk">
      PDF, Word, PowerPoint, Excel, ZIP, gambar, atau video. Maksimal 50 MB.
      @if ($materi->file_name) File sekarang: <b>{{ $materi->file_name }}</b> (kosongkan kalau tidak diganti). @endif
    </p>

    <label for="url">Atau Link (opsional)</label>
    <input type="url" id="url" name="url" value="{{ old('url', $materi->url) }}" placeholder="https://youtube.com/... atau link Google Drive">

    <label for="deskripsi">Deskripsi</label>
    <textarea id="deskripsi" name="deskripsi">{{ old('deskripsi', $materi->deskripsi) }}</textarea>

    <span class="label">Izin Unduh</span>
    <div class="pilih">
      <label><input type="radio" name="boleh_unduh" value="1" @checked(old('boleh_unduh', $materi->exists ? (int) $materi->boleh_unduh : 1) == 1)> Boleh diunduh</label>
      <label><input type="radio" name="boleh_unduh" value="0" @checked(old('boleh_unduh', $materi->exists ? (int) $materi->boleh_unduh : 1) == 0)> Hanya dilihat</label>
    </div>
    <p class="petunjuk">"Hanya dilihat": mahasiswa bisa membuka file di browser (PDF, gambar, video, TXT) tetapi tidak ada tombol unduh.</p>

    <div class="bawah"><button class="m-btn navy">Simpan</button></div>
  </form>
@endsection
