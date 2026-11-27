@extends('layouts.panel', ['menuAktif' => 'tugas'])

@section('title', $tugas->judul)
@section('kembali', route('mahasiswa.kelas.show', [$kelas, 'tab' => 'tugas']))

@section('content')
  @php
    $jenis = ucfirst($tugas->jenis);
    $lewatTenggat = $tugas->sudahLewat();
    $sudahDinilai = $kumpul && $kumpul->nilai !== null;
    $terlambat = $kumpul?->terlambat();
    $formatWaktu = static fn ($waktu) => $waktu?->copy()->locale('id')->translatedFormat('j F Y, H:i') ?? '—';

    $panel = 'min-w-0 rounded-xl border border-solid border-slate-200 bg-white p-4 shadow-sm sm:p-5';
    $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
    $tombol = 'inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-lg
               px-4 py-2 font-sans text-sm font-semibold';
    $tautanFile = 'flex min-w-0 items-center gap-2 rounded-lg border border-solid border-[#acd6fb]
                   bg-[#f4f7fb] px-3 py-2.5 text-sm font-semibold text-[#173d6e]
                   no-underline hover:bg-[#dfeffe]';
  @endphp

  <div class="mx-auto max-w-5xl space-y-4 text-[#173d6e]">
    <header class="flex items-start gap-3 rounded-xl border border-solid border-[#acd6fb]
                   bg-[#dfeffe] p-4 sm:gap-4 sm:px-5">
      <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-white/80">
        <i class="fa-solid {{ $tugas->jenis === 'praktikum' ? 'fa-clipboard-list' : 'fa-file-lines' }}"
           aria-hidden="true"></i>
      </span>

      <div class="min-w-0 [overflow-wrap:anywhere]">
        <p class="m-0 text-sm font-semibold text-[#2b62a8]">{{ $jenis }}</p>
        <h1 class="mt-1 mb-0 text-xl leading-tight font-extrabold tracking-tight sm:text-2xl">
          {{ $tugas->judul }}
        </h1>
        <p class="mt-1.5 mb-0 text-sm text-slate-700">
          Diposting {{ $formatWaktu($tugas->created_at) }}
        </p>
      </div>
    </header>

    <div class="grid items-start gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(19rem,0.8fr)]">
      <section aria-labelledby="judul-instruksi" class="{{ $panel }}">
        <h2 id="judul-instruksi" class="m-0 text-base font-bold">Instruksi {{ strtolower($jenis) }}</h2>
        <p class="mt-2 mb-0 text-sm leading-7 whitespace-pre-wrap text-slate-700
                  [overflow-wrap:anywhere]">{{ $tugas->instruksi }}</p>

        @if ($tugas->file_path)
          <div class="mt-4 border-0 border-t border-solid border-slate-100 pt-4">
            <h3 class="mt-0 mb-2 text-sm font-semibold">Lampiran dosen</h3>

            <a href="{{ route('file.tugas', $tugas) }}" class="{{ $tautanFile }} {{ $fokus }}">
              <i class="fa-solid fa-download shrink-0" aria-hidden="true"></i>
              <span class="sr-only">Unduh lampiran</span>
              <span class="min-w-0 [overflow-wrap:anywhere]">{{ $tugas->file_name ?: 'Lampiran tugas' }}</span>
            </a>
          </div>
        @endif
      </section>

      <section aria-labelledby="judul-pengumpulan" class="{{ $panel }}">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <h2 id="judul-pengumpulan" class="m-0 text-base font-bold">Pengumpulan</h2>
          <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                       {{ $kumpul ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
            {{ $sudahDinilai ? 'Sudah dinilai' : ($kumpul ? 'Terkumpul' : 'Belum dikumpulkan') }}
          </span>
        </div>

        <div class="mt-3 rounded-lg bg-[#f4f7fb] p-3">
          <p class="m-0 text-xs font-semibold text-slate-600">Tenggat pengumpulan</p>
          <p class="mt-1 mb-0 text-sm font-semibold">
            <time datetime="{{ $tugas->deadline->toIso8601String() }}">{{ $formatWaktu($tugas->deadline) }}</time>
          </p>
          <p class="mt-1 mb-0 text-xs font-medium {{ $lewatTenggat ? 'text-red-700' : 'text-slate-600' }}">
            {{ $lewatTenggat ? 'Tenggat sudah lewat' : $tugas->deadline->copy()->locale('id')->diffForHumans() }}
          </p>
        </div>

        @if ($kumpul)
          <div class="mt-4 space-y-2">
            <a href="{{ route('file.pengumpulan', $kumpul) }}" class="{{ $tautanFile }} {{ $fokus }}">
              <i class="fa-solid fa-download shrink-0" aria-hidden="true"></i>
              <span class="sr-only">Unduh jawaban</span>
              <span class="min-w-0 [overflow-wrap:anywhere]">{{ $kumpul->file_name }}</span>
            </a>

            <p class="text-sm text-slate-600">Dikirim {{ $formatWaktu($kumpul->waktuKumpul()) }}</p>
            <p class="text-sm font-semibold {{ $terlambat ? 'text-red-700' : 'text-emerald-700' }}">
              {{ $terlambat ? 'Terlambat ' . $kumpul->lamaTerlambat() : 'Tepat waktu' }}
            </p>
          </div>

          @if ($sudahDinilai)
            <div class="mt-4 rounded-lg bg-[#dfeffe] p-3">
              <p class="m-0 text-sm font-semibold">Nilai</p>
              <p class="mt-1 mb-0 text-sm font-semibold"><strong class="text-2xl">{{ $kumpul->nilai }}</strong> / 100</p>

              @if (filled($kumpul->feedback))
                <p class="mt-2 mb-0 text-sm font-semibold">Catatan dosen</p>
                <p class="mt-1 mb-0 text-sm whitespace-pre-wrap [overflow-wrap:anywhere]">
                  {{ $kumpul->feedback }}
                </p>
              @endif
            </div>
          @else
            <form method="POST" action="{{ route('mahasiswa.tugas.batal', $tugas) }}" class="mt-4"
                  data-konfirmasi-judul="Batal Kirim"
                  data-konfirmasi="File jawaban akan dihapus. Kamu bisa mengunggah file lagi.">
              @csrf
              @method('DELETE')

              <button type="submit"
                      class="{{ $tombol }} {{ $fokus }} border border-solid border-red-200
                             bg-white text-red-800 hover:bg-red-50">
                Batal Kirim
              </button>
            </form>
          @endif
        @else
          @if ($lewatTenggat)
            <p class="mt-3 mb-0 rounded-lg border border-solid border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
              Kamu masih bisa mengirim jawaban. Pengumpulan akan ditandai <strong>Terlambat</strong>.
            </p>
          @endif

          <form method="POST" action="{{ route('mahasiswa.tugas.kumpul', $tugas) }}"
                enctype="multipart/form-data" class="mt-4">
            @csrf

            <label for="file-jawaban" class="mb-2 block text-sm font-semibold">File jawaban</label>
            <input id="file-jawaban" type="file" name="file" required
                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt,.png,.jpg,.jpeg"
                   aria-describedby="format-jawaban{{ $errors->has('file') ? ' error-jawaban' : '' }}"
                   @if ($errors->has('file')) aria-invalid="true" @endif
                   class="{{ $fokus }} box-border min-w-0 w-full rounded-lg border border-solid border-slate-300
                          bg-white p-1.5 font-sans text-sm text-slate-700 aria-invalid:border-red-400
                          file:mr-2 file:cursor-pointer file:rounded-md file:border-0
                          file:bg-[#dfeffe] file:px-3 file:py-2 file:font-semibold file:text-[#173d6e]">

            @error('file')
              <p id="error-jawaban" class="mt-2 mb-0 text-sm text-red-700">{{ $message }}</p>
            @enderror

            <p id="format-jawaban" class="mt-2 mb-0 text-xs leading-relaxed text-slate-600">
              Maksimal 20 MB. Format yang diterima mencakup PDF, Word, PowerPoint, Excel, ZIP, RAR, TXT, PNG, dan JPG.
            </p>

            <button type="submit"
                    class="{{ $tombol }} {{ $fokus }} mt-3 w-full border-0 bg-[#173d6e]
                           text-white hover:bg-[#2b62a8] sm:w-auto">
              <i class="fa-solid fa-upload" aria-hidden="true"></i>
              Kirim jawaban
            </button>
          </form>
        @endif
      </section>
    </div>
  </div>
@endsection