@extends('layouts.panel')

@section('title', 'Kehadiran Pertemuan ' . $pertemuan->nomor)
@section('kembali', route('dosen.kelas.show', [$kelas, 'tab' => 'absensi']))

@section('content')
  @php
    $dibuka = $sesi?->sedangDibuka() ?? false;
    $ditutup = $sesi?->sudahDitutup() ?? false;
    $statusDefault = $ditutup ? 'alpa' : '';

    $pilihan = [
      'hadir' => ['Hadir', 'bg-emerald-50 text-emerald-800'],
      'izin' => ['Izin', 'bg-blue-50 text-blue-800'],
      'sakit' => ['Sakit', 'bg-amber-50 text-amber-900'],
      'alpa' => ['Alpa', 'bg-red-50 text-red-800'],
      '' => ['Belum absen', 'bg-slate-100 text-slate-700'],
    ];

    $jumlahStatus = $mahasiswa->countBy(fn ($mhs) => $absensi[$mhs->id]->status ?? $statusDefault);
    $tanggal = $pertemuan->tanggal?->copy()->locale('id')->translatedFormat('l, j F Y');
    $panel = 'min-w-0 rounded-xl border border-solid border-slate-200 bg-white shadow-sm';
    $tombol = 'inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg px-4 py-2
               font-sans text-sm font-semibold focus-visible:outline-2 focus-visible:outline-offset-2
               focus-visible:outline-[#2b62a8]';
    $utama = 'border-0 bg-[#173d6e] text-white hover:bg-[#2b62a8]';
    $sekunder = 'border border-solid border-slate-200 bg-white text-[#173d6e] hover:bg-[#f4f7fb]';
  @endphp

  <div class="mx-auto max-w-5xl space-y-4 text-[#173d6e]">
    <header class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-solid
                   border-[#acd6fb] bg-[#dfeffe] p-4 sm:px-5">
      <div class="min-w-0 [overflow-wrap:anywhere]">
        <h1 class="m-0 text-xl leading-tight font-extrabold tracking-tight sm:text-2xl">
          Kehadiran Pertemuan {{ $pertemuan->nomor }}
        </h1>

        @if (filled($pertemuan->judul))
          <p class="mt-1 mb-0 text-sm font-semibold">{{ $pertemuan->judul }}</p>
        @endif

        <p class="mt-1 mb-0 text-sm text-slate-700">
          {{ $tanggal ?? 'Tanggal belum ditentukan' }}
          @if ($sesi)
            · {{ $sesi->dibuka_pada->format('H:i') }}–{{ $sesi->ditutup_pada->format('H:i') }}
          @endif
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                     {{ $dibuka ? 'bg-emerald-50 text-emerald-800' : 'bg-white text-slate-700' }}">
          {{ $dibuka ? 'Sedang dibuka' : ($ditutup ? 'Sudah ditutup' : 'Belum dibuka') }}
        </span>

        <form method="POST" action="{{ route($dibuka ? 'dosen.absensi.tutup' : 'dosen.absensi.buka', $pertemuan) }}">
          @csrf
          <button type="submit" class="{{ $tombol }} {{ $dibuka ? $sekunder : $utama }}">
            {{ $dibuka ? 'Tutup Sekarang' : 'Buka Sekarang' }}
          </button>
        </form>
      </div>
    </header>

    @if ($mahasiswa->isNotEmpty())
      <dl aria-label="Ringkasan kehadiran" class="m-0 grid grid-cols-2 gap-2 sm:grid-cols-5">
        @foreach ($pilihan as $kode => [$label, $warna])
          <div class="{{ $warna }} min-w-0 rounded-lg p-3 last:col-span-2 sm:last:col-span-1">
            <dt class="text-xs font-semibold">{{ $label }}</dt>
            <dd class="m-0 mt-1 text-xl font-extrabold tabular-nums">{{ $jumlahStatus->get($kode, 0) }}</dd>
          </div>
        @endforeach
      </dl>
    @endif

    <section aria-labelledby="judul-daftar-hadir" class="{{ $panel }}">
      <div class="flex flex-wrap items-center justify-between gap-2 border-0 border-b border-solid border-slate-100 px-4 py-3 sm:px-5">
        <h2 id="judul-daftar-hadir" class="m-0 text-base font-bold">Daftar mahasiswa</h2>
        <span class="text-sm text-slate-600">{{ $mahasiswa->count() }} mahasiswa</span>
      </div>

      <form method="POST" action="{{ route('dosen.absensi.simpan', $pertemuan) }}">
        @csrf

        <div class="divide-y divide-solid divide-slate-100">
          @forelse ($mahasiswa as $mhs)
            @php
              $awal = $absensi[$mhs->id]->status ?? $statusDefault;
              $status = old('status.' . $mhs->id, $awal) ?? '';
            @endphp

            <div class="grid items-center gap-2 px-4 py-3 sm:grid-cols-[minmax(0,1fr)_11rem] sm:px-5">
              <div class="min-w-0 [overflow-wrap:anywhere]">
                <p class="m-0 text-sm font-semibold">{{ $mhs->name }}</p>

                @if (filled($mhs->nim_nidn))
                  <p class="mt-0.5 mb-0 text-xs text-slate-600">{{ $mhs->nim_nidn }}</p>
                @endif
              </div>

              <div>
                <label for="status-{{ $mhs->id }}" class="sr-only">Kehadiran {{ $mhs->name }}</label>

                <select id="status-{{ $mhs->id }}" name="status[{{ $mhs->id }}]"
                        class="box-border min-h-11 w-full rounded-lg border border-solid border-slate-300 bg-white
                               px-3 py-2 font-sans text-sm text-[#173d6e] focus:border-[#2b62a8]
                               focus:outline-none focus:ring-2 focus:ring-[#acd6fb]">
                  @if ($awal === '')
                    <option value="" @selected($status === '')>Belum absen</option>
                  @endif

                  @foreach ($pilihan as $kode => [$label])
                    @continue($kode === '')
                    <option value="{{ $kode }}" @selected($status === $kode)>{{ $label }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          @empty
            <p class="m-0 p-4 text-sm text-slate-600 sm:px-5">Belum ada mahasiswa di kelas ini.</p>
          @endforelse
        </div>

        @if ($mahasiswa->isNotEmpty())
          <div class="flex flex-wrap items-center justify-between gap-3 border-0 border-t border-solid border-slate-100 px-4 py-3 sm:px-5">
            <p class="m-0 text-sm text-slate-600">Perubahan status dicatat setelah disimpan.</p>
            <button type="submit" class="{{ $tombol }} {{ $utama }}">Simpan Kehadiran</button>
          </div>
        @endif
      </form>
    </section>
  </div>
@endsection