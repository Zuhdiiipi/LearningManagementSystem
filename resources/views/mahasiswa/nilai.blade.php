@extends('layouts.panel')
@section('title', 'Nilai Saya')
@section('content')
  <h1 class="m-title">Nilai Saya</h1>
  <div class="m-box">
    <h2>Semua Kelas</h2>
    @if ($daftar->isEmpty())
      <div class="m-item"><p class="m-muted">Belum mengikuti kelas.</p></div>
    @else
      <div style="overflow-x:auto">
        <table class="m-table">
          <tr><th>Mata Kuliah</th><th>Tugas</th><th>Praktikum</th><th>Quiz</th><th>Kehadiran</th><th>Total</th><th>Huruf</th><th></th></tr>
          @foreach ($daftar as $d)
            <tr>
              <td><b>{{ $d['kelas']->mataKuliah->nama }}</b><br><small>{{ $d['kelas']->nama }}</small></td>
              <td>{{ $d['rekap']['tugas'] ?? 0 }}</td>
              <td>{{ $d['rekap']['praktikum'] ?? 0 }}</td>
              <td>{{ $d['rekap']['kuis'] ?? 0 }}</td>
              <td>{{ $d['rekap']['kehadiran'] ?? 0 }}/100</td>
              <td><b>{{ $d['rekap']['akhir'] ?? 0 }}</b></td>
              <td><span class="badge">{{ $d['rekap']['huruf'] ?? '-' }}</span></td>
              <td><a href="{{ route('mahasiswa.kelas.show', [$d['kelas'], 'tab' => 'nilai']) }}" class="m-btn navy small">Detail</a></td>
            </tr>
          @endforeach
        </table>
      </div>
    @endif
    <p class="m-note">Bobot: Tugas {{ $bobot['tugas'] }}%, Praktikum {{ $bobot['praktikum'] }}%, Quiz {{ $bobot['kuis'] }}%, Kehadiran {{ $bobot['kehadiran'] }}%.</p>
  </div>
@endsection
