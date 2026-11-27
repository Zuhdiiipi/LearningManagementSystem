@extends('layouts.panel')

@php
  $jenis = $tugas->jenis === 'praktikum' ? 'Praktikum' : 'Tugas';
  $panel = 'min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5';
  $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
  $tombol = "inline-flex min-h-10 items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold no-underline $fokus";
@endphp

@section('title', $tugas->judul)
@section('kembali', route('dosen.kelas.show', [$kelas, 'tab' => 'tugas']))

@section('content')
  <div class="space-y-4">
    <header class="flex flex-wrap items-center justify-between gap-3 rounded-xl
                   border border-[#acd6fb] bg-[#dfeffe] p-4 sm:p-5">
      <div class="flex min-w-0 items-start gap-3">
        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-white text-[#173d6e]">
          <i class="fa-solid {{ $jenis === 'Tugas' ? 'fa-file-code' : 'fa-clipboard-list' }}"
             aria-hidden="true"></i>
        </span>

        <div class="min-w-0 [overflow-wrap:anywhere]">
          <p class="text-xs font-semibold text-[#173d6e]">{{ $jenis }}</p>
          <h1 class="mt-0.5 text-xl font-extrabold tracking-tight text-[#173d6e] sm:text-2xl">
            {{ $tugas->judul }}
          </h1>
          <p class="mt-1 text-sm text-slate-700">
            Dibuat {{ \App\Support\Waktu::singkat($tugas->created_at) }}
          </p>
        </div>
      </div>

      <div class="flex flex-wrap gap-2">
        <a href="{{ route('dosen.tugas.edit', $tugas) }}"
           class="{{ $tombol }} border border-slate-300 bg-white text-[#173d6e] hover:bg-slate-50">
          <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i> Ubah
        </a>

        <form method="POST" action="{{ route('dosen.tugas.destroy', $tugas) }}"
              data-konfirmasi-judul="Hapus {{ $jenis }}"
              data-konfirmasi="Hapus {{ $tugas->judul }}? Catatan pengumpulan dan nilai terkait ikut terhapus.">
          @csrf
          @method('DELETE')

          <button type="submit"
                  class="{{ $tombol }} border border-red-200 bg-white text-red-700 hover:bg-red-50">
            <i class="fa-regular fa-trash-can" aria-hidden="true"></i> Hapus
          </button>
        </form>
      </div>
    </header>

    <div class="grid items-start gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(16rem,21rem)]">
      <section class="{{ $panel }} space-y-4" aria-labelledby="judul-instruksi">
        <div>
          <h2 id="judul-instruksi" class="text-base font-bold text-[#173d6e]">Instruksi</h2>
          <p class="mt-2 text-sm leading-relaxed whitespace-pre-wrap text-slate-700
                    [overflow-wrap:anywhere]">{{ $tugas->instruksi }}</p>
        </div>

        @include('partials.lampiran-tugas', ['ruteTugas' => 'dosen.tugas.show'])
      </section>

      <section class="{{ $panel }}" aria-labelledby="judul-pengumpulan">
        <h2 id="judul-pengumpulan" class="text-base font-bold text-[#173d6e]">
          Pengumpulan
        </h2>

        <dl class="mt-3 space-y-3">
          <div>
            <dt class="text-sm text-slate-600">Batas pengumpulan</dt>
            <dd class="mt-0.5 text-sm font-semibold text-[#173d6e]">
              {{ $tugas->deadline->copy()->locale('id')->translatedFormat('l, j F Y · H:i') }}
            </dd>
          </div>

          <div>
            <dt class="text-sm text-slate-600">Sudah mengumpulkan</dt>
            <dd class="mt-0.5 text-xl font-bold tabular-nums text-[#173d6e]">
              {{ $tugas->pengumpulan_count }} / {{ $jumlahMahasiswa }}
            </dd>
          </div>
        </dl>

        <a href="{{ route('dosen.tugas.terkumpul', $tugas) }}"
           class="{{ $tombol }} mt-4 w-full bg-[#173d6e] text-white hover:bg-[#2b62a8]">
          Lihat Pengumpulan
        </a>
      </section>
    </div>
  </div>
@endsection