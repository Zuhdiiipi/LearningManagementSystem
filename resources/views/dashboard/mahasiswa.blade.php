@extends('layouts.panel')

@section('title', 'Dashboard Mahasiswa')

@section('content')
  @php
    $tugasTerdekat = $tugasBelum->first();
    $kuisAktif = $kuisTersedia->first();
    $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
    $kartu = 'rounded-xl border border-solid border-slate-200 bg-white shadow-sm';
  @endphp

  <div class="space-y-6 text-[#173d6e] [&_a]:no-underline">
    <section aria-labelledby="judul-dashboard"
             class="relative isolate overflow-hidden rounded-2xl bg-[#173d6e] px-5 py-6 text-white
                    sm:px-7 sm:py-7">
      <div aria-hidden="true"
           class="absolute inset-y-0 right-0 -z-10 hidden w-2/5 sm:block">
        <img src="{{ asset('images/banner-mahasiswa.png') }}" alt=""
             class="absolute inset-0 size-full object-cover object-right">
        <div class="absolute inset-0 bg-linear-to-r from-[#173d6e] to-transparent"></div>
      </div>

      <div class="sm:max-w-[60%] [overflow-wrap:anywhere]">
        <p class="m-0 text-sm font-semibold text-[#acd6fb]">Dashboard Mahasiswa</p>
        <h1 id="judul-dashboard" class="mt-1 mb-0 text-2xl font-extrabold tracking-tight sm:text-3xl">
          Halo, {{ auth()->user()->name }}
        </h1>
        <p class="mt-2 mb-0 text-sm leading-relaxed text-[#dfeffe]">
          Lihat tugas dan kuis yang tersedia, lalu lanjutkan dari kelasmu.
        </p>
      </div>
    </section>

    @if ($tugasTerdekat || $kuisAktif)
      <section aria-labelledby="judul-pengingat">
        <h2 id="judul-pengingat" class="mt-0 mb-3 text-lg font-bold tracking-tight">Pengingat</h2>

        <div class="grid gap-3 {{ $tugasTerdekat && $kuisAktif ? 'sm:grid-cols-2' : '' }}">
          @if ($tugasTerdekat)
            <a href="{{ route('mahasiswa.tugas.show', $tugasTerdekat) }}"
               class="{{ $kartu }} {{ $fokus }} group flex min-w-0 items-start gap-3
                      p-4 text-[#173d6e] hover:border-[#acd6fb] hover:bg-[#f4f7fb]">
              <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-[#dfeffe]">
                <i class="fa-regular fa-file-lines" aria-hidden="true"></i>
              </span>

              <div class="min-w-0 flex-1 [overflow-wrap:anywhere]">
                <p class="m-0 text-sm text-slate-600">{{ $tugasBelum->count() }} tugas belum dikumpulkan</p>
                <p class="mt-1 mb-0 font-bold">{{ $tugasTerdekat->judul }}</p>
                <p class="mt-1 mb-0 text-sm text-slate-600">
                  Tenggat terdekat
                  <time datetime="{{ $tugasTerdekat->deadline->toIso8601String() }}">
                    {{ $tugasTerdekat->deadline->copy()->locale('id')->diffForHumans() }}
                  </time>
                </p>
                <span class="mt-2 inline-block text-sm font-semibold text-[#2b62a8] group-hover:underline">
                  Lihat tugas
                </span>
              </div>
            </a>
          @endif

          @if ($kuisAktif)
            <a href="{{ route('mahasiswa.kuis.kerjakan', $kuisAktif) }}"
               class="{{ $kartu }} {{ $fokus }} group flex min-w-0 items-start gap-3
                      p-4 text-[#173d6e] hover:border-[#acd6fb] hover:bg-[#f4f7fb]">
              <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-[#dfeffe]">
                <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
              </span>

              <div class="min-w-0 flex-1 [overflow-wrap:anywhere]">
                <p class="m-0 text-sm text-slate-600">{{ $kuisTersedia->count() }} kuis dibuka</p>
                <p class="mt-1 mb-0 font-bold">{{ $kuisAktif->judul }}</p>
                <p class="mt-1 mb-0 text-sm text-slate-600">
                  Ditutup
                  <time datetime="{{ $kuisAktif->selesai->toIso8601String() }}">
                    {{ $kuisAktif->selesai->copy()->locale('id')->diffForHumans() }}
                  </time>
                </p>
                <span class="mt-2 inline-block text-sm font-semibold text-[#2b62a8] group-hover:underline">
                  Kerjakan kuis
                </span>
              </div>
            </a>
          @endif
        </div>
      </section>
    @endif

    <section aria-labelledby="judul-kelas">
      <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 id="judul-kelas" class="m-0 text-lg font-bold tracking-tight">Kelas Saya</h2>
          <p class="mt-1 mb-0 text-sm text-slate-600">{{ $daftarKelas->count() }} kelas aktif</p>
        </div>

        <button type="button" data-buka="modal-gabung" aria-haspopup="dialog"
                class="{{ $fokus }} inline-flex min-h-11 cursor-pointer items-center gap-2 rounded-lg
                       border-0 bg-[#173d6e] px-4 py-2 font-sans text-sm font-semibold
                       text-white hover:bg-[#2b62a8]">
          <i class="fa-solid fa-plus" aria-hidden="true"></i>
          Gabung Kelas
        </button>
      </div>

      <div class="grid gap-3 sm:grid-cols-2 2xl:grid-cols-3">
        @forelse ($daftarKelas as $k)
          <a href="{{ route('mahasiswa.kelas.show', $k) }}"
             class="{{ $kartu }} {{ $fokus }} flex min-w-0 flex-col overflow-hidden
                    text-[#173d6e] hover:border-[#acd6fb] hover:shadow-md">
            <div class="grow p-4 [overflow-wrap:anywhere]">
              <p class="m-0 text-xs font-semibold text-[#2b62a8]">{{ $k->nama }}</p>
              <h3 class="mt-1 mb-3 text-base leading-snug font-bold">
                {{ $k->mataKuliah?->nama ?? $k->nama }}
              </h3>

              @if ($k->dosen)
                <div class="flex items-center gap-2 text-sm text-slate-600">
                  @include('mahasiswa._avatar', ['u' => $k->dosen])
                  <span>{{ $k->dosen->name }}</span>
                </div>
              @endif
            </div>

            <div class="flex flex-wrap gap-x-4 gap-y-1 border-0 border-t border-solid border-[#dbe5f1]
                        bg-[#f4f7fb] px-4 py-2.5 text-sm text-slate-600">
              <span>
                <i class="fa-solid fa-book-open mr-1 text-[#2b62a8]" aria-hidden="true"></i>
                {{ $k->materi_count }} materi
              </span>
              <span>
                <i class="fa-regular fa-file-lines mr-1 text-[#2b62a8]" aria-hidden="true"></i>
                {{ $k->tugas_count }} tugas
              </span>
            </div>
          </a>
        @empty
          <div class="col-span-full rounded-xl border border-dashed border-[#acd6fb] bg-white p-6 text-sm sm:p-7">
            <p class="m-0 font-semibold">Belum ada kelas aktif.</p>
            <p class="mt-1 mb-0 text-slate-600">
              Pilih <strong>Gabung Kelas</strong> dan masukkan kode dari dosen.
            </p>
          </div>
        @endforelse
      </div>
    </section>
  </div>

  <dialog id="modal-gabung" aria-labelledby="judul-gabung"
          class="m-auto max-h-[calc(100dvh_-_2rem)] w-[calc(100%_-_2rem)] max-w-sm
                 overflow-y-auto rounded-2xl border border-solid border-slate-200 bg-white
                 p-0 text-[#173d6e] shadow-xl backdrop:bg-slate-900/45">
    <div class="p-5 sm:p-6">
      <h2 id="judul-gabung" class="m-0 text-xl font-bold">Gabung Kelas</h2>
      <p id="petunjuk-kode" class="mt-2 mb-0 text-sm text-slate-600">
        Masukkan kode kelas yang diberikan dosen.
      </p>

      <form id="batal-gabung" method="dialog"></form>

      <form method="POST" action="{{ route('mahasiswa.kelas.gabung') }}" class="mt-4">
        @csrf

        <label for="kode_kelas" class="mb-1.5 block text-sm font-semibold">Kode kelas</label>
        <input id="kode_kelas" name="kode_kelas" type="text" value="{{ old('kode_kelas') }}"
               maxlength="10" autocomplete="off" autocapitalize="characters" spellcheck="false" required autofocus
               aria-describedby="petunjuk-kode{{ $errors->has('kode_kelas') ? ' error-kode' : '' }}"
               @if ($errors->has('kode_kelas')) aria-invalid="true" @endif
               class="box-border min-h-11 w-full rounded-lg border border-solid border-slate-300
                      px-3 py-2 font-sans text-base uppercase focus:border-[#2b62a8] focus:outline-none
                      focus:ring-2 focus:ring-[#acd6fb] aria-invalid:border-red-400">

        @error('kode_kelas')
          <p id="error-kode" role="alert" class="mt-1.5 mb-0 text-sm text-red-700">{{ $message }}</p>
        @enderror

        <div class="mt-5 flex justify-end gap-2">
          <button type="submit" form="batal-gabung"
                  class="{{ $fokus }} min-h-11 cursor-pointer rounded-lg border border-solid border-slate-300
                         bg-white px-4 font-sans text-sm font-semibold text-[#173d6e] hover:bg-[#f4f7fb]">
            Batal
          </button>

          <button type="submit"
                  class="{{ $fokus }} min-h-11 cursor-pointer rounded-lg border-0 bg-[#173d6e]
                         px-4 font-sans text-sm font-semibold text-white hover:bg-[#2b62a8]">
            Gabung
          </button>
        </div>
      </form>
    </div>
  </dialog>
@endsection

@push('scripts')
  @error('kode_kelas')
    <script>
      document.getElementById('modal-gabung').showModal();
    </script>
  @enderror
@endpush