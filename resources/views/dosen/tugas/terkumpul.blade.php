@extends('layouts.panel')

@php
  $jenis = $tugas->jenis === 'praktikum' ? 'Praktikum' : 'Tugas';
  $panel = 'min-w-0 rounded-xl border border-slate-200 bg-white shadow-sm';
  $input = 'min-h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2 text-base
            text-slate-900 focus:border-[#2b62a8] focus:outline-none focus:ring-2 focus:ring-[#acd6fb]
            aria-invalid:border-red-500';
  $label = 'mb-1.5 block text-sm font-semibold text-[#173d6e]';
  $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
@endphp

@section('title', 'Pengumpulan ' . $jenis)
@section('kembali', route('dosen.tugas.show', $tugas))

@section('content')
  <div class="space-y-4">
    <header class="flex flex-wrap items-start justify-between gap-3 rounded-xl border border-[#acd6fb] bg-[#dfeffe] p-4 sm:p-5">
      <div class="min-w-0 [overflow-wrap:anywhere]">
        <p class="text-xs font-semibold text-[#173d6e]">Pengumpulan {{ $jenis }}</p>
        <h1 class="mt-0.5 text-xl font-extrabold tracking-tight text-[#173d6e] sm:text-2xl">{{ $tugas->judul }}</h1>
        <p class="mt-1 text-sm text-slate-700">
          Batas pengumpulan {{ $tugas->deadline->copy()->locale('id')->translatedFormat('j F Y, H:i') }}
        </p>
      </div>

      <div class="flex flex-wrap gap-2 text-xs font-semibold">
        <span class="rounded-full bg-white px-3 py-1.5 text-emerald-800">{{ $pengumpulan->count() }} mengumpulkan</span>
        <span class="rounded-full bg-white px-3 py-1.5 text-slate-700">{{ $belum->count() }} belum mengumpulkan</span>
      </div>
    </header>

    <section class="{{ $panel }}" aria-labelledby="judul-terkumpul">
      <h2 id="judul-terkumpul" class="border-b border-slate-100 px-4 py-3 text-base font-bold text-[#173d6e] sm:px-5">
        Sudah Mengumpulkan
      </h2>

      <ul class="divide-y divide-slate-100">
        @forelse ($pengumpulan as $k)
          @php
            $k->setRelation('tugas', $tugas);
            $terlambat = $k->terlambat();
            $inputLama = (string) old('_pengumpulan') === (string) $k->id;
          @endphp

          <li class="grid gap-4 p-4 sm:p-5 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
            <div class="min-w-0 space-y-2 [overflow-wrap:anywhere]">
              <h3 class="font-semibold text-[#173d6e]">{{ $k->mahasiswa->name }}</h3>

              <div class="[&_a]:no-underline">
                @include('mahasiswa._file', [
                  'judul' => $k->file_name ?: 'File jawaban',
                  'url' => route('file.pengumpulan', $k),
                  'namaFile' => $k->file_name,
                ])
              </div>

              <div class="flex flex-wrap items-center gap-2 text-xs text-slate-600">
                <span>Dikirim {{ $k->waktuKumpul()->copy()->locale('id')->translatedFormat('j M Y, H:i') }}</span>
                <span @class([
                  'rounded-full px-2 py-1 font-semibold',
                  'bg-red-50 text-red-800' => $terlambat,
                  'bg-emerald-50 text-emerald-800' => ! $terlambat,
                ])>
                  {{ $terlambat ? 'Terlambat ' . $k->lamaTerlambat() : 'Tepat waktu' }}
                </span>
              </div>

              @if (filled($k->catatan))
                <div class="text-sm text-slate-700">
                  <p class="font-semibold">Catatan mahasiswa</p>
                  <p class="whitespace-pre-wrap">{{ $k->catatan }}</p>
                </div>
              @endif
            </div>

            <form method="POST" action="{{ route('dosen.pengumpulan.nilai', $k) }}"
                  class="min-w-0 rounded-lg bg-[#f4f7fb] p-3" aria-label="Penilaian {{ $k->mahasiswa->name }}">
              @csrf @method('PATCH')
              <input type="hidden" name="_pengumpulan" value="{{ $k->id }}">

              <div class="grid items-start gap-3 sm:grid-cols-[6.5rem_minmax(0,1fr)]">
                <div>
                  <label for="nilai-{{ $k->id }}" class="{{ $label }}">Nilai / 100</label>
                  <input id="nilai-{{ $k->id }}" type="number" name="nilai" min="0" max="100" step="1" required
                         value="{{ $inputLama ? old('nilai', $k->nilai) : $k->nilai }}"
                         class="{{ $input }}" @if ($inputLama && $errors->has('nilai')) aria-invalid="true" @endif>
                  @if ($inputLama)
                    @error('nilai')
                      <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                  @endif
                </div>

                <div class="min-w-0">
                  <label for="feedback-{{ $k->id }}" class="{{ $label }}">
                    Umpan Balik <span class="font-normal text-slate-600">(opsional)</span>
                  </label>
                  <textarea id="feedback-{{ $k->id }}" name="feedback" rows="2" maxlength="1000"
                            class="{{ $input }} resize-y"
                            @if ($inputLama && $errors->has('feedback')) aria-invalid="true" @endif>{{ $inputLama ? old('feedback', $k->feedback) : $k->feedback }}</textarea>
                  @if ($inputLama)
                    @error('feedback')
                      <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                  @endif
                </div>
              </div>

              <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                <span class="text-xs font-medium text-slate-600">
                  {{ $k->nilai === null ? 'Belum dinilai' : 'Nilai tersimpan' }}
                </span>
                <button type="submit" class="{{ $fokus }} min-h-10 rounded-lg bg-[#173d6e] px-3 py-2 text-sm font-semibold text-white hover:bg-[#2b62a8]">
                  Simpan Nilai
                </button>
              </div>
            </form>
          </li>
        @empty
          <li class="px-4 py-5 text-sm text-slate-600 sm:px-5">Belum ada mahasiswa yang mengumpulkan.</li>
        @endforelse
      </ul>
    </section>

    <section class="{{ $panel }}" aria-labelledby="judul-belum">
      <h2 id="judul-belum" class="border-b border-slate-100 px-4 py-3 text-base font-bold text-[#173d6e] sm:px-5">
        Belum mengumpulkan
      </h2>

      <ul class="divide-y divide-slate-100">
        @forelse ($belum as $mhs)
          <li class="flex flex-wrap items-center justify-between gap-1 px-4 py-3 text-sm sm:px-5">
            <span class="min-w-0 font-semibold text-[#173d6e] [overflow-wrap:anywhere]">{{ $mhs->name }}</span>
            @if (filled($mhs->nim_nidn))
              <span class="text-slate-600">{{ $mhs->nim_nidn }}</span>
            @endif
          </li>
        @empty
          <li class="px-4 py-5 text-sm text-slate-600 sm:px-5">
            {{ $pengumpulan->isEmpty() ? 'Belum ada mahasiswa di kelas ini.' : 'Semua mahasiswa sudah mengumpulkan.' }}
          </li>
        @endforelse
      </ul>
    </section>
  </div>
@endsection