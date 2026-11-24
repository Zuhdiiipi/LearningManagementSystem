@extends('layouts.panel')

@section('title', $materi->judul)
@section('kembali', route('dosen.kelas.show', [$kelas, 'tab' => 'materi']))

@section('content')
  <div class="m-detail-head">
    <span class="m-icon"><i class="fa-solid fa-book-open"></i></span>
    <div style="flex:1">
      <h1>{{ $materi->judul }}</h1>
      <p>{{ \App\Support\Waktu::singkat($materi->created_at) }}</p>
    </div>
    <div class="aksi-ikon">
      <a href="{{ route('dosen.materi.edit', $materi) }}" class="ikon ubah" title="Ubah"><i class="fa-regular fa-pen-to-square"></i><span class="sr">Ubah</span></a>
      @include('dosen._hapus', ['aksi' => route('dosen.materi.destroy', $materi), 'tanya' => 'Hapus materi ini?'])
    </div>
  </div>

  <div class="m-paper">
    <div class="isi">{{ $materi->deskripsi ?: '(Tanpa deskripsi)' }}</div>
    <div>
      @if ($materi->tipe === 'file')
        @include('mahasiswa._file', ['judul' => $materi->judul, 'url' => route('file.materi', $materi), 'namaFile' => $materi->file_name])
      @else
        @include('mahasiswa._file', ['judul' => $materi->judul, 'url' => $materi->url, 'link' => true])
      @endif
    </div>
  </div>

  @if ($materi->tipe === 'file')
    {{-- Pengaturan akses: tentukan izin unduh diizinkan / tidak --}}
    <form method="POST" action="{{ route('dosen.materi.akses', $materi) }}" class="m-box m-form m-akses" id="akses">
      @csrf @method('PATCH')
      <h2><i class="fa-solid fa-lock"></i> Pengaturan Akses</h2>
      <span class="label">Izin unduh untuk mahasiswa</span>
      <div class="pilih">
        <label><input type="radio" name="boleh_unduh" value="1" @checked($materi->boleh_unduh)> Diizinkan</label>
        <label><input type="radio" name="boleh_unduh" value="0" @checked(! $materi->boleh_unduh)> Tidak diizinkan (hanya dilihat)</label>
      </div>
      <p class="petunjuk">Status sekarang: <b>{{ $materi->boleh_unduh ? 'Boleh diunduh' : 'Hanya dapat dilihat' }}</b>.</p>
      <div class="bawah" style="padding-top:16px"><button class="m-btn navy">Simpan Pengaturan</button></div>
    </form>
  @endif
@endsection
