@extends('layouts.panel')

@php
  $ubah = isset($pertemuan);
  $sesi = $ubah ? $pertemuan->sesiAbsensi : null;
  $judulHalaman = $ubah ? 'Ubah Pertemuan ' . $pertemuan->nomor : 'Tambah Absensi';
  $kembali = route('dosen.kelas.show', [$kelas, 'tab' => 'absensi']);
  $tanggal = old('tanggal', ($pertemuan->tanggal ?? now())->format('Y-m-d'));

  $label = 'mb-1.5 block text-sm font-semibold';
  $input = 'box-border min-h-11 min-w-0 w-full rounded-lg border border-solid border-slate-300
            bg-white px-3 py-2 font-sans text-base text-slate-900 focus:border-[#2b62a8]
            focus:outline-none focus:ring-2 focus:ring-[#acd6fb] aria-invalid:border-red-400';
  $tombol = 'inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg
             px-4 py-2 font-sans text-sm font-semibold no-underline focus-visible:outline-2
             focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
@endphp

@section('title', $judulHalaman)
@section('kembali', $kembali)

@section('content')
  <div class="mx-auto max-w-3xl space-y-4 text-[#173d6e]">
    <header class="flex items-start gap-3 rounded-xl border border-solid border-[#acd6fb] bg-[#dfeffe] p-4 sm:px-5">
      <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-white/80">
        <i class="fa-regular fa-calendar-check" aria-hidden="true"></i>
      </span>

      <div class="min-w-0 [overflow-wrap:anywhere]">
        <h1 class="m-0 text-xl leading-tight font-extrabold tracking-tight sm:text-2xl">{{ $judulHalaman }}</h1>
        <p class="mt-1 mb-0 text-sm text-slate-700">{{ $kelas->mataKuliah?->nama }} · {{ $kelas->nama }}</p>
      </div>
    </header>

    <form method="POST"
          action="{{ $ubah ? route('dosen.absensi.update', $pertemuan) : route('dosen.absensi.store', $kelas) }}"
          class="min-w-0 rounded-xl border border-solid border-slate-200 bg-white shadow-sm">
      @csrf
      @if ($ubah)
        @method('PUT')
      @endif

      <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5">
        <div class="min-w-0">
          <label for="nomor" class="{{ $label }}">Pertemuan ke</label>
          <input id="nomor" name="nomor" type="number" min="1" max="50" value="{{ old('nomor', $nomorBerikut) }}"
                 class="{{ $input }}" required
                 @error('nomor') aria-invalid="true" aria-describedby="error-nomor" @enderror>
          @error('nomor')
            <p id="error-nomor" class="mt-1 mb-0 text-sm text-red-700">{{ $message }}</p>
          @enderror
        </div>

        <div class="min-w-0">
          <label for="tanggal" class="{{ $label }}">Tanggal pertemuan</label>
          <input id="tanggal" name="tanggal" type="date" value="{{ $tanggal }}" class="{{ $input }}" required
                 aria-describedby="nama-hari{{ $errors->has('tanggal') ? ' error-tanggal' : '' }}"
                 @error('tanggal') aria-invalid="true" @enderror>
          <p id="nama-hari" aria-live="polite" class="mt-1 mb-0 text-sm text-slate-600"></p>
          @error('tanggal')
            <p id="error-tanggal" class="mt-1 mb-0 text-sm text-red-700">{{ $message }}</p>
          @enderror
        </div>

        <div class="min-w-0">
          <label for="waktu_buka" class="{{ $label }}">Waktu Buka</label>
          <input id="waktu_buka" name="waktu_buka" type="time"
                 value="{{ old('waktu_buka', $sesi?->dibuka_pada->format('H:i') ?? '10:30') }}" class="{{ $input }}" required
                 @error('waktu_buka') aria-invalid="true" aria-describedby="error-waktu-buka" @enderror>
          @error('waktu_buka')
            <p id="error-waktu-buka" class="mt-1 mb-0 text-sm text-red-700">{{ $message }}</p>
          @enderror
        </div>

        <div class="min-w-0">
          <label for="waktu_tutup" class="{{ $label }}">Waktu Tutup</label>
          <input id="waktu_tutup" name="waktu_tutup" type="time"
                 value="{{ old('waktu_tutup', $sesi?->ditutup_pada->format('H:i') ?? '12:00') }}" class="{{ $input }}" required
                 @error('waktu_tutup') aria-invalid="true" aria-describedby="error-waktu-tutup" @enderror>
          @error('waktu_tutup')
            <p id="error-waktu-tutup" class="mt-1 mb-0 text-sm text-red-700">{{ $message }}</p>
          @enderror
        </div>

        <div class="min-w-0 sm:col-span-2">
          <label for="judul" class="{{ $label }}">
            Topik pertemuan <span class="font-normal text-slate-600">(opsional)</span>
          </label>
          <input id="judul" name="judul" type="text" maxlength="150" value="{{ old('judul', $pertemuan->judul ?? '') }}"
                 placeholder="Contoh: Migration dan Seeder" class="{{ $input }}"
                 @error('judul') aria-invalid="true" aria-describedby="error-judul" @enderror>
          @error('judul')
            <p id="error-judul" class="mt-1 mb-0 text-sm text-red-700">{{ $message }}</p>
          @enderror
        </div>

        <p class="m-0 rounded-lg bg-[#f4f7fb] p-3 text-sm text-slate-700 sm:col-span-2">
          Mahasiswa dapat menandai hadir selama sesi absensi terbuka.
        </p>
      </div>

      <div class="flex flex-wrap justify-end gap-2 border-0 border-t border-solid border-slate-100 px-4 py-3 sm:px-5">
        <a href="{{ $kembali }}"
           class="{{ $tombol }} border border-solid border-slate-200 bg-white text-[#173d6e] hover:bg-[#f4f7fb]">
          Batal
        </a>
        <button type="submit" class="{{ $tombol }} border-0 bg-[#173d6e] text-white hover:bg-[#2b62a8]">
          {{ $ubah ? 'Simpan perubahan' : 'Simpan jadwal' }}
        </button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
  <script>
    (() => {
      const tanggal = document.getElementById('tanggal');
      const namaHari = document.getElementById('nama-hari');
      const format = new Intl.DateTimeFormat('id-ID', { weekday: 'long', timeZone: 'UTC' });

      const tampilkanHari = () => {
        namaHari.textContent = tanggal.valueAsDate ? format.format(tanggal.valueAsDate) : '';
      };

      tanggal.addEventListener('input', tampilkanHari);
      tampilkanHari();
    })();
  </script>
@endpush