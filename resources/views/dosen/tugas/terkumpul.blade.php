@extends('layouts.panel')

@section('title', ucfirst($tugas->jenis) . ' Selesai')
@section('kembali', route('dosen.tugas.show', $tugas))
@section('content')
  <h1 class="m-title">{{ ucfirst($tugas->jenis) }} Selesai</h1>
  <div class="m-box">
    <div class="m-info">
      <span><b>{{ $tugas->judul }}</b> · batas {{ $tugas->deadline->translatedFormat('j F Y, H:i') }}</span>
      <span>{{ $pengumpulan->count() }} mengumpulkan, {{ $belum->count() }} belum</span>
    </div>
    <div class="m-list">
      @forelse ($pengumpulan as $k)
        <div class="m-orang">
          <div>
            <h4>{{ $k->mahasiswa->name }}</h4>
            @include('mahasiswa._file', ['judul' => $k->file_name, 'url' => route('file.pengumpulan', $k), 'namaFile' => $k->file_name])
            <p class="m-note">Dikirim {{ $k->waktuKumpul()->translatedFormat('j M Y, H:i') }}
              @if ($k->terlambat()) <b style="color:#dc2626">(terlambat {{ $k->lamaTerlambat() }})</b> @else <b style="color:#16a34a">(tepat waktu)</b> @endif
              @if ($k->catatan) · Catatan: {{ $k->catatan }} @endif</p>
          </div>
          {{-- Kotak nilai: ketik angka lalu Enter atau klik Simpan --}}
          <form method="POST" action="{{ route('dosen.pengumpulan.nilai', $k) }}" class="kanan">
            @csrf @method('PATCH')
            <input type="text" name="feedback" value="{{ $k->feedback }}" placeholder="Catatan (opsional)" style="border:1.5px solid var(--navy);border-radius:8px;padding:8px 10px;font-size:14px;width:180px">
            <input type="number" name="nilai" min="0" max="100" class="nilai-input" value="{{ $k->nilai }}" placeholder="-" required title="Nilai 0-100">
            <button class="m-btn navy small">Simpan</button>
          </form>
        </div>
      @empty
        <div class="m-orang"><p class="m-muted">Belum ada mahasiswa yang mengumpulkan.</p></div>
      @endforelse

      @foreach ($belum as $mhs)
        <div class="m-orang belum">
          <div><h4>{{ $mhs->name }}</h4><p>{{ $mhs->nim_nidn }}</p></div>
          <span class="status-alpa">Belum mengumpulkan</span>
        </div>
      @endforeach
    </div>
  </div>
@endsection
