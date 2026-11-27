<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'LMS') | LMS</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    @layer legacy, theme, base, components, utilities;
    @import url("{{ asset('css/mhs.css') }}") layer(legacy);
  </style>

  @vite('resources/css/app.css')
  @stack('head')
</head>

@php
  $penggunaPanel = auth()->user();
  $peranPanel = $penggunaPanel->role;
  $dashboardPanel = $peranPanel . '.dashboard';
  $ruteKelasPanel = $peranPanel . '.kelas.show';

  $menuPeranPanel = match ($peranPanel) {
    'admin' => [
      ['Pengguna', 'admin.users.index', 'admin.users.*', 'fa-users'],
      ['Kelas', 'admin.kelas.index', 'admin.kelas.*', 'fa-chalkboard-user'],
      ['Data Master', 'admin.master.index', 'admin.master.*', 'fa-database'],
      ['Laporan', 'admin.laporan', 'admin.laporan', 'fa-chart-line'],
    ],
    'dosen' => [
      ['Buat Kelas', 'dosen.kelas.create', 'dosen.kelas.create', 'fa-plus'],
      ['Profil', 'dosen.profil', 'dosen.profil', 'fa-user'],
    ],
    'mahasiswa' => [
      ['Nilai Saya', 'mahasiswa.nilai', 'mahasiswa.nilai', 'fa-star'],
      ['Profil', 'mahasiswa.profil', 'mahasiswa.profil', 'fa-user'],
    ],
    default => [],
  };

  $menuPanel = [
    ['Dashboard', $dashboardPanel, $dashboardPanel, 'fa-gauge-high'],
    ...$menuPeranPanel,
  ];

  $tampilkanTabKelasPanel = $peranPanel !== 'admin'
    && isset($kelas)
    && $kelas instanceof \App\Models\Kelas
    && $kelas->exists
    && empty($tanpaMenu);

  $tabKelasPanel = [
    'materi' => 'Materi',
    'tugas' => 'Tugas',
    'kuis' => 'Kuis',
    'absensi' => 'Absensi',
    'nilai' => 'Nilai',
  ];

  if ($peranPanel === 'dosen') {
    $tabKelasPanel += ['progres' => 'Progres', 'anggota' => 'Anggota'];
  }

  $tabAktifPanel = $menuAktif ?? ($tab ?? 'materi');
  $punyaNotifikasiPanel = $peranPanel === 'mahasiswa' && Route::has('mahasiswa.notifikasi.buka');
@endphp

