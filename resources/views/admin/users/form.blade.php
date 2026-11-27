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
  $kelasInput = 'mt-1.5 block w-full rounded-lg border bg-white px-3 py-2.5
                 text-sm text-slate-900 focus:border-[#2b62a8] focus:outline-none
                 focus:ring-2 focus:ring-[#acd6fb]';
@endphp

@section('title', $judul)

@section('content')
  <h1 class="mb-5 text-2xl font-bold tracking-tight text-[#173d6e] sm:text-3xl">
    {{ $judul }}
  </h1>

  <form method="POST"
        action="{{ $ubah ? route('admin.users.update', $user) : route('admin.users.store') }}"
        class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    @csrf
    @if ($ubah)
      @method('PUT')
    @endif

    <div class="grid xl:grid-cols-[minmax(0,1fr)_20rem]">
      <section class="min-w-0 p-4 sm:p-6" aria-labelledby="data-pengguna">
        <h2 id="data-pengguna" class="text-base font-bold text-[#173d6e]">Data Pengguna</h2>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
          @foreach ($kolom as [$nama, $label, $tipe, $isianOtomatis, $wajib, $batas])
            <div @class(['min-w-0', 'sm:col-span-2' => $ubah && $nama === 'email'])>
              <label for="{{ $nama }}" class="block text-sm font-semibold text-[#173d6e]">
                {{ $label }}
              </label>

              <input id="{{ $nama }}" name="{{ $nama }}" type="{{ $tipe }}"
                     value="{{ old($nama, $user->{$nama}) }}"
                     @if ($isianOtomatis) autocomplete="{{ $isianOtomatis }}" @endif
                     @if ($wajib) required @endif
                     @if ($batas) maxlength="{{ $batas }}" @endif
                     @error($nama) aria-invalid="true" aria-describedby="{{ $nama }}-error" @enderror
                     @class([
                       $kelasInput,
                       'border-red-400' => $errors->has($nama),
                       'border-slate-200' => ! $errors->has($nama),
                     ])>

              @error($nama)
                <p id="{{ $nama }}-error" class="mt-1 text-xs text-red-700">{{ $message }}</p>
              @enderror
            </div>
          @endforeach

          @unless ($ubah)
            <div class="min-w-0">
              <label for="password" class="block text-sm font-semibold text-[#173d6e]">
                Kata sandi <span class="font-normal text-slate-500">(minimal 8 karakter)</span>
              </label>

              <input id="password" name="password" type="password" autocomplete="new-password"
                     required minlength="8"
                     @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                     @class([
                       $kelasInput,
                       'border-red-400' => $errors->has('password'),
                       'border-slate-200' => ! $errors->has('password'),
                     ])>

              @error('password')
                <p id="password-error" class="mt-1 text-xs text-red-700">{{ $message }}</p>
              @enderror
            </div>
          @endunless
        </div>
      </section>

      <section class="min-w-0 border-t border-[#acd6fb] bg-[#f1f7ff] p-4 sm:p-6
                      xl:rounded-tr-2xl xl:border-t-0 xl:border-l"
               aria-labelledby="pengaturan-akun">
        <h2 id="pengaturan-akun" class="text-base font-bold text-[#173d6e]">Pengaturan Akun</h2>

        <fieldset class="mt-4 min-w-0 border-0" @error('role') aria-describedby="role-error" @enderror>
          <legend class="text-sm font-semibold text-[#173d6e]">Peran</legend>

          <div class="mt-2 grid gap-2 sm:grid-cols-3 xl:grid-cols-1">
            @foreach ($peran as $nilai => $label)
              <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200
                            bg-white px-3 py-2 text-sm font-medium text-[#173d6e]
                            has-checked:border-[#2b62a8] has-checked:bg-[#dfeffe]">
                <input type="radio" name="role" value="{{ $nilai }}" required
                       class="size-4 shrink-0 accent-[#173d6e]"
                       @checked(old('role', $user->role ?? 'mahasiswa') === $nilai)>
                {{ $label }}
              </label>
            @endforeach
          </div>

          @error('role')
            <p id="role-error" class="mt-1 text-xs text-red-700">{{ $message }}</p>
          @enderror
        </fieldset>

        @if ($ubah)
          <div class="mt-4 flex items-center justify-between gap-2 border-t border-[#acd6fb] pt-3 text-sm">
            <span class="font-medium text-[#4d6a92]">Status akun</span>
            <span @class([
              'rounded-full px-2.5 py-1 text-xs font-semibold',
              'bg-emerald-50 text-emerald-800' => $user->is_active,
              'bg-slate-100 text-slate-700' => ! $user->is_active,
            ])>
              {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
          </div>
        @endif
      </section>
    </div>

    <div class="flex flex-wrap justify-end gap-2 border-t border-slate-100 px-4 py-4 sm:px-6">
      <a href="{{ route('admin.users.index') }}"
         class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold
                text-[#173d6e] hover:bg-[#dfeffe]">
        Batal
      </a>

      <button type="submit"
              class="rounded-lg border-0 bg-[#173d6e] px-4 py-2.5 text-sm font-semibold
                     text-white hover:bg-[#2b62a8]">
        Simpan
      </button>
    </div>
  </form>
@endsection