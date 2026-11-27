@extends('layouts.panel')

@section('title', 'Hasil ' . $kuis->judul)

@section('content')
  @php
    $skor = rtrim(rtrim(number_format($hasil->skor, 2, ',', '.'), '0'), ',');
    $waktu = static fn ($tanggal) => $tanggal->copy()->locale('id')->translatedFormat('j F Y, H:i');
    $panel = 'min-w-0 rounded-xl border border-solid border-slate-200 bg-white shadow-sm';
    $badge = 'inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-xs font-semibold';
  @endphp

  <div class="mx-auto max-w-4xl space-y-4 text-[#173d6e]">
    <header class="overflow-hidden rounded-xl bg-[#173d6e] text-white">
      <div class="grid gap-4 p-4 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center sm:px-5">
        <div class="min-w-0 [overflow-wrap:anywhere]">
          <p class="m-0 text-sm font-semibold text-[#acd6fb]">Hasil kuis</p>
          <h1 class="mt-1 mb-0 text-xl font-extrabold tracking-tight sm:text-2xl">{{ $kuis->judul }}</h1>
          <p class="mt-2 mb-0 text-sm leading-relaxed text-[#dfeffe]">
            {{ $hasil->benar }} dari {{ $hasil->jumlah_soal }} jawaban benar
            · Dikerjakan {{ $waktu($hasil->created_at) }}
          </p>
        </div>

        <p class="m-0 w-fit rounded-lg bg-white/15 px-4 py-2 font-semibold tabular-nums">
          <strong class="text-3xl font-extrabold">{{ $skor }}</strong>
          <span class="text-sm text-[#dfeffe]">/ 100</span>
        </p>
      </div>
    </header>

    <section aria-labelledby="judul-hasil-jawaban" class="space-y-3">
      <h2 id="judul-hasil-jawaban" class="m-0 text-lg font-bold tracking-tight">Hasil Jawaban</h2>

      @unless ($tampilKunci)
        <p class="m-0 rounded-lg border border-solid border-[#acd6fb] bg-[#dfeffe] p-3 text-sm">
          Kunci jawaban ditandai setelah sesi kuis ditutup, paling lambat {{ $waktu($kuis->selesai) }}.
        </p>
      @endunless

      @forelse ($soal as $s)
        @php
          $pilih = $hasil->jawaban[$s->id] ?? null;
          $benar = $pilih === $s->jawaban;

          [$status, $warna] = match (true) {
              $benar => ['Benar', 'bg-emerald-50 text-emerald-800'],
              $pilih === null => ['Tidak dijawab', 'bg-slate-100 text-slate-700'],
              default => ['Salah', 'bg-red-50 text-red-800'],
          };
        @endphp

        <article class="{{ $panel }} p-4 sm:px-5">
          <div class="mb-3 flex flex-wrap items-start justify-between gap-2">
            <h3 class="m-0 flex min-w-0 flex-1 basis-full gap-2 text-base leading-snug font-bold sm:basis-0">
              <span class="shrink-0 text-[#2b62a8]">{{ $loop->iteration }}.</span>
              <span class="min-w-0 whitespace-pre-wrap [overflow-wrap:anywhere]">{{ $s->pertanyaan }}</span>
            </h3>

            <span class="{{ $badge }} {{ $warna }} ml-5 sm:ml-0">{{ $status }}</span>
          </div>

          <ul class="m-0 grid list-none gap-2 p-0">
            @foreach (['a', 'b', 'c', 'd', 'e'] as $o)
              @php
                $opsi = $s->{'opsi_' . $o};
                $dipilih = $pilih === $o;
                $kunci = $tampilKunci && $s->jawaban === $o;

                $warnaOpsi = match (true) {
                    $kunci || ($dipilih && $benar) => 'border-emerald-200 bg-emerald-50',
                    $dipilih => 'border-red-200 bg-red-50',
                    default => 'border-slate-200 bg-slate-50',
                };
              @endphp
              @continue($opsi === null || $opsi === '')

              <li class="{{ $warnaOpsi }} flex items-start gap-2.5 rounded-lg border border-solid
                         px-3 py-2.5 text-sm text-slate-800">
                <span class="shrink-0 font-semibold">{{ strtoupper($o) }}.</span>

                <div class="min-w-0 flex-1">
                  <span class="whitespace-pre-wrap [overflow-wrap:anywhere]">{{ $opsi }}</span>

                  @if ($dipilih || $kunci)
                    <div class="mt-1 flex flex-wrap gap-1.5">
                      @if ($dipilih)
                        <span class="{{ $badge }} bg-white text-[#173d6e]">Jawabanmu</span>
                      @endif

                      @if ($kunci)
                        <span class="{{ $badge }} bg-white text-emerald-800">Kunci jawaban</span>
                      @endif
                    </div>
                  @endif
                </div>
              </li>
            @endforeach
          </ul>
        </article>
      @empty
        <p class="{{ $panel }} m-0 p-4 text-sm text-slate-600">Tidak ada soal untuk ditampilkan.</p>
      @endforelse
    </section>

    <a href="{{ route('mahasiswa.kelas.show', [$kuis->kelas, 'tab' => 'kuis']) }}"
       class="inline-flex min-h-11 items-center gap-2 rounded-lg bg-[#173d6e] px-4 py-2 text-sm font-semibold
              text-white no-underline hover:bg-[#2b62a8] focus-visible:outline-2
              focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]">
      <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
      Kembali ke Kelas
    </a>
  </div>
@endsection