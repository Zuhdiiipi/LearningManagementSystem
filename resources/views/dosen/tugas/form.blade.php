@extends('layouts.panel')

@php
  $ubah = $tugas->exists;
  $jenis = old('jenis', $tugas->jenis ?? 'tugas');
  $judulHalaman = ($ubah ? 'Ubah ' : 'Tambah ') . ($jenis === 'praktikum' ? 'Praktikum' : 'Tugas');
  $kembali = route('dosen.kelas.show', [$kelas, 'tab' => 'tugas']);

  $panel = 'min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5';
  $label = 'mb-1.5 block text-sm font-semibold text-[#173d6e]';
  $input = 'min-h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2 text-base
            text-slate-900 focus:border-[#2b62a8] focus:outline-none focus:ring-2 focus:ring-[#acd6fb]
            aria-invalid:border-red-500';
  $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
  $tombol = "inline-flex min-h-11 items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold no-underline $fokus";
@endphp

@section('title', $judulHalaman)
@section('kembali', $kembali)

@section('content')
  <div class="mx-auto max-w-4xl space-y-4">
    <header class="flex items-start gap-3 rounded-xl border border-[#acd6fb] bg-[#dfeffe] p-4 sm:p-5">
      <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-white text-[#173d6e]">
        <i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>
      </span>

      <div class="min-w-0 [overflow-wrap:anywhere]">
        <h1 class="text-xl font-extrabold tracking-tight text-[#173d6e] sm:text-2xl">{{ $judulHalaman }}</h1>
        <p class="mt-1 text-sm text-slate-700">{{ $kelas->mataKuliah->nama }} · {{ $kelas->nama }}</p>
      </div>
    </header>

    <form method="POST" enctype="multipart/form-data"
          action="{{ $ubah ? route('dosen.tugas.update', $tugas) : route('dosen.tugas.store', $kelas) }}"
          class="space-y-4">
      @csrf
      @if ($ubah)
        @method('PUT')
      @endif

      <section class="{{ $panel }}" aria-labelledby="judul-pengaturan">
        <h2 id="judul-pengaturan" class="mb-4 text-base font-bold text-[#173d6e]">Pengaturan</h2>

        <div class="grid gap-4 sm:grid-cols-2">
          <div class="min-w-0 sm:col-span-2">
            <label for="judul" class="{{ $label }}">Judul</label>
            <input id="judul" name="judul" type="text" value="{{ old('judul', $tugas->judul) }}"
                   class="{{ $input }}" maxlength="150" required
                   @error('judul') aria-invalid="true" aria-describedby="error-judul" @enderror>
            @error('judul')
              <p id="error-judul" class="mt-1 text-sm text-red-700">{{ $message }}</p>
            @enderror
          </div>

          <fieldset class="min-w-0">
            <legend class="{{ $label }}">Jenis</legend>
            <div class="grid grid-cols-2 gap-2">
              @foreach (['tugas' => 'Tugas', 'praktikum' => 'Praktikum'] as $nilai => $teks)
                <label class="flex min-h-11 cursor-pointer items-center gap-2 rounded-lg border border-slate-300
                              px-2.5 py-2 text-sm font-semibold text-[#173d6e]
                              has-checked:border-[#2b62a8] has-checked:bg-[#dfeffe]">
                  <input type="radio" name="jenis" value="{{ $nilai }}" class="accent-[#173d6e]"
                         @checked($jenis === $nilai) required>
                  {{ $teks }}
                </label>
              @endforeach
            </div>
            @error('jenis')
              <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
            @enderror
          </fieldset>

          <div class="min-w-0">
            <label for="deadline" class="{{ $label }}">Batas Pengumpulan</label>
            <input id="deadline" name="deadline" type="datetime-local"
                   value="{{ old('deadline', $tugas->deadline?->format('Y-m-d\TH:i')) }}"
                   class="{{ $input }}" required
                   @error('deadline') aria-invalid="true" aria-describedby="error-deadline" @enderror>
            @error('deadline')
              <p id="error-deadline" class="mt-1 text-sm text-red-700">{{ $message }}</p>
            @enderror
          </div>
        </div>
      </section>

      <section class="{{ $panel }} space-y-4" aria-labelledby="judul-instruksi">
        <h2 id="judul-instruksi" class="text-base font-bold text-[#173d6e]">Instruksi dan Lampiran</h2>

        <div>
          <label for="instruksi" class="{{ $label }}">Instruksi untuk Mahasiswa</label>
          <textarea id="instruksi" name="instruksi" rows="4" maxlength="5000" required
                    class="{{ $input }} min-h-28 resize-y"
                    @error('instruksi') aria-invalid="true" aria-describedby="error-instruksi" @enderror>{{ old('instruksi', $tugas->instruksi) }}</textarea>
          @error('instruksi')
            <p id="error-instruksi" class="mt-1 text-sm text-red-700">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="file" class="{{ $label }}">
            Lampiran <span class="font-normal text-slate-600">(opsional)</span>
          </label>
          <input id="file" name="file" type="file"
                 aria-describedby="info-file{{ $errors->has('file') ? ' error-file' : '' }}"
                 @error('file') aria-invalid="true" @enderror
                 class="{{ $fokus }} w-full min-w-0 rounded-lg border border-slate-300 bg-white p-1 text-sm text-slate-700
                        file:mr-2 file:rounded-md file:border-0 file:bg-[#dfeffe] file:px-3 file:py-2
                        file:font-semibold file:text-[#173d6e] hover:file:bg-[#acd6fb]">
          <p id="info-file" class="mt-1 text-sm text-slate-600 [overflow-wrap:anywhere]">
            Maksimal 20 MB.
            @if ($tugas->file_name)
              Lampiran saat ini <strong>{{ $tugas->file_name }}</strong>. Kosongkan pilihan file untuk mempertahankannya.
            @endif
          </p>
          @error('file')
            <p id="error-file" class="mt-1 text-sm text-red-700">{{ $message }}</p>
          @enderror
        </div>
      </section>

      <div class="flex flex-wrap justify-end gap-2">
        <a href="{{ $kembali }}"
           class="{{ $tombol }} border border-slate-300 bg-white text-[#173d6e] hover:bg-slate-50">
          Batal
        </a>
        <button type="submit" class="{{ $tombol }} bg-[#173d6e] text-white hover:bg-[#2b62a8]">
          {{ $ubah ? 'Simpan Perubahan' : 'Simpan dan Terbitkan' }}
        </button>
      </div>
    </form>
  </div>
@endsection