@extends('layouts.panel')

@section('title', $kuis->judul)

@section('content')
  @php
    $jumlahSoal = $soal->count();
    $poin = $jumlahSoal ? round(100 / $jumlahSoal, 2) : 0;
    $panel = 'min-w-0 rounded-xl border border-solid border-slate-200 bg-white shadow-sm';
    $tombol = 'min-h-11 cursor-pointer rounded-lg px-4 py-2 font-sans text-sm font-semibold
               focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
    $utama = 'border-0 bg-[#173d6e] text-white hover:bg-[#2b62a8] disabled:cursor-not-allowed disabled:opacity-60';
  @endphp

  <form method="POST" action="{{ route('mahasiswa.kuis.submit', $kuis) }}" id="form-kuis"
        data-sisa-detik="{{ $sisaDetik }}" data-jumlah-soal="{{ $jumlahSoal }}"
        class="mx-auto max-w-4xl space-y-4 text-[#173d6e]">
    @csrf

    <header class="rounded-xl border border-solid border-[#acd6fb] bg-[#dfeffe] p-4 sm:px-5">
      <h1 class="m-0 text-xl font-extrabold tracking-tight [overflow-wrap:anywhere] sm:text-2xl">
        {{ $kuis->judul }}
      </h1>

      @if ($kuis->deskripsi)
        <p class="mt-1.5 mb-0 text-sm leading-relaxed whitespace-pre-wrap
                  text-slate-700 [overflow-wrap:anywhere]">{{ $kuis->deskripsi }}</p>
      @endif
    </header>

    <div class="{{ $panel }} sticky top-2 z-20 flex flex-wrap items-center justify-between gap-2 px-4 py-2.5">
      <p class="m-0 text-sm text-slate-600">
        <strong id="jumlah-dijawab" class="tabular-nums text-[#173d6e]">0 / {{ $jumlahSoal }}</strong>
        soal dijawab
      </p>

      <div id="timer-kuis" class="flex items-center gap-2 rounded-lg bg-[#dfeffe] px-2.5 py-1.5 text-sm
                                 data-[mendesak=true]:bg-red-50 data-[mendesak=true]:text-red-800">
        <i class="fa-regular fa-clock" aria-hidden="true"></i>
        <span>Sisa waktu</span>
        <span id="timer" role="timer" class="font-bold tabular-nums">--:--:--</span>
      </div>
    </div>

    @forelse ($soal as $s)
      <article class="{{ $panel }} p-4 sm:px-5">
        <div class="mb-3 flex flex-wrap items-start justify-between gap-2">
          <h2 id="soal-{{ $s->id }}"
              class="m-0 flex min-w-0 flex-1 basis-full gap-2 text-base leading-snug font-bold sm:basis-0">
            <span class="shrink-0 text-[#2b62a8]">{{ $loop->iteration }}.</span>
            <span class="min-w-0 whitespace-pre-wrap [overflow-wrap:anywhere]">{{ $s->pertanyaan }}</span>
          </h2>

          <span class="ml-5 rounded-full bg-[#f4f7fb] px-2.5 py-1 text-xs font-semibold sm:ml-0">
            {{ str_replace('.', ',', (string) $poin) }} poin
          </span>
        </div>

        <fieldset aria-labelledby="soal-{{ $s->id }}" class="m-0 grid min-w-0 gap-2 border-0 p-0">
          @foreach (['a', 'b', 'c', 'd', 'e'] as $o)
            @php $opsi = $s->{'opsi_' . $o}; @endphp
            @continue($opsi === null || $opsi === '')

            <label class="flex min-h-11 cursor-pointer items-start gap-2.5 rounded-lg border border-solid
                          border-slate-200 px-3 py-2.5 text-sm text-slate-800 hover:bg-[#f4f7fb]
                          has-checked:border-[#2b62a8] has-checked:bg-[#dfeffe]
                          focus-within:ring-2 focus-within:ring-[#acd6fb]">
              <input type="radio" name="jawaban[{{ $s->id }}]" value="{{ $o }}"
                     class="mt-0.5 shrink-0 accent-[#173d6e]">
              <span class="min-w-0 whitespace-pre-wrap [overflow-wrap:anywhere]">{{ $opsi }}</span>
            </label>
          @endforeach
        </fieldset>
      </article>
    @empty
      <p class="{{ $panel }} m-0 p-4 text-sm text-slate-600">Belum ada soal pada kuis ini.</p>
    @endforelse

    <div class="{{ $panel }} flex flex-wrap items-center justify-between gap-3 p-4">
      <p class="m-0 text-sm text-slate-600">Jawaban dikirim otomatis saat waktu habis.</p>

      <button type="submit" id="tombol-kirim-kuis" class="{{ $tombol }} {{ $utama }} w-full sm:w-auto">
        Kirim Jawaban
      </button>
    </div>
  </form>

  <dialog id="konfirmasi-kuis" aria-labelledby="judul-konfirmasi-kuis"
          aria-describedby="ringkasan-kuis pesan-konfirmasi-kuis"
          class="m-auto w-[calc(100%_-_2rem)] max-w-sm rounded-2xl border border-solid border-slate-200
                 bg-white p-0 text-[#173d6e] shadow-xl backdrop:bg-slate-900/45">
    <div class="p-5">
      <h2 id="judul-konfirmasi-kuis" class="m-0 text-lg font-bold">Kirim jawaban?</h2>
      <p id="ringkasan-kuis" class="mt-2 mb-0 text-sm text-slate-700"></p>
      <p id="pesan-konfirmasi-kuis" class="mt-1 mb-0 text-sm text-slate-600">
        Jawaban tidak bisa diubah setelah dikirim.
      </p>

      <form method="dialog" class="mt-4 flex flex-wrap justify-end gap-2">
        <button type="submit" value="batal" autofocus
                class="{{ $tombol }} border border-solid border-slate-300 bg-white
                       text-[#173d6e] hover:bg-[#f4f7fb]">
          Batal
        </button>

        <button type="submit" value="kirim" class="{{ $tombol }} {{ $utama }}">
          Kirim Jawaban
        </button>
      </form>
    </div>
  </dialog>
@endsection

@push('scripts')
  <script>
    (() => {
      const form = document.getElementById('form-kuis');
      const timer = document.getElementById('timer');
      const panelTimer = document.getElementById('timer-kuis');
      const jumlahDijawab = document.getElementById('jumlah-dijawab');
      const tombolKirim = document.getElementById('tombol-kirim-kuis');
      const dialog = document.getElementById('konfirmasi-kuis');
      const ringkasan = document.getElementById('ringkasan-kuis');
      const jumlahSoal = Number(form.dataset.jumlahSoal);
      const batas = Date.now() + Number(form.dataset.sisaDetik) * 1000;
      let mengirim = false;

      const hitungJawaban = () => form.querySelectorAll('input[type="radio"]:checked').length;
      const duaDigit = angka => String(angka).padStart(2, '0');

      function tampilkanJumlah() {
        jumlahDijawab.textContent = hitungJawaban() + ' / ' + jumlahSoal;
      }

      function kirim() {
        if (mengirim) return;

        mengirim = true;
        clearInterval(interval);
        tombolKirim.disabled = true;
        tombolKirim.textContent = 'Mengirim…';

        if (dialog.open) dialog.close();

        form.submit();
      }

      function perbaruiTimer() {
        if (mengirim) return;

        const sisa = Math.max(0, Math.ceil((batas - Date.now()) / 1000));

        timer.textContent = [
          Math.floor(sisa / 3600),
          Math.floor((sisa % 3600) / 60),
          sisa % 60,
        ].map(duaDigit).join(':');

        panelTimer.dataset.mendesak = sisa <= 60 ? 'true' : 'false';

        if (sisa === 0) kirim();
      }

      form.addEventListener('change', tampilkanJumlah);

      form.addEventListener('submit', event => {
        event.preventDefault();
        perbaruiTimer();

        if (mengirim || dialog.open) return;

        ringkasan.textContent = 'Kamu sudah menjawab ' + hitungJawaban() + ' dari ' + jumlahSoal + ' soal.';
        dialog.returnValue = '';
        dialog.showModal();
      });

      dialog.addEventListener('close', () => {
        if (dialog.returnValue === 'kirim') kirim();
      });

      document.addEventListener('visibilitychange', perbaruiTimer);

      const interval = setInterval(perbaruiTimer, 1000);
      tampilkanJumlah();
      perbaruiTimer();
    })();
  </script>
@endpush