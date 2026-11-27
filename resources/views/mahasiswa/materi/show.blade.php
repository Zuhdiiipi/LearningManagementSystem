@extends('layouts.panel', ['menuAktif' => 'materi'])

@section('title', $materi->judul)
@section('kembali', route('mahasiswa.kelas.show', [$kelas, 'tab' => 'materi']))

@section('content')
  @php
    $materiFile = $materi->tipe === 'file';
    $daftarLampiran = $materiFile ? $materi->daftarLampiran() : [];
    $adaTautan = ! $materiFile && filled($materi->url);
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
        <h1 class="m-0 text-xl leading-tight font-extrabold tracking-tight sm:text-2xl">{{ $materi->judul }}</h1>
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
      <section aria-labelledby="judul-deskripsi" class="{{ $panel }} px-4 py-4 sm:px-5">
        <h2 id="judul-deskripsi" class="m-0 text-base font-bold">Deskripsi</h2>
        <p class="mt-2 mb-0 text-sm leading-7 whitespace-pre-wrap text-slate-700 [overflow-wrap:anywhere]">{{ $materi->deskripsi }}</p>
      </section>
    @endif

    <section aria-labelledby="judul-sumber" class="{{ $panel }} p-4 sm:px-5">
      <h2 id="judul-sumber" class="text-base font-bold">{{ $materiFile ? 'File materi' : 'Tautan materi' }}</h2>

      @if ($materiFile)
        <ul id="daftar-file-materi" class="mt-3 grid scroll-mt-4 gap-2">
          @forelse ($daftarLampiran as $indeks => $file)
            @php
              $format = strtoupper(pathinfo($file['nama'], PATHINFO_EXTENSION)) ?: 'FILE';
              $bisaDilihat = $format === 'XLSX' || $materi->bisaDilihat($file['nama']);
              $aktif = ($pratinjau['indeks'] ?? null) === $indeks;
            @endphp
            <li class="flex flex-wrap items-center gap-3 rounded-lg border border-slate-200 p-3">
              <span class="grid h-9 w-11 shrink-0 place-items-center rounded-lg bg-[#dfeffe] text-xs font-bold" aria-hidden="true">
                {{ mb_substr($format, 0, 4) }}
              </span>
              <div class="min-w-0 flex-1 [overflow-wrap:anywhere]">
                <p class="text-sm font-semibold">{{ $file['nama'] }}</p>
              </div>
              <div class="flex w-full flex-wrap gap-2 sm:w-auto">
                @if ($bisaDilihat)
                  <a href="{{ route('mahasiswa.materi.show', [$materi, 'lihat' => $indeks]) }}#pratinjau-materi"
                     class="{{ $tombol }} {{ $aktif ? $sekunder : $utama }}" aria-label="Lihat {{ $file['nama'] }}"
                     @if ($aktif) aria-current="true" @endif>
                    <i class="fa-regular fa-eye" aria-hidden="true"></i> Lihat materi
                  </a>
                @endif
                <a href="{{ route('file.materi', [$materi, 'lampiran' => $indeks, 'unduh' => 1]) }}"
                   class="{{ $tombol }} {{ $bisaDilihat ? $sekunder : $utama }}" aria-label="Unduh {{ $file['nama'] }}">
                  <i class="fa-solid fa-download" aria-hidden="true"></i> Unduh
                </a>
              </div>
            </li>
          @empty
            <li class="text-sm text-slate-600">File materi belum tersedia.</li>
          @endforelse
        </ul>
      @elseif ($adaTautan)
        <div class="mt-3 flex flex-wrap items-center gap-3">
          @if ($domain)
            <p class="min-w-0 text-sm text-slate-600 [overflow-wrap:anywhere]">{{ $domain }}</p>
          @endif
          <a href="{{ $materi->url }}" target="_blank" rel="noopener" class="{{ $tombol }} {{ $utama }}">
            <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
            Buka tautan<span class="sr-only"> di tab baru</span>
          </a>
        </div>
      @else
        <p class="mt-3 text-sm text-slate-600">Tautan materi belum tersedia.</p>
      @endif
    </section>

    @if ($pratinjau)
      <section id="pratinjau-materi" aria-labelledby="judul-pratinjau" class="{{ $panel }} scroll-mt-4 p-4 sm:px-5">
        <header class="mb-3 flex flex-wrap items-center justify-between gap-2">
          <div class="min-w-0 [overflow-wrap:anywhere]">
            <h2 id="judul-pratinjau" class="text-base font-bold">Pratinjau materi</h2>
            <p class="mt-0.5 text-sm text-slate-600">{{ $pratinjau['nama'] }}</p>
          </div>
          <a href="{{ route('mahasiswa.materi.show', $materi) }}#daftar-file-materi" class="{{ $tombol }} {{ $sekunder }}">
            Tutup pratinjau
          </a>
        </header>

        @if (! $pratinjau['xlsx'])
          <iframe src="{{ route('file.materi', [$materi, 'lampiran' => $pratinjau['indeks']]) }}"
                  title="Pratinjau {{ $pratinjau['nama'] }}"
                  class="block h-[65vh] min-h-72 w-full rounded-lg border border-slate-200 bg-slate-50"></iframe>
        @elseif ($excel === null)
          <p class="rounded-lg bg-slate-50 p-3 text-sm text-slate-700">File Excel ini belum bisa dipratinjau.</p>
        @else
          @php
            $pilihan = $excel['pilihan'];
            $tautanExcel = fn (array $parameter) => route('mahasiswa.materi.show', [$materi, 'lihat' => $pratinjau['indeks']] + $parameter) . '#pratinjau-materi';
          @endphp

          @if ($excel['lembar'])
            <nav aria-label="Lembar Excel" class="mb-3 flex flex-wrap gap-2">
              @foreach ($excel['lembar'] as $indeks => $nama)
                <a href="{{ $tautanExcel(['lembar' => $indeks]) }}"
                   class="{{ $tombol }} {{ ($pilihan['indeks'] ?? null) === $indeks ? $utama : $sekunder }}"
                   @if (($pilihan['indeks'] ?? null) === $indeks) aria-current="page" @endif>
                  {{ $nama }}
                </a>
              @endforeach
            </nav>
          @endif

          @if ($pilihan && $pilihan['jumlahKolom'] > 0)
            <div role="region" aria-label="Isi lembar Excel" tabindex="0"
                 class="overflow-x-auto rounded-lg border border-slate-200 focus-visible:outline-2 focus-visible:outline-[#2b62a8]">
              <table class="w-full border-collapse text-left text-sm text-slate-800">
                <thead class="bg-[#dfeffe] text-[#173d6e]">
                  <tr>
                    <th scope="col" class="border border-slate-200 px-3 py-2">Baris</th>
                    @for ($kolom = 0; $kolom < $pilihan['jumlahKolom']; $kolom++)
                      <th scope="col" class="border border-slate-200 px-3 py-2 whitespace-nowrap">
                        {{ $pilihan['awalKolom'] + $kolom + 1 }}
                      </th>
                    @endfor
                  </tr>
                </thead>
                <tbody>
                  @foreach ($pilihan['baris'] as $nomor => $isi)
                    <tr class="odd:bg-white even:bg-slate-50">
                      <th scope="row" class="border border-slate-200 px-3 py-2 text-slate-600">{{ $nomor }}</th>
                      @for ($kolom = 0; $kolom < $pilihan['jumlahKolom']; $kolom++)
                        <td class="border border-slate-200 px-3 py-2 align-top whitespace-pre-wrap [overflow-wrap:anywhere]">{{ $isi[$kolom] ?? '' }}</td>
                      @endfor
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <p class="rounded-lg bg-slate-50 p-3 text-sm text-slate-700">
              {{ $pilihan ? 'Bagian lembar ini kosong.' : 'Pilih lembar Excel untuk melihat isinya.' }}
            </p>
          @endif

          @if ($pilihan)
            @php
              $navigasiExcel = [
                ['Baris sebelumnya', $pilihan['halaman'] > 1, $pilihan['halaman'] - 1, $pilihan['kolom']],
                ['Baris berikutnya', $pilihan['barisLain'], $pilihan['halaman'] + 1, $pilihan['kolom']],
                ['Kolom sebelumnya', $pilihan['kolom'] > 1, $pilihan['halaman'], $pilihan['kolom'] - 1],
                ['Kolom berikutnya', $pilihan['kolomLain'], $pilihan['halaman'], $pilihan['kolom'] + 1],
              ];
            @endphp
            <nav aria-label="Navigasi isi Excel" class="mt-3 flex flex-wrap gap-2">
              @foreach ($navigasiExcel as [$label, $tersedia, $halaman, $kolom])
                @if ($tersedia)
                  <a href="{{ $tautanExcel(['lembar' => $pilihan['indeks'], 'halaman' => $halaman, 'kolom' => $kolom]) }}"
                     class="{{ $tombol }} {{ $sekunder }}">{{ $label }}</a>
                @endif
              @endforeach
            </nav>
          @endif
        @endif
      </section>
    @endif
  </div>
@endsection