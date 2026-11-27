@extends('layouts.panel')

@php
  $berkas = $materi->tipe === 'file';
  $izin = (int) old('boleh_unduh', $materi->boleh_unduh);
  $panel = 'min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5';
  $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
  $tombol = "inline-flex min-h-10 items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold no-underline $fokus";
@endphp

@section('title', $materi->judul)
@section('kembali', route('dosen.kelas.show', [$kelas, 'tab' => 'materi']))

@section('content')
  <div class="space-y-4">
    <header class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-[#acd6fb] bg-[#dfeffe] p-4 sm:p-5">
      <div class="flex min-w-0 items-start gap-3">
        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-white text-[#173d6e]">
          <i class="fa-solid {{ $berkas ? 'fa-file-lines' : 'fa-link' }}" aria-hidden="true"></i>
        </span>

        <div class="min-w-0 [overflow-wrap:anywhere]">
          <h1 class="text-xl font-extrabold tracking-tight text-[#173d6e] sm:text-2xl">
            {{ $materi->judul }}
          </h1>
          <p class="mt-1 text-sm text-slate-700">
            {{ $berkas ? 'File materi' : 'Tautan materi' }} · {{ \App\Support\Waktu::singkat($materi->created_at) }}
          </p>
        </div>
      </div>

      <div class="flex flex-wrap gap-2">
        <a href="{{ route('dosen.materi.edit', $materi) }}"
           class="{{ $tombol }} border border-slate-300 bg-white text-[#173d6e] hover:bg-slate-50">
          <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
          Ubah
        </a>

        <form method="POST" action="{{ route('dosen.materi.destroy', $materi) }}"
              data-konfirmasi-judul="Hapus Materi"
              data-konfirmasi="Hapus materi {{ $materi->judul }} dari kelas ini?">
          @csrf @method('DELETE')
          <button type="submit" class="{{ $tombol }} border border-red-200 bg-white text-red-700 hover:bg-red-50">
            <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
            Hapus
          </button>
        </form>
      </div>
    </header>

    <div class="grid items-start gap-4 {{ $berkas ? 'lg:grid-cols-[minmax(0,1fr)_minmax(17rem,21rem)]' : '' }}">
      <section class="{{ $panel }} space-y-4" aria-labelledby="judul-deskripsi">
        <div>
          <h2 id="judul-deskripsi" class="text-base font-bold text-[#173d6e]">Deskripsi</h2>
          <p class="mt-2 text-sm leading-relaxed whitespace-pre-wrap text-slate-700 [overflow-wrap:anywhere]">{{ $materi->deskripsi ?: 'Belum ada deskripsi.' }}</p>
        </div>

        <div class="border-t border-slate-100 pt-4 [&_a]:no-underline">
          <h2 class="mb-2 text-base font-bold text-[#173d6e]">
            {{ $berkas ? 'Lampiran' : 'Tautan' }}
          </h2>

          @if ($berkas)
            @include('mahasiswa._file', [
              'judul' => $materi->file_name ?: $materi->judul,
              'url' => route('file.materi', $materi),
              'namaFile' => $materi->file_name,
              'baruTab' => $materi->bisaDilihat(),
            ])
          @else
            @include('mahasiswa._file', [
              'judul' => $materi->judul,
              'url' => $materi->url,
              'link' => true,
            ])
          @endif
        </div>
      </section>

      @if ($berkas)
        <form id="akses" method="POST" action="{{ route('dosen.materi.akses', $materi) }}"
              class="{{ $panel }} scroll-mt-4" aria-labelledby="judul-akses">
          @csrf @method('PATCH')

          <h2 id="judul-akses" class="flex items-center gap-2 text-base font-bold text-[#173d6e]">
            <i class="fa-solid fa-lock" aria-hidden="true"></i>
            Pengaturan Akses
          </h2>

          <p class="mt-2 text-sm text-slate-600">
            Status saat ini <strong class="text-[#173d6e]">{{ $materi->boleh_unduh ? 'Boleh diunduh' : 'Hanya dilihat' }}</strong>.
          </p>

          <fieldset class="mt-4" aria-describedby="petunjuk-akses">
            <legend class="text-sm font-semibold text-[#173d6e]">Izin unduh untuk mahasiswa</legend>

            <div class="mt-2 grid gap-2">
              @foreach ([1 => 'Boleh diunduh', 0 => 'Hanya dilihat'] as $nilai => $teks)
                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-3 py-2.5
                              text-sm font-semibold text-[#173d6e] has-checked:border-[#2b62a8] has-checked:bg-[#dfeffe]">
                  <input type="radio" name="boleh_unduh" value="{{ $nilai }}" class="accent-[#173d6e]"
                         @checked($izin === $nilai) required>
                  {{ $teks }}
                </label>
              @endforeach
            </div>

            <p id="petunjuk-akses" class="mt-2 text-xs text-slate-600">
              @if ($materi->bisaDilihat())
                Dalam mode Hanya dilihat, mahasiswa dapat membuka file di browser.
              @else
                Format file ini memerlukan izin unduh agar dapat dibuka mahasiswa.
              @endif
            </p>
          </fieldset>

          <button type="submit" class="{{ $tombol }} mt-4 w-full bg-[#173d6e] text-white hover:bg-[#2b62a8]">
            Simpan Pengaturan
          </button>
        </form>
      @endif
    </div>
  </div>
@endsection