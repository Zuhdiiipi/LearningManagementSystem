@extends('layouts.panel')

@section('title', 'Kelola Pengguna')

@section('content')
  @php
    $sensor = static function (?string $email): string {
      [$nama, $domain] = array_pad(explode('@', (string) $email, 2), 2, '');

      return $nama !== '' && $domain !== ''
        ? mb_substr($nama, 0, 4) . '********@' . $domain
        : '—';
    };

    $tombolIkon = 'inline-flex size-8 items-center justify-center rounded-lg border-0 bg-transparent
                   text-[#173d6e] hover:bg-[#dfeffe] focus-visible:outline-2
                   focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
  @endphp

  <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-2xl font-bold tracking-tight text-[#173d6e] sm:text-3xl">
        Kelola Pengguna
      </h1>
      <p class="mt-1 text-sm text-slate-600">
        {{ number_format($users->count(), 0, ',', '.') }} pengguna ditampilkan
      </p>
    </div>

    <a href="{{ route('admin.users.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-[#173d6e] px-4 py-2.5
              text-sm font-semibold text-white hover:bg-[#2b62a8]">
      <i class="fa-solid fa-plus" aria-hidden="true"></i>
      Tambah Pengguna
    </a>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <form method="GET" action="{{ route('admin.users.index') }}"
          class="flex flex-wrap items-center gap-2 border-b border-slate-100 p-4">
      <div class="relative w-full min-w-0 sm:w-auto sm:min-w-52 sm:flex-1">
        <label for="cari" class="sr-only">Cari nama, NIM, atau email</label>

        <i class="fa-solid fa-magnifying-glass pointer-events-none absolute top-1/2 left-3
                  -translate-y-1/2 text-sm text-slate-400" aria-hidden="true"></i>

        <input id="cari" type="search" name="cari" value="{{ request('cari') }}"
               placeholder="Cari nama / NIM / email"
               class="w-full rounded-lg border border-slate-200 bg-white py-2.5 pr-3 pl-9
                      text-sm focus:border-[#2b62a8] focus:outline-none focus:ring-2
                      focus:ring-[#acd6fb]">
      </div>

      <label for="filter-role" class="sr-only">Filter peran</label>
      <select id="filter-role" name="role" onchange="this.form.requestSubmit()"
              class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700">
        <option value="">Semua peran</option>

        @foreach (['admin' => 'Admin', 'dosen' => 'Dosen', 'mahasiswa' => 'Mahasiswa'] as $nilai => $label)
          <option value="{{ $nilai }}" @selected(request('role') === $nilai)>
            {{ $label }}
          </option>
        @endforeach
      </select>

      <button type="submit"
              class="rounded-lg border-0 bg-[#dfeffe] px-4 py-2.5
                     text-sm font-semibold text-[#173d6e] hover:bg-[#acd6fb]">
        Cari
      </button>

      @if (request()->filled('cari') || request()->filled('role'))
        <a href="{{ route('admin.users.index') }}"
           class="px-2 py-2 text-sm font-semibold text-[#2b62a8] hover:underline">
          Reset filter
        </a>
      @endif
    </form>

    <div class="overflow-x-auto rounded-b-2xl" tabindex="0" role="region" aria-label="Tabel pengguna">
      <table class="w-full min-w-[48rem] border-collapse text-left text-sm">
        <thead class="bg-[#dfeffe] text-[#173d6e]">
          <tr>
            <th scope="col" class="w-12 px-3 py-3 text-center font-semibold">No.</th>
            <th scope="col" class="px-3 py-3 font-semibold">Nama</th>
            <th scope="col" class="px-3 py-3 font-semibold">NIM / NIDN</th>
            <th scope="col" class="px-3 py-3 font-semibold">Email</th>
            <th scope="col" class="px-3 py-3 font-semibold">Peran</th>
            <th scope="col" class="px-3 py-3 font-semibold">Status</th>
            <th scope="col" class="px-3 py-3 text-right font-semibold">Aksi</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          @forelse ($users as $u)
            @php $aksiStatus = $u->is_active ? 'Nonaktifkan' : 'Aktifkan'; @endphp

            <tr class="hover:bg-slate-50">
              <td class="px-3 py-3 text-center tabular-nums text-slate-500">
                {{ $loop->iteration }}
              </td>

              <th scope="row" class="px-3 py-3 font-semibold text-[#173d6e] [overflow-wrap:anywhere]">
                {{ $u->name }}
              </th>

              <td class="px-3 py-3 text-slate-600">{{ $u->nim_nidn ?? '—' }}</td>
              <td class="px-3 py-3 text-slate-600 [overflow-wrap:anywhere]">{{ $sensor($u->email) }}</td>

              <td class="px-3 py-3">
                <span class="inline-flex rounded-full bg-[#dfeffe] px-2.5 py-1
                             text-xs font-semibold text-[#173d6e]">
                  {{ ucfirst($u->role) }}
                </span>
              </td>

              <td class="px-3 py-3">
                <span @class([
                  'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                  'bg-emerald-50 text-emerald-800' => $u->is_active,
                  'bg-slate-100 text-slate-600' => ! $u->is_active,
                ])>
                  {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>

              <td class="px-3 py-3">
                <div class="flex items-center justify-end gap-0.5">
                  <a href="{{ route('admin.users.show', $u) }}" class="{{ $tombolIkon }}"
                     title="Lihat" aria-label="Lihat {{ $u->name }}">
                    <i class="fa-regular fa-eye" aria-hidden="true"></i>
                  </a>

                  <a href="{{ route('admin.users.edit', $u) }}" class="{{ $tombolIkon }}"
                     title="Ubah" aria-label="Ubah {{ $u->name }}">
                    <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                  </a>

                  <form method="POST" action="{{ route('admin.users.reset', $u) }}"
                        data-konfirmasi-judul="Reset Kata Sandi"
                        data-konfirmasi="Kata sandi akun {{ $u->name }} akan dikembalikan ke kata sandi bawaan.">
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="{{ $tombolIkon }}"
                            title="Reset kata sandi" aria-label="Reset kata sandi {{ $u->name }}">
                      <i class="fa-solid fa-key" aria-hidden="true"></i>
                    </button>
                  </form>

                  @if ($u->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.toggle', $u) }}"
                          data-konfirmasi-judul="{{ $aksiStatus }} Akun"
                          data-konfirmasi="{{ $aksiStatus }} akun {{ $u->name }}?">
                      @csrf
                      @method('PATCH')

                      <button type="submit" class="{{ $tombolIkon }}"
                              title="{{ $aksiStatus }} akun"
                              aria-label="{{ $aksiStatus }} akun {{ $u->name }}">
                        <i class="fa-solid fa-power-off {{ $u->is_active ? 'text-red-700' : 'text-emerald-700' }}"
                           aria-hidden="true"></i>
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-4 py-10 text-center text-slate-600">
                Tidak ada pengguna.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection