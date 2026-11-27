@extends('layouts.panel')

@section('title', 'Dashboard Dosen')

@section('content')
  @php
    $jumlahAktif = $daftarKelas->where('status', 'aktif')->count();
    $jumlahDraf = $daftarKelas->where('status', 'draft')->count();
    $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
  @endphp

  <div class="space-y-6 text-[#173d6e]">
    @include('partials.banner', [
      'label' => 'Dashboard Dosen',
      'ikon' => 'fa-chalkboard-user',
      'sapaan' => auth()->user()->name,
      'judul' => 'Selamat datang kembali!',
      'sub' => 'Buka kelas untuk melihat pengumpulan dan mengelola materi.',
    ])

    @if ($perluDinilai)
      <section aria-labelledby="judul-penilaian"
               class="flex items-start gap-3 rounded-xl border border-solid border-[#acd6fb]
                      bg-white p-4 shadow-sm">
        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-[#dfeffe]">
          <i class="fa-solid fa-clipboard-check" aria-hidden="true"></i>
        </span>

        <div class="min-w-0">
          <h2 id="judul-penilaian" class="m-0 text-base font-bold">Perlu dinilai</h2>
          <p class="mt-1 mb-0 text-sm text-slate-600">
            <strong class="tabular-nums text-[#173d6e]">{{ $perluDinilai }}</strong>
            pengumpulan tugas atau praktikum menunggu penilaian.
          </p>
        </div>
      </section>
    @endif

    <section aria-labelledby="judul-kelas">
      <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 id="judul-kelas" class="m-0 text-lg font-bold tracking-tight">Kelas Saya</h2>
          @if ($daftarKelas->isNotEmpty())
            <p class="mt-1 mb-0 text-sm text-slate-600">{{ $jumlahAktif }} aktif · {{ $jumlahDraf }} draf</p>
          @endif
        </div>

        <a href="{{ route('dosen.kelas.create') }}"
           class="{{ $fokus }} inline-flex min-h-11 items-center gap-2 rounded-lg bg-[#173d6e]
                  px-4 py-2 text-sm font-semibold text-white no-underline hover:bg-[#2b62a8]">
          <i class="fa-solid fa-plus" aria-hidden="true"></i>
          Buat Kelas
        </a>
      </div>

      <div class="grid gap-3 sm:grid-cols-2 2xl:grid-cols-3">
        @forelse ($daftarKelas as $k)
          @php
            $mataKuliah = $k->mataKuliah?->nama ?? $k->nama;
            $draf = $k->status === 'draft';
          @endphp

          <article class="relative min-w-0 rounded-xl border border-solid border-slate-200 bg-white
                          shadow-sm hover:border-[#acd6fb] [&:has(details[open])]:z-20">
            <details data-panel-dropdown class="absolute top-3 right-3 z-10">
              <summary aria-label="Pilihan kelas {{ $k->nama }}"
                       class="{{ $fokus }} grid size-10 cursor-pointer list-none place-items-center
                              rounded-lg text-[#173d6e] hover:bg-[#dfeffe] [&::-webkit-details-marker]:hidden">
                <i class="fa-solid fa-ellipsis-vertical" aria-hidden="true"></i>
              </summary>

              <div class="absolute top-full right-0 mt-1 w-44 rounded-lg border border-solid
                          border-slate-200 bg-white p-1 shadow-lg">
                <a href="{{ route('dosen.kelas.edit', $k) }}"
                   class="{{ $fokus }} flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold
                          text-[#173d6e] no-underline hover:bg-[#dfeffe]">
                  <i class="fa-solid fa-pen" aria-hidden="true"></i>
                  Ubah Kelas
                </a>

                <form method="POST" action="{{ route('dosen.kelas.destroy', $k) }}"
                      data-konfirmasi-judul="Hapus Kelas"
                      data-konfirmasi="Hapus kelas {{ $k->nama }} ({{ $mataKuliah }})? Materi, tugas, kuis, dan nilai di kelas ini ikut terhapus.">
                  @csrf
                  @method('DELETE')

                  <button type="submit"
                          class="{{ $fokus }} flex w-full cursor-pointer items-center gap-2 rounded-md
                                 border-0 bg-transparent px-3 py-2 text-left font-sans
                                 text-sm font-semibold text-red-800 hover:bg-red-50">
                    <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                    Hapus Kelas
                  </button>
                </form>
              </div>
            </details>

            <a href="{{ route('dosen.kelas.show', $k) }}"
               class="{{ $fokus }} group flex h-full min-w-0 flex-col overflow-hidden rounded-xl
                      text-[#173d6e] no-underline">
              <div class="grow p-4 pr-14 [overflow-wrap:anywhere] group-hover:bg-[#f4f7fb]">
                <div class="flex flex-wrap items-center gap-2">
                  <span @class([
                    'rounded-full px-2.5 py-1 text-xs font-semibold',
                    'bg-amber-50 text-amber-900' => $draf,
                    'bg-emerald-50 text-emerald-800' => ! $draf,
                  ])>{{ $draf ? 'Draf' : 'Aktif' }}</span>
                  <span class="text-sm text-slate-600">{{ $k->nama }}</span>
                </div>
                <h3 class="mt-2 mb-0 text-base leading-snug font-bold">{{ $mataKuliah }}</h3>
              </div>

              <div class="flex flex-wrap gap-x-4 gap-y-1 border-0 border-t border-solid
                          border-[#dbe5f1] bg-[#f4f7fb] px-4 py-2.5 text-sm text-slate-600">
                <span>
                  <i class="fa-solid fa-book-open mr-1 text-[#2b62a8]" aria-hidden="true"></i>
                  {{ $k->materi_count }} materi
                </span>
                <span>
                  <i class="fa-regular fa-file-lines mr-1 text-[#2b62a8]" aria-hidden="true"></i>
                  {{ $k->tugas_count }} tugas/praktikum
                </span>
                <span>
                  <i class="fa-solid fa-users mr-1 text-[#2b62a8]" aria-hidden="true"></i>
                  {{ $k->mahasiswa_count }} mahasiswa
                </span>
              </div>
            </a>
          </article>
        @empty
          <div class="col-span-full rounded-xl border border-dashed border-[#acd6fb] bg-white p-6 text-sm sm:p-7">
            <p class="m-0 font-semibold">Belum ada kelas.</p>
            <p class="mt-1 mb-0 text-slate-600">
              Pilih <strong>Buat Kelas</strong> untuk membuat kelas pertama.
            </p>
          </div>
        @endforelse
      </div>
    </section>
  </div>
@endsection