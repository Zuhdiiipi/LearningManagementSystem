@extends('layouts.panel', ['menuAktif' => 'materi'])

@section('title', $materi->judul)
@section('kembali', route('mahasiswa.kelas.show', [$kelas, 'tab' => 'materi']))

@section('content')
  @php
    $materiFile = $materi->tipe === 'file';
    $bisaDilihat = $materiFile && $materi->bisaDilihat();
    $bolehUnduh = $materiFile && $materi->boleh_unduh;
    $adaTautan = ! $materiFile && filled($materi->url);
    $formatFile = strtoupper(pathinfo($materi->file_name ?? '', PATHINFO_EXTENSION));
    $domain = $adaTautan ? parse_url($materi->url, PHP_URL_HOST) : null;
    $tanggal = $materi->created_at?->copy()->locale('id')->translatedFormat('j F Y, H:i');

    $panel = 'min-w-0 rounded-xl border border-solid border-slate-200 bg-white shadow-sm';
    $tombol = 'inline-flex min-h-11 grow items-center justify-center gap-2 rounded-lg px-3 py-2
               text-sm font-semibold no-underline focus-visible:outline-2 focus-visible:outline-offset-2
               focus-visible:outline-[#2b62a8] sm:grow-0';
    $utama = 'bg-[#173d6e] text-white hover:bg-[#2b62a8]';
    $sekunder = 'border border-solid border-[#acd6fb] bg-white text-[#173d6e] hover:bg-[#dfeffe]';
  @endphp

  <div class="mx-auto max-w-4xl space-y-4 text-[#173d6e]">
    <header class="flex items-start gap-3 rounded-xl border border-solid border-[#acd6fb]
                   bg-[#dfeffe] p-4 sm:gap-4 sm:px-5">
      <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-white/80">
        <i class="fa-solid fa-book-open" aria-hidden="true"></i>
      </span>

      <div class="min-w-0 [overflow-wrap:anywhere]">
        <h1 class="m-0 text-xl leading-tight font-extrabold tracking-tight sm:text-2xl">
          {{ $materi->judul }}
        </h1>

        <p class="mt-1.5 mb-0 flex flex-wrap gap-x-2 gap-y-1 text-sm text-slate-700">
          @if ($tanggal)
            <time datetime="{{ $materi->created_at->toIso8601String() }}">{{ $tanggal }}</time>
          @endif

          @if ($materi->pertemuan)
            @if ($tanggal)
              <span aria-hidden="true">·</span>
            @endif

            <span>Pertemuan {{ $materi->pertemuan->nomor }}</span>
          @endif
        </p>
      </div>
    </header>

    @if (filled($materi->deskripsi))
      <section aria-labelledby="judul-deskripsi" class="{{ $panel }} p-4 sm:px-5">
        <h2 id="judul-deskripsi" class="m-0 text-base font-bold">Deskripsi</h2>
        <p class="mt-2 mb-0 text-sm leading-7 whitespace-pre-wrap text-slate-700
                  [overflow-wrap:anywhere]">{{ $materi->deskripsi }}</p>
      </section>
    @endif

    <section aria-labelledby="judul-sumber" class="{{ $panel }} p-4 sm:px-5">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex min-w-0 flex-1 basis-full items-start gap-3 sm:basis-0">
          <span class="grid h-9 w-11 shrink-0 place-items-center rounded-lg bg-[#dfeffe] text-xs font-bold"
                aria-hidden="true">
            @if ($materiFile && $formatFile)
              {{ mb_substr($formatFile, 0, 4) }}
            @else
              <i class="fa-solid {{ $materiFile ? 'fa-file-lines' : 'fa-link' }}"></i>
            @endif
          </span>

          <div class="min-w-0 [overflow-wrap:anywhere]">
            <h2 id="judul-sumber" class="m-0 text-base font-bold">
              {{ $materiFile ? 'File materi' : 'Tautan materi' }}
            </h2>

            @if ($materiFile && $materi->file_name)
              <p class="mt-1 mb-0 text-sm leading-snug text-slate-600">{{ $materi->file_name }}</p>
            @elseif ($domain)
              <p class="mt-1 mb-0 text-sm leading-snug text-slate-600">{{ $domain }}</p>
            @endif
          </div>
        </div>

        @if ($bisaDilihat || $bolehUnduh || $adaTautan)
          <div class="flex w-full flex-wrap gap-2 sm:w-auto">
            @if ($bisaDilihat)
              <a href="{{ route('file.materi', $materi) }}" target="_blank" rel="noopener"
                 class="{{ $tombol }} {{ $utama }}">
                <i class="fa-regular fa-eye" aria-hidden="true"></i>
                Lihat materi<span class="sr-only"> di tab baru</span>
              </a>
            @endif

            @if ($bolehUnduh)
              <a href="{{ route('file.materi', [$materi, 'unduh' => 1]) }}"
                 class="{{ $tombol }} {{ $bisaDilihat ? $sekunder : $utama }}">
                <i class="fa-solid fa-download" aria-hidden="true"></i>
                Unduh
              </a>
            @endif

            @if ($adaTautan)
              <a href="{{ $materi->url }}" target="_blank" rel="noopener"
                 class="{{ $tombol }} {{ $utama }}">
                <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                Buka tautan<span class="sr-only"> di tab baru</span>
              </a>
            @endif
          </div>
        @endif
      </div>

      @if ($materiFile && ! $bolehUnduh)
        <p class="mt-3 mb-0 flex items-start gap-2 rounded-lg bg-slate-50 p-3
                  text-sm leading-relaxed text-slate-700">
          <i class="fa-solid fa-lock mt-1 shrink-0" aria-hidden="true"></i>
          <span>
            @if ($bisaDilihat)
              Dosen menonaktifkan unduhan melalui LMS. Materi bisa dibuka lewat pratinjau.
            @else
              Pratinjau untuk format ini tidak tersedia. Unduhan melalui LMS dinonaktifkan oleh dosen.
            @endif
          </span>
        </p>
      @elseif (! $materiFile && ! $adaTautan)
        <p class="mt-3 mb-0 text-sm text-slate-600">Tautan materi belum tersedia.</p>
      @endif
    </section>
  </div>
@endsection