<body class="min-h-screen bg-[#f4f7fb] font-['Inter',sans-serif] text-slate-900 antialiased lg:pl-56">
  <header class="relative z-30 border-b border-slate-200 bg-white">
    <div class="mx-auto flex min-h-14 max-w-7xl items-center justify-between gap-3 px-4 sm:px-6 lg:min-h-16 lg:px-8">
      <a href="{{ route($dashboardPanel) }}" class="flex items-center gap-2 text-lg font-bold text-[#173d6e] lg:hidden">
        <img src="{{ asset('images/logo.png') }}" class="size-8 object-contain" alt="">
        LMS
      </a>

      <p class="hidden min-w-0 text-base font-bold lg:block">@yield('title', 'LMS')</p>

      <div class="relative flex shrink-0 items-center gap-2">
        @if ($punyaNotifikasiPanel)
          @php
            $belumDibacaPanel = $penggunaPanel->unreadNotifications()->count();
            $notifikasiPanel = $penggunaPanel->notifications()->limit(5)->get();
          @endphp

          <details data-panel-dropdown>
            <summary class="relative grid size-9 cursor-pointer list-none place-items-center rounded-lg
                            border border-slate-200 text-[#173d6e] hover:bg-slate-50
                            [&::-webkit-details-marker]:hidden">
              <i class="fa-solid fa-bell" aria-hidden="true"></i>
              <span class="sr-only">Notifikasi</span>

              @if ($belumDibacaPanel)
                <span class="absolute -right-1 -top-1 min-w-4 rounded-full bg-red-600 px-1
                             text-center text-[10px] font-bold leading-4 text-white">
                  {{ $belumDibacaPanel > 9 ? '9+' : $belumDibacaPanel }}
                </span>
              @endif
            </summary>

            <div class="absolute right-0 top-full z-40 mt-2 w-80 max-w-[calc(100vw-2rem)]
                        overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
              <div class="flex items-center justify-between gap-2 border-b border-slate-100 px-4 py-3">
                <strong class="text-sm">Notifikasi</strong>

                @if ($belumDibacaPanel && Route::has('mahasiswa.notifikasi.baca-semua'))
                  <form method="POST" action="{{ route('mahasiswa.notifikasi.baca-semua') }}">
                    @csrf
                    <button type="submit" class="border-0 bg-transparent text-xs font-semibold text-blue-700">
                      Tandai semua dibaca
                    </button>
                  </form>
                @endif
              </div>

              <div class="max-h-[min(22rem,60vh)] overflow-y-auto">
                @forelse ($notifikasiPanel as $notifikasi)
                  <a href="{{ route('mahasiswa.notifikasi.buka', $notifikasi->id) }}"
                     @class([
                       'block border-b border-slate-100 px-4 py-3 text-sm hover:bg-slate-50',
                       'bg-blue-50' => ! $notifikasi->read_at,
                     ])>
                    <span class="block text-xs font-semibold text-blue-700">
                      {{ $notifikasi->data['jenis'] ?? 'Info' }}
                    </span>
                    <strong class="mt-0.5 block break-words font-semibold">
                      {{ $notifikasi->data['judul'] ?? '' }}
                    </strong>
                    <span class="mt-1 block text-xs text-slate-500">
                      {{ $notifikasi->created_at->diffForHumans() }}
                    </span>
                  </a>
                @empty
                  <p class="px-4 py-4 text-sm text-slate-600">Belum ada notifikasi.</p>
                @endforelse
              </div>

              @if (Route::has('mahasiswa.notifikasi'))
                <a href="{{ route('mahasiswa.notifikasi') }}"
                   class="block px-4 py-3 text-center text-sm font-semibold text-blue-700 hover:bg-slate-50">
                  Lihat semua notifikasi
                </a>
              @endif
            </div>
          </details>
        @endif

        <details class="relative" data-panel-dropdown>
          <summary class="flex min-h-9 cursor-pointer list-none items-center gap-2 rounded-lg
                          border border-slate-200 px-3 py-1.5 text-sm font-semibold
                          hover:bg-slate-50 [&::-webkit-details-marker]:hidden">
            <i class="fa-solid fa-circle-user text-[#2b62a8]" aria-hidden="true"></i>
            <span class="max-w-24 truncate sm:max-w-44">{{ $penggunaPanel->name }}</span>
            <i class="fa-solid fa-chevron-down text-xs text-slate-500" aria-hidden="true"></i>
          </summary>

          <form method="POST" action="{{ route('logout') }}"
                class="absolute right-0 top-full z-40 mt-2 w-40 rounded-xl border
                       border-slate-200 bg-white p-1 shadow-lg">
            @csrf
            <button type="submit"
                    class="flex w-full items-center gap-2 rounded-lg border-0 bg-transparent
                           px-3 py-2 text-left text-sm font-semibold hover:bg-slate-100">
              <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
              Keluar
            </button>
          </form>
        </details>
      </div>
    </div>
  </header>

  <aside class="border-b border-slate-200 bg-white lg:fixed lg:inset-y-0 lg:left-0
                lg:w-56 lg:overflow-y-auto lg:border-r lg:border-b-0">
    <a href="{{ route($dashboardPanel) }}"
       class="hidden h-16 items-center gap-3 border-b border-slate-100 px-5 lg:flex">
      <img src="{{ asset('images/logo.png') }}" class="size-9 object-contain" alt="">

      <span>
        <span class="block text-lg leading-5 font-extrabold tracking-tight text-[#173d6e]">LMS</span>
        <span class="text-xs text-slate-600">{{ ucfirst($peranPanel) }}</span>
      </span>
    </a>

    <nav aria-label="Navigasi utama" class="flex gap-1 overflow-x-auto px-3 py-2 lg:flex-col lg:py-5">
      @foreach ($menuPanel as [$label, $namaRute, $pola, $ikon])
        @continue(! Route::has($namaRute))
        @php $menuAktifPanel = request()->routeIs($pola); @endphp

        <a href="{{ route($namaRute) }}"
           @if ($menuAktifPanel) aria-current="page" @endif
           @class([
             'flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold whitespace-nowrap lg:gap-3 lg:py-2.5',
             'bg-[#e8f0fb] text-[#173d6e]' => $menuAktifPanel,
             'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! $menuAktifPanel,
           ])>
          <i class="fa-solid {{ $ikon }} w-5 text-center" aria-hidden="true"></i>
          {{ $label }}
        </a>
      @endforeach
    </nav>
  </aside>

  <main class="mx-auto min-w-0 max-w-7xl px-4 py-5 sm:px-6 sm:py-7 lg:px-8">
    @if (session('sukses'))
      <div role="status" class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
        {{ session('sukses') }}
      </div>
    @endif

    @if (session('gagal'))
      <div role="alert" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
        {{ session('gagal') }}
      </div>
    @endif

    @if ($errors->any())
      <div role="alert" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
        {{ $errors->first() }}
      </div>
    @endif

    @if ($tampilkanTabKelasPanel)
      <div class="mb-5 rounded-xl border border-slate-200 bg-white">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-3 py-2">
          <a href="@yield('kembali', route($dashboardPanel))"
             class="inline-flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm
                    font-semibold text-[#173d6e] hover:bg-slate-100">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
            Kembali
          </a>

          @if ($peranPanel === 'dosen')
            <button type="button" data-salin-kode="{{ $kelas->kode_kelas }}" title="Salin kode kelas"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#e8f0fb] px-3 py-1.5
                           text-sm font-semibold text-[#173d6e]">
              <i class="fa-solid fa-copy" aria-hidden="true"></i>
              <span class="sr-only">Kode kelas</span>
              <span data-hasil-salin aria-live="polite">{{ $kelas->kode_kelas }}</span>
            </button>
          @endif
        </div>

        <nav aria-label="Navigasi kelas" class="flex gap-1 overflow-x-auto px-3 py-2">
          @foreach ($tabKelasPanel as $idTab => $labelTab)
            <a href="{{ route($ruteKelasPanel, [$kelas, 'tab' => $idTab]) }}"
               @if ($tabAktifPanel === $idTab) aria-current="page" @endif
               @class([
                 'shrink-0 rounded-lg px-3 py-2 text-sm font-semibold whitespace-nowrap',
                 'bg-[#e8f0fb] text-[#173d6e]' => $tabAktifPanel === $idTab,
                 'text-slate-600 hover:bg-slate-100' => $tabAktifPanel !== $idTab,
               ])>
              {{ $labelTab }}
            </a>
          @endforeach
        </nav>
      </div>
    @endif

    @yield('content')
  </main>

  <script>
    document.addEventListener('click', event => {
      document.querySelectorAll('[data-panel-dropdown][open], details.m-more[open]').forEach(menu => {
        if (!menu.contains(event.target)) {
          menu.open = false;
        }
      });
    });

    document.querySelectorAll('[data-buka]').forEach(button => {
      button.addEventListener('click', () => {
        const dialog = document.getElementById(button.dataset.buka);
        if (dialog instanceof HTMLDialogElement && !dialog.open) {
          dialog.showModal();
        }
      });
    });

    document.querySelectorAll('dialog').forEach(dialog => {
      dialog.addEventListener('click', event => {
        if (event.target === dialog) {
          dialog.close();
        }
      });
    });

    const tombolSalinKodePanel = document.querySelector('[data-salin-kode]');

    tombolSalinKodePanel?.addEventListener('click', async () => {
      const kode = tombolSalinKodePanel.dataset.salinKode;
      const hasil = tombolSalinKodePanel.querySelector('[data-hasil-salin]');

      try {
        await navigator.clipboard.writeText(kode);
        hasil.textContent = 'Tersalin';
      } catch {
        hasil.textContent = 'Gagal menyalin';
      }

      setTimeout(() => {
        hasil.textContent = kode;
      }, 1500);
    });
  </script>

  @include('partials.konfirmasi')
  @stack('scripts')
</body>
</html>