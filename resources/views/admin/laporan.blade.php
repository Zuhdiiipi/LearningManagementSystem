@extends('layouts.panel')

@section('title', 'Laporan Aktivitas')

@section('content')
  @php
    $ikonStatistik = [
      'Dosen' => 'fa-chalkboard-user',
      'Mahasiswa' => 'fa-user-graduate',
      'Kelas' => 'fa-door-open',
      'Materi' => 'fa-book-open',
      'Tugas & Praktikum' => 'fa-clipboard-list',
      'Pengumpulan Tugas' => 'fa-file-circle-check',
      'Kuis' => 'fa-circle-question',
      'Kuis Dikerjakan' => 'fa-square-check',
    ];

    $kartu = 'min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm';
    $kepala = 'flex flex-wrap items-center justify-between gap-2 px-4 py-3 sm:px-5';
    $judul = 'text-base font-bold text-[#173d6e]';
    $tabel = 'w-full min-w-[38rem] border-collapse text-left text-sm';
    $th = 'px-4 py-2.5 font-semibold';
    $td = 'px-4 py-3 align-top';
    $kosong = 'px-4 py-8 text-center text-slate-600';
    $tombolHalaman = 'rounded-lg border border-slate-200 bg-white px-3 py-1.5
                      text-sm font-semibold text-[#173d6e] hover:bg-[#dfeffe]';
  @endphp

  <h1 class="mb-5 text-2xl font-bold tracking-tight text-[#173d6e] sm:text-3xl">
    Laporan Aktivitas
  </h1>

  <dl class="mb-5 grid grid-cols-2 gap-3 xl:grid-cols-4">
    @foreach ($statistik as $label => $nilai)
      <div class="relative min-w-0 rounded-xl border border-[#acd6fb] bg-white p-3 shadow-sm
                  min-[400px]:pl-14 sm:p-4 sm:pl-16">
        <dt class="break-words text-sm font-medium leading-5 text-[#4d6a92]">
          <span class="absolute top-3 left-3 hidden size-8 place-items-center rounded-lg bg-[#dfeffe]
                       text-[#173d6e] min-[400px]:grid sm:top-4 sm:left-4 sm:size-9">
            <i class="fa-solid {{ $ikonStatistik[$label] ?? 'fa-chart-simple' }}" aria-hidden="true"></i>
          </span>
          {{ $label }}
        </dt>
        <dd class="mt-1 break-words text-2xl font-bold tabular-nums text-[#173d6e]">
          {{ number_format($nilai, 0, ',', '.') }}
        </dd>
      </div>
    @endforeach
  </dl>

  <div class="space-y-4">
    <section class="{{ $kartu }}" aria-labelledby="laporan-kelas">
      <div class="{{ $kepala }}">
        <h2 id="laporan-kelas" class="{{ $judul }}">Daftar Kelas</h2>
        <span class="text-sm text-slate-600">{{ number_format($kelas->count(), 0, ',', '.') }} kelas</span>
      </div>

      <div class="overflow-x-auto" tabindex="0" role="region" aria-labelledby="laporan-kelas">
        <table class="{{ $tabel }}">
          <thead class="bg-[#dfeffe] text-[#173d6e]">
            <tr>
              @foreach (['Mata Kuliah', 'Kelas', 'Dosen', 'Mahasiswa', 'Dibuat'] as $kolom)
                <th scope="col" class="{{ $th }}">{{ $kolom }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse ($kelas as $k)
              <tr class="hover:bg-slate-50">
                <th scope="row" class="{{ $td }} font-semibold text-[#173d6e] [overflow-wrap:anywhere]">
                  {{ $k->mataKuliah?->nama ?? '—' }}
                </th>
                <td class="{{ $td }} [overflow-wrap:anywhere]">{{ $k->nama }}</td>
                <td class="{{ $td }} [overflow-wrap:anywhere]">{{ $k->dosen?->name ?? '—' }}</td>
                <td class="{{ $td }} tabular-nums">{{ number_format($k->mahasiswa_count, 0, ',', '.') }}</td>
                <td class="{{ $td }} whitespace-nowrap text-slate-600">
                  {{ $k->created_at?->translatedFormat('d M Y') ?? '—' }}
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="{{ $kosong }}">Belum ada kelas.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    <section class="{{ $kartu }}" aria-labelledby="laporan-log">
      <div class="{{ $kepala }}">
        <h2 id="laporan-log" class="{{ $judul }}">Log Aktivitas Pengguna</h2>
        <span class="text-sm text-slate-600">{{ number_format($log->total(), 0, ',', '.') }} aktivitas</span>
      </div>

      <div class="overflow-x-auto" tabindex="0" role="region" aria-labelledby="laporan-log">
        <table class="{{ $tabel }}">
          <thead class="bg-[#dfeffe] text-[#173d6e]">
            <tr>
              @foreach (['Waktu', 'Pengguna', 'Peran', 'Aktivitas'] as $kolom)
                <th scope="col" class="{{ $th }}">{{ $kolom }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse ($log as $l)
              <tr class="hover:bg-slate-50">
                <td class="{{ $td }} whitespace-nowrap text-slate-600">
                  {{ $l->created_at?->translatedFormat('d M Y H:i') ?? '—' }}
                </td>
                <td class="{{ $td }} font-medium text-[#173d6e] [overflow-wrap:anywhere]">
                  {{ $l->user?->name ?? '—' }}
                </td>
                <td class="{{ $td }}">
                  @if ($l->user)
                    <span class="inline-flex rounded-full bg-[#dfeffe] px-2.5 py-1
                                 text-xs font-semibold text-[#173d6e]">
                      {{ ucfirst($l->user->role) }}
                    </span>
                  @else
                    —
                  @endif
                </td>
                <td class="{{ $td }} [overflow-wrap:anywhere]">{{ $l->aksi }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="{{ $kosong }}">Belum ada aktivitas pada halaman ini.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($log->hasPages())
        <nav aria-label="Halaman log aktivitas"
             class="flex flex-wrap items-center justify-center gap-3 border-t border-slate-100 px-4 py-3">
          @if ($log->previousPageUrl())
            <a href="{{ $log->previousPageUrl() }}" rel="prev" class="{{ $tombolHalaman }}">
              Sebelumnya
            </a>
          @endif

          <span class="text-sm text-slate-600">
            Halaman {{ $log->currentPage() }} dari {{ $log->lastPage() }}
          </span>

          @if ($log->nextPageUrl())
            <a href="{{ $log->nextPageUrl() }}" rel="next" class="{{ $tombolHalaman }}">
              Berikutnya
            </a>
          @endif
        </nav>
      @endif
    </section>

    <section class="{{ $kartu }}" aria-labelledby="laporan-email">
      <div class="{{ $kepala }}">
        <h2 id="laporan-email" class="{{ $judul }}">Log Notifikasi Email</h2>
        <span class="text-sm text-slate-600">{{ $email->count() }} catatan terbaru</span>
      </div>

      <div class="overflow-x-auto" tabindex="0" role="region" aria-labelledby="laporan-email">
        <table class="{{ $tabel }}">
          <thead class="bg-[#dfeffe] text-[#173d6e]">
            <tr>
              @foreach (['Waktu', 'Penerima', 'Jenis', 'Subjek', 'Status'] as $kolom)
                <th scope="col" class="{{ $th }}">{{ $kolom }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse ($email as $e)
              <tr class="hover:bg-slate-50">
                <td class="{{ $td }} whitespace-nowrap text-slate-600">
                  {{ $e->waktu_kirim?->translatedFormat('d M Y H:i') ?? '—' }}
                </td>
                <td class="{{ $td }} [overflow-wrap:anywhere]">{{ $e->penerima_email }}</td>
                <td class="{{ $td }}">{{ $e->jenis_notif }}</td>
                <td class="{{ $td }} [overflow-wrap:anywhere]">{{ $e->subjek }}</td>
                <td class="{{ $td }}">
                  <span @class([
                    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                    'bg-emerald-50 text-emerald-800' => $e->status_pengiriman,
                    'bg-red-50 text-red-800' => ! $e->status_pengiriman,
                  ])>
                    {{ $e->status_pengiriman ? 'Terkirim' : 'Gagal' }}
                  </span>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="{{ $kosong }}">Belum ada email yang dikirim.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  </div>
@endsection