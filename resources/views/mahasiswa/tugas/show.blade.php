@extends('layouts.panel', ['menuAktif' => 'tugas'])

@section('title', $tugas->judul)
@section('kembali', route('mahasiswa.kelas.show', [$kelas, 'tab' => 'tugas']))

@section('content')
  @php
    $jenis = ucfirst($tugas->jenis);
    $lewatTenggat = $tugas->sudahLewat();
    $sudahDinilai = $kumpul && $kumpul->nilai !== null;
    $terlambat = $kumpul?->terlambat();

    $errorFile = $errors->first('files')
      ?: $errors->first('files.*')
      ?: $errors->first('file')
      ?: $errors->first('jumlah_file');

    $formatWaktu = static fn ($waktu) =>
      $waktu?->copy()->locale('id')->translatedFormat('j F Y, H:i') ?? '—';

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
        <h2 id="judul-instruksi" class="m-0 text-base font-bold">
          Instruksi {{ strtolower($jenis) }}
        </h2>

        <p class="mt-2 mb-0 text-sm leading-7 whitespace-pre-wrap text-slate-700
                  [overflow-wrap:anywhere]">{{ $tugas->instruksi }}</p>

        @include('partials.lampiran-tugas', ['ruteTugas' => 'mahasiswa.tugas.show'])
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
            <time datetime="{{ $tugas->deadline->toIso8601String() }}">
              {{ $formatWaktu($tugas->deadline) }}
            </time>
          </p>

          <p class="mt-1 mb-0 text-xs font-medium {{ $lewatTenggat ? 'text-red-700' : 'text-slate-600' }}">
            {{ $lewatTenggat ? 'Tenggat sudah lewat' : $tugas->deadline->copy()->locale('id')->diffForHumans() }}
          </p>
        </div>

        @if ($kumpul)
          <div class="mt-4 space-y-2">
            <ul class="grid gap-2">
              @foreach ($kumpul->daftarLampiran() as $indeks => $file)
                <li>
                  <a href="{{ route('file.pengumpulan', [$kumpul, 'lampiran' => $indeks]) }}"
                     class="{{ $tautanFile }} {{ $fokus }}">
                    <i class="fa-solid fa-download shrink-0" aria-hidden="true"></i>
                    <span class="sr-only">Unduh jawaban</span>
                    <span class="min-w-0 [overflow-wrap:anywhere]">{{ $file['nama'] }}</span>
                  </a>
                </li>
              @endforeach
            </ul>

            <p class="text-sm text-slate-600">
              Dikirim {{ $formatWaktu($kumpul->waktuKumpul()) }}
            </p>

            <p class="text-sm font-semibold {{ $terlambat ? 'text-red-700' : 'text-emerald-700' }}">
              {{ $terlambat ? 'Terlambat ' . $kumpul->lamaTerlambat() : 'Tepat waktu' }}
            </p>
          </div>

          @if ($sudahDinilai)
            <div class="mt-4 rounded-lg bg-[#dfeffe] p-3">
              <p class="m-0 text-sm font-semibold">Nilai</p>

              <p class="mt-1 mb-0 text-sm font-semibold">
                <strong class="text-2xl">{{ $kumpul->nilai }}</strong> / 100
              </p>

              @if (filled($kumpul->feedback))
                <p class="mt-2 mb-0 text-sm font-semibold">Catatan dosen</p>
                <p class="mt-1 mb-0 text-sm whitespace-pre-wrap
                          [overflow-wrap:anywhere]">{{ $kumpul->feedback }}</p>
              @endif
            </div>
          @else
            <form method="POST" action="{{ route('mahasiswa.tugas.batal', $tugas) }}" class="mt-4"
                  data-konfirmasi-judul="Batal Kirim"
                  data-konfirmasi="Semua file dalam pengiriman ini akan dihapus. Kamu bisa mengirim ulang jawaban.">
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
            <p class="mt-3 mb-0 rounded-lg border border-solid border-amber-200 bg-amber-50
                      p-3 text-sm text-amber-900">
              Kamu masih bisa mengirim jawaban. Pengumpulan akan ditandai <strong>Terlambat</strong>.
            </p>
          @endif

          <form method="POST" action="{{ route('mahasiswa.tugas.kumpul', $tugas) }}"
                enctype="multipart/form-data" class="mt-4">
            @csrf

            <label for="file-jawaban" class="mb-2 block text-sm font-semibold">
              File jawaban
            </label>

            <input type="hidden" name="jumlah_file">

            <div id="pilihan-file-jawaban" class="space-y-2">
              <input id="file-jawaban" type="file" name="files[]" multiple required
                     accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt,.png,.jpg,.jpeg"
                     aria-describedby="format-jawaban{{ $errorFile ? ' error-jawaban' : '' }}"
                     @if ($errorFile) aria-invalid="true" @endif
                     class="{{ $fokus }} box-border min-w-0 w-full rounded-lg border border-solid
                            border-slate-300 bg-white p-1.5 font-sans text-sm text-slate-700
                            aria-invalid:border-red-400 file:mr-2 file:cursor-pointer file:rounded-md
                            file:border-0 file:bg-[#dfeffe] file:px-3 file:py-2
                            file:font-semibold file:text-[#173d6e]">
            </div>

            <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1">
              <button id="tambah-file-jawaban" type="button" hidden
                      class="{{ $fokus }} cursor-pointer rounded-md border border-[#acd6fb]
                             bg-white px-3 py-2 text-xs font-semibold hover:bg-[#dfeffe]">
                <i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah pilihan file
              </button>

              <p id="status-file-jawaban" class="text-xs text-slate-600" aria-live="polite"></p>
            </div>

            @if ($errorFile)
              <p id="error-jawaban" class="mt-2 mb-0 text-sm text-red-700">{{ $errorFile }}</p>
            @endif

            <p id="format-jawaban" class="mt-2 mb-0 text-xs leading-relaxed text-slate-600">
              Pilih satu atau beberapa file. Maksimal 20 MB per file.
              Format yang diterima mencakup PDF, Word, PowerPoint, Excel, ZIP, RAR, TXT, PNG, dan JPG.
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

@unless ($kumpul)
  @push('scripts')
    <script>
      (() => {
        const awal = document.getElementById('file-jawaban');
        const wadah = document.getElementById('pilihan-file-jawaban');
        const tambah = document.getElementById('tambah-file-jawaban');
        const status = document.getElementById('status-file-jawaban');

        const hitung = () => {
          const jumlah = [...wadah.querySelectorAll('input[type="file"]')]
            .reduce((total, input) => total + input.files.length, 0);

          awal.form.elements.jumlah_file.value = jumlah;
          awal.required = jumlah === 0;
          status.textContent = jumlah ? `${jumlah} file dipilih` : '';
        };

        tambah.addEventListener('click', () => {
          const baris = document.createElement('div');
          baris.className = 'flex items-center gap-2';

          const input = awal.cloneNode();
          input.value = '';
          input.removeAttribute('id');
          input.removeAttribute('aria-invalid');
          input.setAttribute('aria-label', 'File jawaban tambahan');
          input.required = false;

          const hapus = document.createElement('button');
          hapus.type = 'button';
          hapus.className = 'shrink-0 cursor-pointer rounded-md border-0 bg-white p-2 text-xs font-semibold text-red-700 hover:bg-red-50';
          hapus.textContent = 'Hapus';
          hapus.setAttribute('aria-label', 'Hapus pilihan file tambahan');

          hapus.addEventListener('click', () => {
            baris.remove();
            hitung();
            tambah.focus();
          });

          baris.append(input, hapus);
          wadah.append(baris);
          input.focus();
        });

        wadah.addEventListener('change', hitung);
        awal.form.addEventListener('submit', hitung);
        tambah.hidden = false;
        hitung();
      })();
    </script>
  @endpush
@endunless