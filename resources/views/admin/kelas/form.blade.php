@extends('layouts.panel')

@php
  $pilihanKelas = [
    'mata_kuliah_id' => ['Mata Kuliah', $matkul->mapWithKeys(fn ($m) => [$m->id => $m->kode . ' — ' . $m->nama])],
    'dosen_id' => ['Dosen Pengampu', $dosen->pluck('name', 'id')],
    'tahun_ajaran_id' => ['Tahun Akademik', $tahun->pluck('nama', 'id')],
  ];
  $pengaturan = [
    'semester' => ['Semester', ['ganjil' => 'Ganjil', 'genap' => 'Genap']],
    'status' => ['Status Kelas', ['aktif' => 'Aktif', 'draft' => 'Draf']],
  ];

  $labelKolom = 'mb-1.5 block text-sm font-semibold text-lms-navy';
  $input = 'box-border block min-h-11 w-full min-w-0 rounded-lg border border-solid border-slate-300
            bg-white px-3 py-2 text-base text-slate-900 focus:border-lms-blue focus:outline-none
            focus:ring-2 focus:ring-lms-sky aria-invalid:border-red-400';
  $opsiRadio = 'flex min-h-10 cursor-pointer items-center gap-2.5 rounded-lg border border-solid
                border-slate-200 bg-white px-3 py-2 text-sm font-medium text-lms-navy
                hover:bg-slate-50 has-checked:border-lms-blue has-checked:bg-lms-wash';
  $tombol = 'inline-flex min-h-11 items-center justify-center rounded-lg px-4 py-2 text-sm
             font-semibold no-underline focus-visible:outline-2 focus-visible:outline-offset-2
             focus-visible:outline-lms-blue';
@endphp

@section('title', 'Ubah Kelas')

@section('content')
  <div class="mx-auto w-full max-w-xl">
    <form method="POST" action="{{ route('admin.kelas.update', $kelas) }}"
          class="overflow-hidden rounded-2xl border border-solid border-slate-200 bg-white shadow-sm">
      @csrf
      @method('PUT')

      <header class="flex items-center gap-3 border-b border-solid border-lms-sky bg-lms-wash p-4 sm:px-6">
        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-white text-lms-navy">
          <i class="fa-solid fa-chalkboard-user" aria-hidden="true"></i>
        </span>

        <h1 class="m-0 text-xl font-extrabold tracking-tight text-lms-navy sm:text-2xl">
          Ubah Kelas
        </h1>
      </header>

      <div class="flex flex-col gap-4 p-4 sm:px-6 sm:py-5">
        <div class="min-w-0">
          <label for="nama" class="{{ $labelKolom }}">Nama Kelas</label>

          <input id="nama" name="nama" type="text" value="{{ old('nama', $kelas->nama) }}"
                 required maxlength="100" class="{{ $input }}"
                 @error('nama') aria-invalid="true" aria-describedby="nama-error" @enderror>

          @error('nama')
            <p id="nama-error" class="mt-1 mb-0 text-xs text-red-700">{{ $message }}</p>
          @enderror
        </div>

        @foreach ($pilihanKelas as $field => [$label, $opsi])
          <div class="min-w-0">
            <label for="{{ $field }}" class="{{ $labelKolom }}">{{ $label }}</label>

            <select id="{{ $field }}" name="{{ $field }}" required class="{{ $input }}"
                    @error($field) aria-invalid="true" aria-describedby="{{ $field }}-error" @enderror>
              <option value="">Pilih {{ strtolower($label) }}</option>

              @foreach ($opsi as $id => $teks)
                <option value="{{ $id }}" @selected((string) old($field, $kelas->{$field}) === (string) $id)>
                  {{ $teks }}
                </option>
              @endforeach
            </select>

            @error($field)
              <p id="{{ $field }}-error" class="mt-1 mb-0 text-xs text-red-700">{{ $message }}</p>
            @enderror
          </div>
        @endforeach

        @foreach ($pengaturan as $field => [$label, $opsi])
          <fieldset class="m-0 min-w-0 border-0 p-0"
                    @error($field) aria-describedby="{{ $field }}-error" @enderror>
            <legend class="{{ $labelKolom }} p-0">{{ $label }}</legend>

            <div class="grid grid-cols-1 gap-2">
              @foreach ($opsi as $nilai => $teks)
                <label class="{{ $opsiRadio }}">
                  <input type="radio" name="{{ $field }}" value="{{ $nilai }}" required
                         class="size-4 shrink-0 accent-lms-navy"
                         @checked(old($field, $kelas->{$field}) === $nilai)>
                  {{ $teks }}
                </label>
              @endforeach
            </div>

            @error($field)
              <p id="{{ $field }}-error" class="mt-1 mb-0 text-xs text-red-700">{{ $message }}</p>
            @enderror
          </fieldset>
        @endforeach
      </div>

      <div class="flex flex-col gap-1 border-t border-solid border-slate-200 bg-lms-surface px-4 py-3 sm:px-6">
        <button type="submit"
                class="{{ $tombol }} w-full cursor-pointer border-0 bg-lms-navy text-white hover:bg-lms-blue">
          Simpan Perubahan
        </button>

        <a href="{{ route('admin.kelas.index') }}"
           class="{{ $tombol }} self-center text-lms-navy hover:bg-lms-wash">
          Batal
        </a>
      </div>
    </form>
  </div>
@endsection