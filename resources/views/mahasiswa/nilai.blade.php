@extends('layouts.panel')

@section('title', 'Nilai Saya')

@section('content')
  <h1 class="mb-4 text-2xl font-bold tracking-tight text-lms-navy sm:text-3xl">
    Nilai Saya
  </h1>

  <div class="min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
    <h2 class="mb-3 text-xl font-bold text-lms-navy">Semua Kelas</h2>

    @if ($daftar->isEmpty())
      <p class="rounded-xl border border-slate-200 bg-slate-50 p-3.5 text-sm text-lms-muted">
        Belum mengikuti kelas.
      </p>
    @else
      <div class="overflow-x-auto">
        <table class="w-full min-w-max border-collapse text-left text-sm
                      [&_th]:bg-lms-wash [&_th]:px-3 [&_th]:py-2 [&_th]:font-semibold [&_th]:text-lms-navy
                      [&_td]:border-t [&_td]:border-slate-100 [&_td]:px-3 [&_td]:py-2">
          <thead>
            <tr>
              <th scope="col">Mata Kuliah</th>
              <th scope="col">Tugas</th>
              <th scope="col">Praktikum</th>
              <th scope="col">Kuis</th>
              <th scope="col">Nilai Kehadiran</th>
              <th scope="col">Total</th>
              <th scope="col">Huruf</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>

          <tbody>
            @foreach ($daftar as $d)
              <tr>
                <td>
                  <strong>{{ $d['kelas']->mataKuliah->nama }}</strong><br>
                  <small>{{ $d['kelas']->nama }}</small>
                </td>
                <td>{{ $d['rekap']['tugas'] ?? 0 }}</td>
                <td>{{ $d['rekap']['praktikum'] ?? 0 }}</td>
                <td>{{ $d['rekap']['kuis'] ?? 0 }}</td>
                <td>{{ $d['rekap']['kehadiran'] ?? 0 }}/100</td>
                <td><strong>{{ $d['rekap']['akhir'] ?? 0 }}</strong></td>
                <td>
                  <span class="inline-flex rounded-full bg-lms-wash px-2.5 py-1 text-xs font-semibold text-lms-navy">
                    {{ $d['rekap']['huruf'] ?? '—' }}
                  </span>
                </td>
                <td>
                  <a href="{{ route('mahasiswa.kelas.show', [$d['kelas'], 'tab' => 'nilai']) }}"
                     class="inline-flex items-center justify-center rounded-lg bg-lms-navy
                            px-3 py-1.5 text-xs font-semibold text-white no-underline hover:bg-lms-blue">
                    Detail
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
@endsection