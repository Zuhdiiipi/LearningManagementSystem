@extends('layouts.panel')

@section('title', 'Kehadiran Pertemuan ' . $pertemuan->nomor)
@section('kembali', route('dosen.kelas.show', [$kelas, 'tab' => 'absensi']))

@section('content')
  <h1 class="m-title">Kehadiran</h1>
  <div class="m-box">
    <div class="m-info">
      <span><b>Pertemuan {{ $pertemuan->nomor }}</b>@if ($pertemuan->judul) · {{ $pertemuan->judul }}@endif
        · {{ $pertemuan->tanggal?->translatedFormat('l, j F Y') }}
        @if ($sesi) · {{ $sesi->dibuka_pada->format('H:i') }} - {{ $sesi->ditutup_pada->format('H:i') }} @endif</span>
      <span class="aksi-ikon">
        @if ($sesi?->sedangDibuka())
          <b style="color:#16a34a">Sedang dibuka</b>
          <form method="POST" action="{{ route('dosen.absensi.tutup', $pertemuan) }}">@csrf <button class="m-btn outline small">Tutup Sekarang</button></form>
        @else
          <span class="m-muted">{{ $sesi?->sudahDitutup() ? 'Sudah ditutup' : 'Belum dibuka' }}</span>
          <form method="POST" action="{{ route('dosen.absensi.buka', $pertemuan) }}">@csrf <button class="m-btn navy small">Buka Sekarang</button></form>
        @endif
      </span>
    </div>

    {{-- Status bisa diubah manual (misalnya mahasiswa izin/sakit), lalu klik Simpan --}}
    <form method="POST" action="{{ route('dosen.absensi.simpan', $pertemuan) }}">
      @csrf
      <div class="m-list">
        @forelse ($mahasiswa as $mhs)
          @php
            $catat = $absensi[$mhs->id] ?? null;
            $status = $catat->status ?? (($sesi && $sesi->sudahDitutup()) ? 'alpa' : '');
          @endphp
          <div class="m-orang">
            <h4>{{ $mhs->name }}</h4>
            <select name="status[{{ $mhs->id }}]" class="status-{{ $status === 'hadir' ? 'hadir' : ($status === 'alpa' ? 'alpa' : 'izin') }}">
              @if (! $status) <option value="">Belum absen</option> @endif
              @foreach (['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'] as $v => $l)
                <option value="{{ $v }}" @selected($status === $v)>{{ $l }}</option>
              @endforeach
            </select>
          </div>
        @empty
          <div class="m-orang"><p class="m-muted">Belum ada mahasiswa di kelas ini.</p></div>
        @endforelse
      </div>
      @if ($mahasiswa->count())
        <div style="text-align:right;margin-top:20px"><button class="m-btn navy">Simpan Kehadiran</button></div>
      @endif
    </form>
  </div>
@endsection
