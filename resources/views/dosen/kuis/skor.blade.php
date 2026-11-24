@extends('layouts.panel')

@section('title', 'Live Score - ' . $kuis->judul)
@section('kembali', route('dosen.kelas.show', [$kelas, 'tab' => 'kuis']))

@push('head')
  {{-- Selama quiz berlangsung, halaman memuat ulang otomatis tiap 15 detik --}}
  @if ($kuis->sedangDibuka()) <meta http-equiv="refresh" content="15"> @endif
@endpush

@section('content')
  <div class="m-title-row">
    <h1 class="m-title">Live Score</h1>
    <span class="aksi-ikon">
      @if ($kuis->sedangDibuka())
        <form method="POST" action="{{ route('dosen.kuis.tutup', $kuis) }}" onsubmit="return confirm('Tutup sesi quiz ini? Mahasiswa tidak bisa mengerjakan lagi.')">@csrf <button class="m-btn outline sesi-tutup">Tutup Sesi</button></form>
      @else
        <form method="POST" action="{{ route('dosen.kuis.buka', $kuis) }}">@csrf <button class="m-btn navy sesi-buka"><i class="fa-solid fa-play"></i> Buka Sesi</button></form>
      @endif
      <form method="POST" action="{{ route('dosen.kuis.umumkan', $kuis) }}">
        @csrf
        <button class="m-btn navy"><i class="fa-solid fa-envelope"></i> Umumkan via Email</button>
      </form>
    </span>
  </div>
  <div class="m-box">
    <div class="m-info">
      <span><b>{{ $kuis->judul }}</b> · {{ $kuis->mulai->translatedFormat('j M Y, H:i') }} · {{ $kuis->durasi_menit }} menit</span>
      <span>{{ $hasil->count() }}/{{ $mahasiswa->count() }} sudah mengerjakan
        · @if ($kuis->sedangDibuka()) <b style="color:#16a34a">Sedang dibuka sampai {{ $kuis->selesai->format('H:i') }} (diperbarui otomatis)</b> @else {{ $kuis->status() }} @endif</span>
    </div>
    <div class="m-list">
      @forelse ($mahasiswa->sortByDesc(fn ($m) => $hasil[$m->id]->skor ?? -1) as $mhs)
        @php $h = $hasil[$mhs->id] ?? null; @endphp
        <div class="m-orang {{ $h ? '' : 'belum' }}">
          <h4>{{ $mhs->name }}</h4>
          <span class="skor">{{ $h ? str_replace('.', ',', rtrim(rtrim(number_format($h->skor, 2, '.', ''), '0'), '.')) : 'Belum' }}</span>
        </div>
      @empty
        <div class="m-orang"><p class="m-muted">Belum ada mahasiswa di kelas ini.</p></div>
      @endforelse
    </div>
  </div>
@endsection
