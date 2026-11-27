@extends('layouts.panel')

@section('title', $kelas->mataKuliah?->nama ?? 'Detail Kelas')

@section('content')
  @php
    $mataKuliah = $kelas->mataKuliah;

    $informasi = [
      'Kode Kelas' => $kelas->kode_kelas,
      'Mata Kuliah' => $mataKuliah ? $mataKuliah->kode . ' — ' . $mataKuliah->nama : '—',
      'Program Studi' => $mataKuliah?->programStudi?->nama ?? '—',
      'Dosen Pengampu' => $kelas->dosen?->name ?? '—',
      'Tahun Akademik' => $kelas->tahunAjaran?->nama ?? '—',
      'Semester' => ucfirst($kelas->semester),
    ];

    $ringkasan = [
      'Materi' => $kelas->materi_count,
      'Tugas/Praktikum' => $kelas->tugas_count,
      'Kuis' => $kelas->kuis_count,
      'Absensi' => $kelas->pertemuan_count,
    ];
  @endphp

  <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold tracking-tight text-[#173d6e] sm:text-3xl">Detail Kelas</h1>

    <div class="flex flex-wrap gap-2">
      <a href="{{ route('admin.kelas.index') }}"
         class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2
                text-sm font-semibold text-[#173d6e] hover:bg-[#dfeffe]">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Kembali
      </a>
      <a href="{{ route('admin.kelas.edit', $kelas) }}"
         class="inline-flex items-center gap-2 rounded-lg bg-[#173d6e] px-3 py-2
                text-sm font-semibold text-white hover:bg-[#2b62a8]">
        <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i> Ubah Kelas
      </a>
    </div>
  </div>

  <div class="grid items-start gap-4 xl:grid-cols-[minmax(0,1fr)_20rem]">
    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
             aria-labelledby="identitas-kelas">
      <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#acd6fb] bg-[#dfeffe] p-4 sm:px-6">
        <h2 id="identitas-kelas" class="text-lg font-bold text-[#173d6e] [overflow-wrap:anywhere]">
          {{ $kelas->nama }}
        </h2>

        <span @class([
          'rounded-full px-2.5 py-1 text-xs font-semibold',
          'bg-emerald-50 text-emerald-800' => $kelas->status === 'aktif',
          'bg-amber-50 text-amber-800' => $kelas->status !== 'aktif',
        ])>{{ ucfirst($kelas->status) }}</span>
      </div>

      <dl class="grid gap-x-6 gap-y-4 p-4 sm:grid-cols-2 sm:p-6">
        @foreach ($informasi as $label => $nilai)
          <div class="min-w-0">
            <dt class="text-sm font-medium text-[#4d6a92]">{{ $label }}</dt>
            <dd class="mt-1 text-sm font-semibold text-[#173d6e] [overflow-wrap:anywhere]">{{ $nilai }}</dd>
          </div>
        @endforeach
      </dl>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"
             aria-labelledby="isi-kelas">
      <h2 id="isi-kelas" class="text-base font-bold text-[#173d6e]">Isi Kelas</h2>

      <dl class="mt-3 grid grid-cols-2 gap-2">
        @foreach ($ringkasan as $label => $jumlah)
          <div class="min-w-0 rounded-lg bg-[#f1f7ff] p-3">
            <dt class="text-xs font-medium text-[#4d6a92] [overflow-wrap:anywhere]">{{ $label }}</dt>
            <dd class="mt-1 text-2xl font-bold tabular-nums text-[#173d6e]">{{ $jumlah }}</dd>
          </div>
        @endforeach
      </dl>
    </section>
  </div>

  <section class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
           aria-labelledby="anggota-kelas">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-4 py-3 sm:px-6">
      <h2 id="anggota-kelas" class="text-base font-bold text-[#173d6e]">Anggota</h2>
      <span class="rounded-full bg-[#dfeffe] px-2.5 py-1 text-xs font-semibold text-[#173d6e]">
        {{ $kelas->mahasiswa->count() }} mahasiswa
      </span>
    </div>

    @if ($kelas->mahasiswa->isEmpty())
      <p class="px-4 py-6 text-center text-sm text-slate-600">Belum ada mahasiswa.</p>
    @else
      <table class="w-full table-fixed text-left text-sm">
        <thead class="bg-slate-50 text-[#4d6a92]">
          <tr>
            <th scope="col" class="w-12 px-3 py-2.5 text-center font-semibold">No.</th>
            <th scope="col" class="px-3 py-2.5 font-semibold">Nama</th>
            <th scope="col" class="w-2/5 px-3 py-2.5 font-semibold sm:w-1/3">NIM / NIDN</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach ($kelas->mahasiswa as $m)
            <tr>
              <td class="px-3 py-2.5 text-center tabular-nums text-slate-600">{{ $loop->iteration }}</td>
              <th scope="row" class="px-3 py-2.5 font-semibold text-[#173d6e] [overflow-wrap:anywhere]">
                {{ $m->name }}
              </th>
              <td class="px-3 py-2.5 text-slate-600 [overflow-wrap:anywhere]">{{ $m->nim_nidn ?? '—' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </section>
@endsection