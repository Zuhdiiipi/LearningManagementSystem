@extends('layouts.panel')

@php
  $berlangsung = $kuis->sedangDibuka();
  $jumlah = $mahasiswa->count();
  $selesai = $mahasiswa->filter(fn ($mhs) => $hasil->has($mhs->id))->count();
  $urutan = $mahasiswa->sortByDesc(fn ($mhs) => $hasil[$mhs->id]->skor ?? -1);

  $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
  $tombol = "inline-flex min-h-11 items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold $fokus";
  $utama = "$tombol bg-[#173d6e] text-white hover:bg-[#2b62a8]";
  $sekunder = "$tombol border border-slate-300 bg-white text-[#173d6e] hover:bg-slate-50";
@endphp

@section('title', 'Live Score - ' . $kuis->judul)
@section('kembali', route('dosen.kelas.show', [$kelas, 'tab' => 'kuis']))

@section('content')
  <div class="space-y-4">
    <header class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-[#acd6fb] bg-[#dfeffe] p-4 sm:p-5">
      <div class="min-w-0 [overflow-wrap:anywhere]">
        <p class="text-xs font-semibold text-[#173d6e]">Live Score</p>
        <h1 class="mt-0.5 text-xl font-extrabold tracking-tight text-[#173d6e] sm:text-2xl">
          {{ $kuis->judul }}
        </h1>

        <p class="mt-1 text-sm text-slate-700">
          {{ $kuis->mulai->translatedFormat('j M Y, H:i') }} · {{ $kuis->durasi_menit }} menit
        </p>

        <p class="mt-2 inline-flex rounded-full bg-white px-2.5 py-1 text-xs font-semibold
                  {{ $berlangsung ? 'text-emerald-800' : 'text-slate-700' }}">
          {{ $berlangsung ? 'Terbuka sampai ' . $kuis->selesai->format('H:i') : $kuis->status() }}
        </p>
      </div>

      <div id="aksi-skor" class="flex flex-wrap gap-2">
        <form method="POST" action="{{ route($berlangsung ? 'dosen.kuis.tutup' : 'dosen.kuis.buka', $kuis) }}"
              @if ($berlangsung)
                data-konfirmasi-judul="Tutup Sesi"
                data-konfirmasi="Tutup kuis {{ $kuis->judul }}? Mahasiswa tidak bisa melanjutkan pengerjaan."
              @endif>
          @csrf
          <button type="submit" class="{{ $berlangsung ? $sekunder : $utama }}">
            <i class="fa-solid {{ $berlangsung ? 'fa-stop' : 'fa-play' }}" aria-hidden="true"></i>
            {{ $berlangsung ? 'Tutup Sesi' : 'Buka Sesi' }}
          </button>
        </form>

        <form method="POST" action="{{ route('dosen.kuis.umumkan', $kuis) }}">
          @csrf
          <button type="submit" class="{{ $sekunder }}">
            <i class="fa-regular fa-envelope" aria-hidden="true"></i>
            Umumkan via Email
          </button>
        </form>
      </div>
    </header>

    <section class="min-w-0 rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="judul-skor">
      <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 sm:px-5">
        <div>
          <h2 id="judul-skor" class="text-base font-bold text-[#173d6e]">Skor Mahasiswa</h2>
          @if ($berlangsung)
            <p class="mt-0.5 text-xs text-slate-600">Diperbarui otomatis setiap 15 detik.</p>
          @endif
        </div>

        <p class="rounded-full bg-[#dfeffe] px-3 py-1.5 text-xs font-semibold text-[#173d6e]">
          <span class="tabular-nums">{{ $selesai }}/{{ $jumlah }}</span> sudah mengerjakan
        </p>
      </div>

      <table class="w-full table-fixed text-sm">
        <thead class="bg-[#f4f7fb] text-[#173d6e]">
          <tr>
            <th scope="col" class="px-4 py-2.5 text-left font-semibold sm:px-5">Nama Mahasiswa</th>
            <th scope="col" class="w-32 px-4 py-2.5 text-right font-semibold sm:w-48 sm:px-5">Skor</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          @forelse ($urutan as $mhs)
            @php $skor = $hasil[$mhs->id]->skor ?? null; @endphp

            <tr>
              <th scope="row" class="px-4 py-3 text-left font-semibold text-slate-800 [overflow-wrap:anywhere] sm:px-5">
                {{ $mhs->name }}
              </th>

              <td class="px-4 py-3 text-right sm:px-5">
                @if ($skor !== null)
                  <span class="inline-flex rounded-lg bg-[#dfeffe] px-2.5 py-1 font-bold tabular-nums text-[#173d6e]">
                    {{ rtrim(rtrim(number_format($skor, 2, ',', ''), '0'), ',') }} / 100
                  </span>
                @else
                  <span class="text-xs text-slate-600">Belum mengerjakan</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="2" class="px-4 py-5 text-slate-600 sm:px-5">
                Belum ada mahasiswa di kelas ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </section>
  </div>
@endsection

@if ($berlangsung)
  @push('scripts')
    <script>
      (() => {
        const aksi = document.getElementById('aksi-skor');

        const timer = window.setInterval(() => {
          if (!document.querySelector('dialog[open]')) {
            window.location.reload();
          }
        }, 15000);

        document.addEventListener('submit', event => {
          if (!event.defaultPrevented && aksi.contains(event.target)) {
            window.clearInterval(timer);
          }
        });
      })();
    </script>
  @endpush
@endif