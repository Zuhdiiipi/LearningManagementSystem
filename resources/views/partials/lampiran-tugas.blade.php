@if ($tugas->file_path)
  @php
    $nama = $tugas->file_name ?: basename($tugas->file_path);
    $mimeVideo = \App\Support\PratinjauLampiran::mimeVideo($nama);
    $bisaDilihat = \App\Support\PratinjauLampiran::mime($nama) !== null
        || \App\Support\PratinjauLampiran::excel($nama);

    $tautanPratinjau = fn (array $parameter = []) =>
        route($ruteTugas, [$tugas, 'lihat' => 1] + $parameter) . '#lampiran-tugas';

    $tombol = 'inline-flex min-h-10 items-center justify-center gap-2 rounded-lg
               px-3 py-2 text-sm font-semibold no-underline focus-visible:outline-2
               focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
    $utama = 'bg-[#173d6e] text-white hover:bg-[#2b62a8]';
    $sekunder = 'border border-solid border-[#acd6fb] bg-white text-[#173d6e] hover:bg-[#dfeffe]';
  @endphp

  <div id="lampiran-tugas" class="mt-4 scroll-mt-4 border-0 border-t border-solid border-slate-100 pt-4">
    <h3 class="mt-0 mb-2 text-sm font-bold text-[#173d6e]">Lampiran Dosen</h3>

    <div class="flex flex-wrap items-center gap-3 rounded-lg border border-slate-200 p-3">
      <span class="min-w-0 flex-1 text-sm font-semibold text-[#173d6e] [overflow-wrap:anywhere]">
        {{ $nama }}
      </span>

      <div class="flex flex-wrap gap-2">
        @if ($bisaDilihat)
          @if ($pratinjau)
            <a href="{{ route($ruteTugas, $tugas) }}#lampiran-tugas"
               class="{{ $tombol }} {{ $sekunder }}">
              Tutup Pratinjau
            </a>
          @else
            <a href="{{ $tautanPratinjau() }}" class="{{ $tombol }} {{ $utama }}">
              <i class="{{ $mimeVideo ? 'fa-solid fa-play' : 'fa-regular fa-eye' }}"
                 aria-hidden="true"></i>
              {{ $mimeVideo ? 'Putar Video' : 'Lihat Lampiran' }}
            </a>
          @endif
        @endif

        <a href="{{ route('file.tugas', [$tugas, 'unduh' => 1]) }}"
           class="{{ $tombol }} {{ $bisaDilihat ? $sekunder : $utama }}"
           aria-label="Unduh {{ $nama }}">
          <i class="fa-solid fa-download" aria-hidden="true"></i> Unduh
        </a>
      </div>
    </div>

    @if ($pratinjau)
      <div class="mt-3 min-w-0">
        @if ($pratinjau['xlsx'])
          @include('partials.pratinjau-excel', [
            'excel' => $pratinjau['excel'],
            'tautanExcel' => $tautanPratinjau,
          ])
        @elseif ($mimeVideo)
          <video controls playsinline preload="metadata" aria-label="{{ $nama }}"
                 class="block max-h-[65vh] w-full rounded-lg bg-black">
            <source src="{{ route('file.tugas', [$tugas, 'lihat' => 1]) }}"
                    type="{{ $mimeVideo }}">
            Gunakan tombol Unduh jika pemutar video tidak didukung.
          </video>
        @else
          <iframe src="{{ route('file.tugas', [$tugas, 'lihat' => 1]) }}"
                  title="Pratinjau {{ $nama }}"
                  class="block h-[65vh] min-h-72 w-full rounded-lg border border-slate-200 bg-slate-50"></iframe>
        @endif
      </div>
    @endif
  </div>
@endif