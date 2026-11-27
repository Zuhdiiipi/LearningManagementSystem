@extends('layouts.panel')

@section('title', 'Kelola Kelas')

@section('content')
  <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
    <div>
      <h1 class="text-2xl font-bold tracking-tight text-[#173d6e] sm:text-3xl">Kelola Kelas</h1>
      <p class="mt-1 text-sm text-slate-600">
        {{ number_format($kelas->count(), 0, ',', '.') }} kelas ditampilkan
      </p>
    </div>

    <form method="GET" action="{{ route('admin.kelas.index') }}"
          class="flex w-full flex-wrap items-center gap-2 lg:w-auto">
      <label for="cari-kelas" class="sr-only">Cari kelas, mata kuliah, atau dosen</label>
      <input id="cari-kelas" name="cari" type="search" value="{{ request('cari') }}"
             placeholder="Cari kelas, mata kuliah, atau dosen"
             class="min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm
                    focus:border-[#2b62a8] focus:outline-none focus:ring-2 focus:ring-[#acd6fb] lg:w-72">

      <button type="submit"
              class="rounded-lg bg-[#173d6e] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#2b62a8]">
        Cari
      </button>

      @if (request()->filled('cari'))
        <a href="{{ route('admin.kelas.index') }}"
           class="px-2 py-2.5 text-sm font-semibold text-[#2b62a8] hover:underline">
          Reset
        </a>
      @endif
    </form>
  </div>

  <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @forelse ($kelas as $k)
      <article class="flex min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-[#acd6fb] bg-[#dfeffe] p-4">
          <div class="min-w-0">
            <p class="text-xs font-semibold text-[#4d6a92]">{{ $k->mataKuliah?->kode ?? '—' }}</p>
            <h2 class="mt-1 text-lg font-bold leading-snug text-[#173d6e] [overflow-wrap:anywhere]">
              {{ $k->mataKuliah?->nama ?? 'Mata kuliah belum tersedia' }}
            </h2>
          </div>

          <span @class([
            'shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold',
            'bg-emerald-50 text-emerald-800' => $k->status === 'aktif',
            'bg-amber-50 text-amber-800' => $k->status !== 'aktif',
          ])>{{ ucfirst($k->status) }}</span>
        </div>

        <dl class="grid gap-3 p-4 text-sm">
          @foreach (['Kelas' => $k->nama, 'Dosen' => $k->dosen?->name ?? '—'] as $label => $nilai)
            <div>
              <dt class="text-xs font-medium text-slate-600">{{ $label }}</dt>
              <dd class="mt-0.5 font-semibold text-[#173d6e] [overflow-wrap:anywhere]">{{ $nilai }}</dd>
            </div>
          @endforeach
        </dl>

        <div class="mt-auto flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 px-4 py-3">
          <span class="inline-flex items-center gap-2 text-sm text-slate-600">
            <i class="fa-solid fa-users text-[#4d6a92]" aria-hidden="true"></i>
            {{ $k->mahasiswa_count }} anggota
          </span>

          <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.kelas.show', $k) }}"
               class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-semibold text-[#173d6e] hover:bg-[#dfeffe]">
              Lihat
            </a>
            <a href="{{ route('admin.kelas.edit', $k) }}"
               class="rounded-lg bg-[#173d6e] px-3 py-1.5 text-sm font-semibold text-white hover:bg-[#2b62a8]">
              Ubah
            </a>
          </div>
        </div>
      </article>
    @empty
      <p class="col-span-full rounded-2xl border border-dashed border-[#acd6fb] bg-white px-4 py-8 text-center text-sm text-slate-600">
        {{ request()->filled('cari') ? 'Tidak ada kelas yang cocok dengan pencarian.' : 'Belum ada kelas.' }}
      </p>
    @endforelse
  </div>
@endsection