@extends('layouts.panel')

@section('title', 'Profil')

@php
  $peran = $user->role;
  $dosen = $peran === 'dosen';
  $identitas = $dosen ? 'NIDN' : 'NIM';

  $informasi = [
    $identitas => $user->nim_nidn,
    'Program Studi' => $user->program_studi,
    'Fakultas' => $user->fakultas,
    'Email' => $user->email,
    'No. Telepon' => $user->telepon,
  ];

  $isian = [
    'name' => ['Nama Lengkap', 'text', 100, true],
    'email' => ['Email', 'email', 254, true],
    'fakultas' => ['Fakultas', 'text', 100, false],
    'program_studi' => ['Program Studi', 'text', 100, false],
    'telepon' => ['No. Telepon', 'tel', 20, false],
  ];

  if (! $dosen) {
      $informasi['Angkatan'] = $user->angkatan;
      $isian['angkatan'] = ['Angkatan', 'text', 4, false];
  }

  $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
  $tombol = "inline-flex min-h-10 cursor-pointer items-center justify-center gap-2 rounded-lg border px-3 py-2 text-sm font-semibold $fokus";
  $utama = 'border-[#173d6e] bg-[#173d6e] text-white hover:bg-[#2b62a8]';
  $sekunder = 'border-slate-300 bg-white text-[#173d6e] hover:bg-slate-50';
  $panel = 'min-w-0 rounded-xl border border-slate-200 bg-white shadow-sm';
@endphp

@section('content')
  <div class="mx-auto w-full max-w-5xl space-y-4">
    <header class="flex flex-col gap-4 rounded-xl border border-[#acd6fb] bg-[#dfeffe] p-4
                   sm:flex-row sm:items-center sm:justify-between sm:p-6">
      <div class="flex min-w-0 items-center gap-4">
        @if ($user->foto)
          <img src="{{ route('file.foto', $user) }}" alt=""
               class="h-20 w-20 shrink-0 rounded-full border-2 border-white object-cover shadow-sm">
        @else
          <span aria-hidden="true" class="grid h-20 w-20 shrink-0 place-items-center rounded-full
                                         border-2 border-white bg-[#173d6e] text-2xl font-bold text-white shadow-sm">
            {{ \App\Support\Waktu::inisial($user->name) }}
          </span>
        @endif

        <div class="min-w-0">
          <p class="mb-1 text-sm font-semibold text-[#4d6a92]">{{ $dosen ? 'Dosen' : 'Mahasiswa' }}</p>
          <h1 class="text-xl font-extrabold leading-tight tracking-tight text-[#173d6e] [overflow-wrap:anywhere] sm:text-2xl">
            {{ $user->name }}
          </h1>

          @if ($dosen)
            <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                         {{ $user->is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-white text-slate-600' }}">
              {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
          @endif
        </div>
      </div>

      <div class="flex shrink-0 flex-wrap items-center gap-2">
        <form method="POST" action="{{ route($peran . '.profil.foto') }}" enctype="multipart/form-data">
          @csrf
          <input id="input-foto" type="file" name="foto" accept="image/*" hidden
                 onchange="if (this.files.length) this.form.submit()">
          <button type="button" class="{{ $tombol }} {{ $sekunder }}"
                  onclick="document.getElementById('input-foto').click()">
            <i class="fa-solid fa-camera" aria-hidden="true"></i> Ubah Foto
          </button>
        </form>
      </div>
    </header>

    <div class="grid items-start gap-4 lg:grid-cols-3">
      <section class="{{ $panel }} lg:col-span-2" aria-labelledby="judul-informasi">
        <h2 id="judul-informasi"
            class="border-b border-slate-100 px-4 py-3 text-base font-bold text-[#173d6e] sm:px-5">
          Informasi Akademik
        </h2>

        <dl class="grid gap-x-5 gap-y-4 p-4 sm:grid-cols-2 sm:p-5">
          @foreach ($informasi as $label => $nilai)
            <div class="min-w-0">
              <dt class="text-sm text-slate-600">{{ $label }}</dt>
              <dd class="mt-1 text-sm font-semibold text-slate-900 [overflow-wrap:anywhere]">
                {{ filled($nilai) ? $nilai : '—' }}
              </dd>
            </div>
          @endforeach
        </dl>

        <details class="group border-t border-slate-200"
                 @if ($errors->hasAny(array_keys($isian))) open @endif>
          <summary class="flex cursor-pointer list-none items-center justify-between gap-3
                          px-4 py-3 text-sm font-semibold text-[#173d6e] hover:bg-slate-50
                          [&::-webkit-details-marker]:hidden sm:px-5 {{ $fokus }}">
            <span class="inline-flex items-center gap-2">
              <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i> Ubah Data
            </span>
            <i class="fa-solid fa-chevron-down text-xs group-open:rotate-180" aria-hidden="true"></i>
          </summary>

          <form method="POST" action="{{ route($peran . '.profil.update') }}"
                class="w-full max-w-lg space-y-4 px-4 py-4 sm:px-5">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-3">
              @foreach ($isian as $nama => [$label, $tipe, $panjang, $wajib])
                @php $nilai = old($nama, $user->{$nama}); @endphp

                <div class="min-w-0">
                  <label for="profil-{{ $nama }}" class="mb-0.5 block text-sm font-semibold text-[#173d6e]">
                    {{ $label }}
                  </label>

                  <input id="profil-{{ $nama }}" type="{{ $tipe }}" name="{{ $nama }}"
                         value="{{ is_scalar($nilai) ? $nilai : '' }}" maxlength="{{ $panjang }}"
                         @required($wajib)
                         @if ($nama === 'angkatan') inputmode="numeric" @endif
                         @error($nama) aria-invalid="true" aria-describedby="galat-{{ $nama }}" @enderror
                         class="w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm
                                aria-invalid:border-red-500 {{ $fokus }}">

                  @error($nama)
                    <p id="galat-{{ $nama }}" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                  @enderror
                </div>
              @endforeach

              <p class="text-xs text-slate-600">{{ $identitas }} hanya bisa diubah oleh Admin.</p>
            </div>

            <div class="flex flex-wrap justify-end gap-2 border-t border-slate-100 pt-3">
              <button type="button" class="{{ $tombol }} {{ $sekunder }}"
                      onclick="this.form.reset(); const panel = this.closest('details'); panel.open = false; panel.querySelector('summary').focus()">
                Batal
              </button>
              <button type="submit" class="{{ $tombol }} {{ $utama }}">Simpan</button>
            </div>
          </form>
        </details>
      </section>

      <section class="{{ $panel }}" aria-labelledby="judul-statistik">
        <h2 id="judul-statistik"
            class="border-b border-slate-100 px-4 py-3 text-base font-bold text-[#173d6e]">
          Statistik Singkat
        </h2>

        <dl class="grid grid-cols-2 gap-3 p-4">
          @foreach ($statistik as $label => $angka)
            <div class="min-w-0 rounded-lg bg-[#f4f7fb] px-3 py-2.5">
              <dt class="text-xs font-medium text-slate-600 [overflow-wrap:anywhere]">{{ $label }}</dt>
              <dd class="mt-1 text-2xl font-bold tabular-nums text-[#173d6e]">{{ $angka }}</dd>
            </div>
          @endforeach
        </dl>
      </section>
    </div>
  </div>
@endsection