@extends('layouts.panel')

@section('title', $user->name)

@section('content')
  @php
    $inisial = mb_strtoupper(mb_substr(trim($user->name), 0, 1));
    $aksiStatus = $user->is_active ? 'Nonaktifkan' : 'Aktifkan';

    $informasi = [
      'Email' => $user->email,
      'NIM / NIDN' => $user->nim_nidn,
      'Telepon' => $user->telepon,
      'Terdaftar' => $user->created_at?->translatedFormat('j F Y'),
    ];

    if ($user->role === 'mahasiswa') {
      $informasi += [
        'Program Studi' => $user->program_studi,
        'Angkatan' => $user->angkatan,
        'Kelas diikuti' => $user->kelas_diikuti_count,
      ];
    } elseif ($user->role === 'dosen') {
      $informasi['Kelas diampu'] = $user->kelas_diampu_count;
    }

    $tombolSekunder = 'inline-flex w-full items-center justify-center gap-2 rounded-lg border
                       border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-semibold
                       text-[#173d6e] hover:bg-[#dfeffe]';
  @endphp

  <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold tracking-tight text-[#173d6e] sm:text-3xl">Detail Pengguna</h1>

    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-slate-200
              bg-white px-3 py-2 text-sm font-semibold text-[#173d6e] hover:bg-[#dfeffe]">
      <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Kembali
    </a>
  </div>

  <div class="grid items-start gap-4 xl:grid-cols-[minmax(0,1fr)_20rem]">
    <article class="min-w-0 rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="flex items-center gap-4 rounded-t-2xl border-b border-[#acd6fb]
                  bg-[#dfeffe] px-4 py-4 sm:px-6">
        <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-[#173d6e]
                     text-lg font-bold text-white" aria-hidden="true">
          {{ $inisial }}
        </span>

        <h2 class="min-w-0 text-lg font-bold text-[#173d6e] [overflow-wrap:anywhere]">
          {{ $user->name }}
        </h2>
      </div>

      <dl class="grid gap-x-6 gap-y-4 p-4 sm:grid-cols-2 sm:p-6">
        @foreach ($informasi as $label => $nilai)
          <div class="min-w-0">
            <dt class="text-sm font-medium text-[#4d6a92]">{{ $label }}</dt>
            <dd class="mt-1 text-sm font-semibold text-[#173d6e] [overflow-wrap:anywhere]">
              {{ $nilai === null || $nilai === '' ? '—' : $nilai }}
            </dd>
          </div>
        @endforeach
      </dl>
    </article>

    <section class="min-w-0 rounded-2xl border border-slate-200 bg-white shadow-sm"
             aria-labelledby="akun-pengguna">
      <div class="p-4 sm:p-5">
        <h2 id="akun-pengguna" class="text-base font-bold text-[#173d6e]">Akun Pengguna</h2>

        <dl class="mt-4 grid grid-cols-2 gap-3">
          <div class="rounded-lg bg-[#f1f7ff] px-3 py-2.5">
            <dt class="text-xs font-medium text-[#4d6a92]">Peran</dt>
            <dd class="mt-1 text-sm font-semibold text-[#173d6e]">{{ ucfirst($user->role) }}</dd>
          </div>

          <div class="rounded-lg bg-[#f1f7ff] px-3 py-2.5">
            <dt class="text-xs font-medium text-[#4d6a92]">Status</dt>
            <dd @class([
              'mt-1 text-sm font-semibold',
              'text-emerald-800' => $user->is_active,
              'text-slate-700' => ! $user->is_active,
            ])>
              {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
            </dd>
          </div>
        </dl>
      </div>

      <div class="grid gap-2 border-t border-slate-100 p-4 sm:p-5">
        <a href="{{ route('admin.users.edit', $user) }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#173d6e]
                  px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#2b62a8]">
          <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i> Ubah Data
        </a>

        <form method="POST" action="{{ route('admin.users.reset', $user) }}"
              data-konfirmasi="Reset kata sandi {{ $user->name }} menjadi password123?"
              onsubmit="return window.confirm(this.dataset.konfirmasi)">
          @csrf
          @method('PATCH')
          <button type="submit" class="{{ $tombolSekunder }}">Reset Kata Sandi</button>
        </form>

        @if ($user->id !== auth()->id())
          <form method="POST" action="{{ route('admin.users.toggle', $user) }}"
                data-konfirmasi="{{ $aksiStatus }} akun {{ $user->name }}?"
                onsubmit="return window.confirm(this.dataset.konfirmasi)">
            @csrf
            @method('PATCH')
            <button type="submit" class="{{ $tombolSekunder }}">{{ $aksiStatus }} Akun</button>
          </form>
        @endif
      </div>
    </section>
  </div>
@endsection