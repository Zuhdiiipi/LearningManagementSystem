@extends('layouts.panel')

@php
  $ubah = $user->exists;
  $judul = $ubah ? 'Ubah Pengguna' : 'Tambah Pengguna';

  $kolom = [
    ['name', 'Nama Lengkap', 'text', 'name', true, 100],
    ['nim_nidn', 'NIM / NIDN', 'text', null, false, 20],
    ['email', 'Email', 'email', 'email', true, null],
  ];
  $peran = ['admin' => 'Admin', 'dosen' => 'Dosen', 'mahasiswa' => 'Mahasiswa'];

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

@section('title', $judul)

@section('content')
  <div class="mx-auto w-full max-w-xl">
    <form method="POST"
          action="{{ $ubah ? route('admin.users.update', $user) : route('admin.users.store') }}"
          class="overflow-hidden rounded-2xl border border-solid border-slate-200 bg-white shadow-sm">
      @csrf

      @if ($ubah)
        @method('PUT')
      @endif

      <header class="flex items-center gap-3 border-b border-solid border-lms-sky bg-lms-wash p-4 sm:px-6">
        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-white text-lms-navy">
          <i class="fa-solid {{ $ubah ? 'fa-user-pen' : 'fa-user-plus' }}" aria-hidden="true"></i>
        </span>

        <h1 class="m-0 text-xl font-extrabold tracking-tight text-lms-navy sm:text-2xl">
          {{ $judul }}
        </h1>
      </header>

      <div class="flex flex-col gap-4 p-4 sm:px-6 sm:py-5">
        @foreach ($kolom as [$nama, $label, $tipe, $isianOtomatis, $wajib, $batas])
          <div class="min-w-0">
            <label for="{{ $nama }}" class="{{ $labelKolom }}">{{ $label }}</label>

            <input id="{{ $nama }}" name="{{ $nama }}" type="{{ $tipe }}"
                   value="{{ old($nama, $user->{$nama}) }}"
                   @if ($isianOtomatis) autocomplete="{{ $isianOtomatis }}" @endif
                   @if ($wajib) required @endif
                   @if ($batas) maxlength="{{ $batas }}" @endif
                   @error($nama) aria-invalid="true" aria-describedby="{{ $nama }}-error" @enderror
                   class="{{ $input }}">

            @error($nama)
              <p id="{{ $nama }}-error" class="mt-1 mb-0 text-xs text-red-700">{{ $message }}</p>
            @enderror
          </div>
        @endforeach

        @unless ($ubah)
          <div class="min-w-0">
            <label for="password" class="{{ $labelKolom }}">
              Kata sandi <span class="font-normal text-slate-600">(minimal 8 karakter)</span>
            </label>

            <input id="password" name="password" type="password" autocomplete="new-password"
                   required minlength="8"
                   @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                   class="{{ $input }}">

            @error('password')
              <p id="password-error" class="mt-1 mb-0 text-xs text-red-700">{{ $message }}</p>
            @enderror
          </div>
        @endunless

        <fieldset class="m-0 min-w-0 border-0 p-0"
                  @error('role') aria-describedby="role-error" @enderror>
          <legend class="{{ $labelKolom }} p-0">Peran</legend>

          <div class="grid grid-cols-1 gap-2">
            @foreach ($peran as $nilai => $label)
              <label class="{{ $opsiRadio }}">
                <input type="radio" name="role" value="{{ $nilai }}" required
                       class="size-4 shrink-0 accent-lms-navy"
                       @checked(old('role', $user->role ?? 'mahasiswa') === $nilai)>
                {{ $label }}
              </label>
            @endforeach
          </div>

          @error('role')
            <p id="role-error" class="mt-1 mb-0 text-xs text-red-700">{{ $message }}</p>
          @enderror
        </fieldset>

        @if ($ubah)
          <div class="flex flex-wrap items-center justify-between gap-2 rounded-lg bg-lms-surface px-3 py-2.5">
            <span class="text-sm font-medium text-lms-navy">Status akun</span>
            <span @class([
              'rounded-full px-2.5 py-1 text-xs font-semibold',
              'bg-emerald-50 text-emerald-800' => $user->is_active,
              'bg-slate-100 text-slate-700' => ! $user->is_active,
            ])>
              {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
          </div>
        @endif
      </div>

      <div class="flex flex-col gap-1 border-t border-solid border-slate-200 bg-lms-surface px-4 py-3 sm:px-6">
        <button type="submit"
                class="{{ $tombol }} w-full cursor-pointer border-0 bg-lms-navy text-white hover:bg-lms-blue">
          {{ $ubah ? 'Simpan Perubahan' : 'Simpan Pengguna' }}
        </button>

        <a href="{{ route('admin.users.index') }}"
           class="{{ $tombol }} self-center text-lms-navy hover:bg-lms-wash">
          Batal
        </a>
      </div>
    </form>
  </div>
@endsection