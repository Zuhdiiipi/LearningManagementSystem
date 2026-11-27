@extends('layouts.panel')

@php
  $ubah = $tugas->exists;
  $jenis = old('jenis', $tugas->jenis ?? 'tugas');
  $judulHalaman = ($ubah ? 'Ubah ' : 'Tambah ') . ($jenis === 'praktikum' ? 'Praktikum' : 'Tugas');
  $kembali = route('dosen.kelas.show', [$kelas, 'tab' => 'tugas']);

  $label = 'mb-1.5 block text-sm font-semibold text-lms-navy';
  $input = 'box-border block min-h-11 w-full min-w-0 rounded-lg border border-solid border-slate-300
            bg-white px-3 py-2 text-base text-slate-900 focus:border-lms-blue focus:outline-none
            focus:ring-2 focus:ring-lms-sky aria-invalid:border-red-400';
  $opsiRadio = 'flex min-h-10 cursor-pointer items-center gap-2.5 rounded-lg border border-solid
                border-slate-200 bg-white px-3 py-2 text-sm font-medium text-lms-navy
                hover:bg-slate-50 has-checked:border-lms-blue has-checked:bg-lms-wash';
  $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-lms-blue';
  $tombol = "inline-flex min-h-11 items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold no-underline $fokus";
@endphp

@section('title', $judulHalaman)
@section('kembali', $kembali)

@section('content')
  <div class="mx-auto w-full max-w-xl">
    <form method="POST" enctype="multipart/form-data"
          action="{{ $ubah ? route('dosen.tugas.update', $tugas) : route('dosen.tugas.store', $kelas) }}"
          class="overflow-hidden rounded-2xl border border-solid border-slate-200 bg-white shadow-sm">
      @csrf

      @if ($ubah)
        @method('PUT')
      @endif

      {{-- <header class="flex items-center gap-3 border-b border-solid border-lms-sky bg-lms-wash p-4 sm:px-6">
        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-white text-lms-navy">
          <i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>
        </span>

        <div class="min-w-0 [overflow-wrap:anywhere]">
          <h1 class="m-0 text-xl font-extrabold tracking-tight text-lms-navy sm:text-2xl">
            {{ $judulHalaman }}
          </h1>
          <p class="mt-1 mb-0 text-sm text-slate-700">
            {{ $kelas->mataKuliah->nama }} · {{ $kelas->nama }}
          </p>
        </div>
      </header> --}}

      <div class="flex flex-col gap-4 p-4 sm:px-6 sm:py-5">
        <div class="min-w-0">
          <label for="judul" class="{{ $label }}">Judul</label>

          <input id="judul" name="judul" type="text" value="{{ old('judul', $tugas->judul) }}"
                 class="{{ $input }}" maxlength="150" required
                 @error('judul') aria-invalid="true" aria-describedby="error-judul" @enderror>

          @error('judul')
            <p id="error-judul" class="mt-1 mb-0 text-xs text-red-700">{{ $message }}</p>
          @enderror
        </div>

        <fieldset class="m-0 min-w-0 border-0 p-0"
                  @error('jenis') aria-describedby="error-jenis" @enderror>
          <legend class="{{ $label }} p-0">Jenis</legend>

          <div class="grid grid-cols-1 gap-2">
            @foreach (['tugas' => 'Tugas', 'praktikum' => 'Praktikum'] as $nilai => $teks)
              <label class="{{ $opsiRadio }}">
                <input type="radio" name="jenis" value="{{ $nilai }}" required
                       class="size-4 shrink-0 accent-lms-navy" @checked($jenis === $nilai)>
                {{ $teks }}
              </label>
            @endforeach
          </div>

          @error('jenis')
            <p id="error-jenis" class="mt-1 mb-0 text-xs text-red-700">{{ $message }}</p>
          @enderror
        </fieldset>

        <div class="min-w-0">
          <label for="deadline" class="{{ $label }}">Batas Pengumpulan</label>

          <input id="deadline" name="deadline" type="datetime-local"
                 value="{{ old('deadline', $tugas->deadline?->format('Y-m-d\TH:i')) }}"
                 class="{{ $input }}" required
                 @error('deadline') aria-invalid="true" aria-describedby="error-deadline" @enderror>

          @error('deadline')
            <p id="error-deadline" class="mt-1 mb-0 text-xs text-red-700">{{ $message }}</p>
          @enderror
        </div>

        <div class="min-w-0">
          <label for="instruksi" class="{{ $label }}">Instruksi untuk Mahasiswa</label>

          <textarea id="instruksi" name="instruksi" rows="4" maxlength="5000" required
                    class="{{ $input }} min-h-28 resize-y"
                    @error('instruksi') aria-invalid="true" aria-describedby="error-instruksi" @enderror>{{ old('instruksi', $tugas->instruksi) }}</textarea>

          @error('instruksi')
            <p id="error-instruksi" class="mt-1 mb-0 text-xs text-red-700">{{ $message }}</p>
          @enderror
        </div>

        <div class="min-w-0">
          <label for="file" class="{{ $label }}">
            Lampiran <span class="font-normal text-slate-600">(opsional)</span>
          </label>

          <input id="file" name="file" type="file"
                 aria-describedby="info-file{{ $errors->has('file') ? ' error-file' : '' }}"
                 @error('file') aria-invalid="true" @enderror
                 class="{{ $fokus }} box-border block w-full min-w-0 rounded-lg border border-solid border-slate-300
                        bg-white p-1 text-sm text-slate-700 aria-invalid:border-red-400
                        file:mr-2 file:cursor-pointer file:rounded-md file:border-0 file:bg-lms-wash file:px-3
                        file:py-2 file:font-semibold file:text-lms-navy hover:file:bg-lms-sky">

          <div id="info-file" class="mt-1.5 text-xs text-slate-600">
            <p class="m-0">Maksimal 20 MB.</p>

            @if ($tugas->file_name)
              <p class="mt-2 mb-0 rounded-lg bg-lms-surface px-3 py-2 [overflow-wrap:anywhere]">
                Lampiran saat ini <strong class="text-lms-navy">{{ $tugas->file_name }}</strong>.
                Pilih file baru untuk mengganti lampiran.
              </p>
            @endif
          </div>

          @error('file')
            <p id="error-file" class="mt-1 mb-0 text-xs text-red-700">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="flex flex-col gap-1 border-t border-solid border-slate-200 bg-lms-surface px-4 py-3 sm:px-6">
        <button type="submit"
                class="{{ $tombol }} w-full cursor-pointer border-0 bg-lms-navy text-white hover:bg-lms-blue">
          {{ $ubah ? 'Simpan Perubahan' : 'Simpan dan Terbitkan' }}
        </button>

        <a href="{{ $kembali }}" class="{{ $tombol }} self-center text-lms-navy hover:bg-lms-wash">
          Batal
        </a>
      </div>
    </form>
  </div>
@endsection