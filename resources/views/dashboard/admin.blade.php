@extends('layouts.panel')

@section('title', 'Dashboard Admin')

@section('content')
  @php
    $statistik = [
      ['Total Pengguna', $totalPengguna, 'fa-users'],
      ['Total Kelas', $totalKelas, 'fa-chalkboard-user'],
      ['Kelas Aktif', $kelasAktif, 'fa-circle-check'],
      ['Pengguna Aktif', $penggunaAktif, 'fa-user-check'],
    ];

    $menuKelola = [
      ['Kelola Pengguna', 'admin.users.index', 'fa-users'],
      ['Kelola Kelas', 'admin.kelas.index', 'fa-chalkboard-user'],
      ['Data Master (Program Studi, Mata Kuliah, Tahun Ajaran)', 'admin.master.index', 'fa-database'],
      ['Laporan Aktivitas', 'admin.laporan', 'fa-chart-line'],
    ];
  @endphp

  <div class="[&_.m-banner]:min-h-0 [&_.m-banner]:shadow-sm
              min-[1101px]:[&_.m-banner]:grid-cols-[minmax(0,1fr)_44%]
              [&_.m-banner_.isi]:p-5 sm:[&_.m-banner_.isi]:p-7
              [&_.m-banner_.pill]:border-[#acd6fb]/50 [&_.m-banner_.pill]:bg-white/10
              [&_.m-banner_p]:mt-4 [&_.m-banner_p]:text-sm [&_.m-banner_p]:text-[#acd6fb]
              [&_.m-banner_h1]:mt-1 [&_.m-banner_h1]:mb-2 [&_.m-banner_h1]:text-2xl
              [&_.m-banner_h1]:leading-tight sm:[&_.m-banner_h1]:text-3xl
              [&_.m-banner_.isi>div]:text-sm [&_.m-banner_.isi>div]:leading-6
              [&_.m-banner_.isi>div]:text-white/90">
    @include('partials.banner', [
      'label' => 'Dashboard Admin',
      'ikon' => 'fa-shield-halved',
      'sapaan' => 'Admin',
      'judul' => 'Selamat datang Kembali!',
      'sub' => 'Semoga hari ini terlihat semakin Menyenangkan.',
    ])
  </div>

<section class="mt-6" aria-labelledby="monitoring-admin">
  <h2 id="monitoring-admin"
      class="text-xl font-bold tracking-tight text-[#173d6e] sm:text-2xl">
    Monitoring
  </h2>

  @php
    $ringkasan = [
      ['Pengguna', 'fa-users', $totalPengguna, $penggunaAktif],
      ['Kelas', 'fa-chalkboard-user', $totalKelas, $kelasAktif],
    ];
  @endphp

  <div class="mt-3 grid gap-3 sm:grid-cols-2 sm:gap-4">
    @foreach ($ringkasan as [$label, $ikon, $total, $aktif])
      @php
        $persen = $total > 0
          ? min(100, (int) round($aktif / $total * 100))
          : null;
      @endphp

      <div class="min-w-0 rounded-2xl border border-[#acd6fb] bg-linear-to-br
                  from-white via-white to-[#dfeffe] p-4 shadow-sm sm:p-5">
        <h3 class="flex items-center gap-2.5 text-base font-semibold text-[#173d6e]">
          <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-[#acd6fb]">
            <i class="fa-solid {{ $ikon }}" aria-hidden="true"></i>
          </span>
          {{ $label }}
        </h3>

        <dl class="mt-4 grid grid-cols-2 divide-x divide-[#acd6fb]">
          <div class="min-w-0 pr-4">
            <dt class="text-sm font-medium text-[#4d6a92]">Total</dt>
            <dd class="mt-1 text-3xl leading-none font-bold tabular-nums
                       text-[#173d6e] [overflow-wrap:anywhere]">
              {{ number_format($total, 0, ',', '.') }}
            </dd>
          </div>

          <div class="min-w-0 pl-4">
            <dt class="text-sm font-medium text-[#4d6a92]">Aktif</dt>
            <dd class="mt-1 text-3xl leading-none font-bold tabular-nums
                       text-[#2b62a8] [overflow-wrap:anywhere]">
              {{ number_format($aktif, 0, ',', '.') }}
            </dd>
          </div>
        </dl>

        <div class="mt-4">
          <div class="mb-1.5 flex items-center justify-between gap-2 text-xs text-[#4d6a92]">
            <span>Persentase aktif</span>
            <span class="font-semibold tabular-nums text-[#173d6e]">
              {{ $persen === null ? '—' : $persen . '%' }}
            </span>
          </div>

          <div class="h-1.5 overflow-hidden rounded-full bg-[#dfeffe]" aria-hidden="true">
            <div class="h-full rounded-full bg-[#2b62a8]"
                 style="width: {{ $persen ?? 0 }}%"></div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</section>

  <section class="mt-6" aria-labelledby="kelola-admin">
    <h2 id="kelola-admin" class="text-xl font-bold tracking-tight text-[#173d6e] sm:text-2xl">
      Kelola
    </h2>

    <div class="mt-3 grid gap-3 sm:grid-cols-2">
      @foreach ($menuKelola as [$label, $namaRute, $ikon])
        <a href="{{ route($namaRute) }}"
           class="flex items-center gap-3 rounded-xl border border-[#acd6fb] bg-white px-4 py-3
                  text-sm font-semibold text-[#173d6e] hover:border-[#2b62a8] hover:bg-[#dfeffe]">
          <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-[#acd6fb]">
            <i class="fa-solid {{ $ikon }}" aria-hidden="true"></i>
          </span>

          <span class="min-w-0 flex-1">{{ $label }}</span>
          <i class="fa-solid fa-chevron-right text-xs text-[#2b62a8]" aria-hidden="true"></i>
        </a>
      @endforeach
    </div>
  </section>
@endsection