@extends('layouts.panel')

@php
  $judulHalaman = $kuis->exists ? 'Ubah Kuis' : 'Tambah Kuis';
  $berlangsung = $kuis->exists && $kuis->sedangDibuka();
  $jadwal = $kuis->mulai?->format('Y-m-d\TH:i');

  $daftarSoal = old('soal', $soal->map(fn ($s) => $s->getAttributes())->all());
  $daftarSoal = is_array($daftarSoal) ? array_values(array_filter($daftarSoal, 'is_array')) : [];
  $opsi = ['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E'];

  $panel = 'min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5';
  $label = 'mb-1.5 block text-sm font-semibold text-[#173d6e]';
  $input = 'min-h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2 text-base
            text-slate-900 focus:border-[#2b62a8] focus:outline-none focus:ring-2 focus:ring-[#acd6fb]
            read-only:bg-slate-100';
  $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
@endphp

@section('title', $judulHalaman)
@section('kembali', route('dosen.kelas.show', [$kelas, 'tab' => 'kuis']))

@section('content')
  <form id="form-quiz" method="POST"
        action="{{ $kuis->exists ? route('dosen.kuis.update', $kuis) : route('dosen.kuis.store', $kelas) }}"
        class="mx-auto max-w-4xl space-y-4">
    @csrf
    @if ($kuis->exists)
      @method('PUT')
    @endif

    <header class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-[#acd6fb] bg-[#dfeffe] p-4 sm:p-5">
      <div class="min-w-0 [overflow-wrap:anywhere]">
        <h1 class="text-xl font-extrabold tracking-tight text-[#173d6e] sm:text-2xl">
          {{ $judulHalaman }}
        </h1>
        <p class="mt-1 text-sm text-slate-700">{{ $kelas->mataKuliah->nama }} · {{ $kelas->nama }}</p>
      </div>

      <button type="submit" class="{{ $fokus }} inline-flex min-h-11 items-center gap-2 rounded-lg bg-[#173d6e]
                                   px-4 py-2 text-sm font-semibold text-white hover:bg-[#2b62a8]">
        <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
        Simpan Kuis
      </button>
    </header>

    <section class="{{ $panel }}">
      <h2 class="mb-3 text-base font-bold text-[#173d6e]">Pengaturan Kuis</h2>

      <div class="grid gap-4 sm:grid-cols-2">
        <div class="min-w-0 sm:col-span-2">
          <label for="judul" class="{{ $label }}">Judul Kuis</label>
          <input id="judul" name="judul" type="text" value="{{ old('judul', $kuis->judul) }}"
                 maxlength="150" class="{{ $input }}" required>
        </div>

        <div class="min-w-0">
          <label for="mulai" class="{{ $label }}">Jadwal Kuis</label>
          <input id="mulai" name="mulai" type="datetime-local"
                 value="{{ $berlangsung ? $jadwal : old('mulai', $jadwal) }}"
                 class="{{ $input }}" aria-describedby="petunjuk-jadwal" @readonly($berlangsung) required>
          <p id="petunjuk-jadwal" class="mt-1 text-xs text-slate-600">
            {{ $berlangsung ? 'Jadwal terkunci selama sesi berlangsung.' : 'Kuis dimulai saat dosen menekan Buka Sesi.' }}
          </p>
        </div>

        <div class="min-w-0">
          <label for="durasi_menit" class="{{ $label }}">Batas Waktu (menit)</label>
          <input id="durasi_menit" name="durasi_menit" type="number" min="1" max="600"
                 value="{{ old('durasi_menit', $kuis->durasi_menit) }}" class="{{ $input }}" required>
        </div>
      </div>
    </section>

    @if ($terkunci)
      <section class="{{ $panel }}">
        <h2 class="text-base font-bold text-[#173d6e]">Daftar Soal</h2>
        <p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-sm text-amber-900">
          Soal terkunci karena sudah ada mahasiswa yang mengerjakan kuis. Judul dan durasi masih bisa diubah.
        </p>

        <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-slate-700">
          @foreach ($soal as $s)
            <li class="whitespace-pre-wrap [overflow-wrap:anywhere]">{{ $s->pertanyaan }}</li>
          @endforeach
        </ol>
      </section>
    @else
      <section aria-labelledby="judul-soal" class="space-y-3">
        <div>
          <h2 id="judul-soal" class="text-base font-bold text-[#173d6e]">Daftar Soal</h2>
          <p class="mt-1 text-sm text-slate-600">
            Tandai satu jawaban benar per soal. Pilihan A dan B wajib diisi. Pilihan C sampai E opsional.
          </p>
        </div>

        <div id="daftar-soal" class="space-y-3">
          @foreach ($daftarSoal ?: [[]] as $i => $s)
            <article data-soal class="{{ $panel }}">
              <div class="mb-3 flex items-center justify-between gap-2">
                <h3 data-nomor class="text-sm font-bold text-[#173d6e]">Soal {{ $loop->iteration }}</h3>

                <button type="button" data-hapus aria-label="Hapus soal {{ $loop->iteration }}"
                        class="{{ $fokus }} grid size-10 shrink-0 place-items-center rounded-lg text-red-700
                               hover:bg-red-50 disabled:opacity-40" @disabled($loop->count === 1)>
                  <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                </button>
              </div>

              <label class="{{ $label }}">
                Pertanyaan
                <textarea name="soal[{{ $i }}][pertanyaan]" rows="2" maxlength="2000" required
                          class="{{ $input }} mt-1.5 block min-h-24 resize-y font-normal">{{ $s['pertanyaan'] ?? '' }}</textarea>
              </label>

              <fieldset class="mt-4 min-w-0">
                <legend class="{{ $label }}">Pilihan Jawaban</legend>

                <div class="grid gap-2 sm:grid-cols-2">
                  @foreach ($opsi as $o => $huruf)
                    <div class="flex min-w-0 items-center gap-2 rounded-lg border border-slate-200 px-2
                                has-checked:border-[#2b62a8] has-checked:bg-[#dfeffe]">
                      <label class="flex min-h-11 shrink-0 cursor-pointer items-center gap-1.5 text-sm font-semibold text-[#173d6e]">
                        <input type="radio" name="soal[{{ $i }}][jawaban]" value="{{ $o }}"
                               class="size-4 accent-[#173d6e]" aria-label="Pilihan {{ $huruf }} sebagai jawaban benar"
                               @checked(($s['jawaban'] ?? null) === $o) @required($o === 'a')>
                        <span aria-hidden="true">{{ $huruf }}</span>
                      </label>

                      <input type="text" name="soal[{{ $i }}][opsi_{{ $o }}]" value="{{ $s['opsi_' . $o] ?? '' }}"
                             aria-label="Teks pilihan {{ $huruf }}" maxlength="255" @required($o === 'a' || $o === 'b')
                             placeholder="{{ $o === 'a' || $o === 'b' ? 'Tulis pilihan' : 'Opsional' }}"
                             class="{{ $input }} my-1">
                    </div>
                  @endforeach
                </div>
              </fieldset>
            </article>
          @endforeach
        </div>

        <button type="button" id="tambah-soal" class="{{ $fokus }} inline-flex min-h-11 items-center gap-2 rounded-lg
                                                      border border-[#acd6fb] bg-white px-4 py-2 text-sm font-semibold
                                                      text-[#173d6e] hover:bg-[#dfeffe]">
          <i class="fa-solid fa-plus" aria-hidden="true"></i>
          Tambah Soal
        </button>
      </section>
    @endif
  </form>
@endsection

@if (! $terkunci)
  @push('scripts')
    <script>
      (() => {
        const daftar = document.getElementById('daftar-soal');
        const tambah = document.getElementById('tambah-soal');
        const pola = daftar.firstElementChild.cloneNode(true);
        let indeksBerikut = daftar.children.length;

        function perbaruiNomor() {
          const jumlah = daftar.children.length;

          [...daftar.children].forEach((soal, indeks) => {
            const nomor = indeks + 1;
            soal.querySelector('[data-nomor]').textContent = 'Soal ' + nomor;

            const hapus = soal.querySelector('[data-hapus]');
            hapus.setAttribute('aria-label', 'Hapus soal ' + nomor);
            hapus.disabled = jumlah === 1;
          });
        }

        function perbaruiPilihanWajib(soal) {
          const benar = soal.querySelector('input[type="radio"]:checked')?.value;

          soal.querySelectorAll('input[type="text"]').forEach(input => {
            const opsi = input.name.match(/\[opsi_([a-e])\]$/)[1];
            input.required = opsi === 'a' || opsi === 'b' || opsi === benar;
          });
        }

        tambah.addEventListener('click', () => {
          const soal = pola.cloneNode(true);

          soal.querySelectorAll('[name]').forEach(input => {
            input.name = input.name.replace(/^soal\[\d+\]/, 'soal[' + indeksBerikut + ']');

            if (input.type === 'radio') {
              input.defaultChecked = false;
              input.checked = false;
            } else {
              input.defaultValue = '';
              input.value = '';
            }
          });

          indeksBerikut++;
          daftar.append(soal);
          perbaruiPilihanWajib(soal);
          perbaruiNomor();
          soal.querySelector('textarea').focus();
        });

        daftar.addEventListener('click', event => {
          const tombol = event.target.closest('[data-hapus]');
          if (!tombol || daftar.children.length === 1) return;

          const soal = tombol.closest('[data-soal]');
          const tujuanFokus = soal.nextElementSibling || soal.previousElementSibling;

          soal.remove();
          perbaruiNomor();
          tujuanFokus.querySelector('textarea').focus();
        });

        daftar.addEventListener('change', event => {
          if (event.target.matches('input[type="radio"]')) {
            perbaruiPilihanWajib(event.target.closest('[data-soal]'));
          }
        });

        [...daftar.children].forEach(perbaruiPilihanWajib);
      })();
    </script>
  @endpush
@endif