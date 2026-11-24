<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title') - LMS</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/mhs.css') }}">
  @stack('head')
</head>
@php
  // Layout bersama untuk Mahasiswa, Dosen dan Admin. Menu menyesuaikan peran yang login.
  $peran = auth()->user()->role;            // 'mahasiswa', 'dosen' atau 'admin'
  $rute = $peran . '.kelas.show';
@endphp
<body class="m-body">
  {{-- Header atas: logo dan nama pengguna (klik untuk membuka menu) --}}
  <header class="m-header">
    <a href="{{ route($peran . '.dashboard') }}" class="m-brand"><img src="{{ asset('images/logo.png') }}" alt="Logo"> LMS</a>

    @if ($peran === 'admin')
      {{-- Menu atas khusus Admin --}}
      <nav class="a-nav">
        {{-- <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Home</a>
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Pengguna</a>
        <a href="{{ route('admin.kelas.index') }}" class="{{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">Kelas</a> --}}
      </nav>
    @endif

    <div class="m-kanan">
    @if ($peran === 'mahasiswa')
      @php
        $belumDibaca = auth()->user()->unreadNotifications()->count();
        $notifTerbaru = auth()->user()->notifications()->limit(5)->get();
      @endphp
      {{-- Lonceng notifikasi: materi baru, tugas baru, quiz, nilai, pengingat deadline --}}
      <details class="m-notif">
        <summary title="Notifikasi"><i class="fa-solid fa-bell"></i><span class="sr">Notifikasi</span>
          @if ($belumDibaca) <span class="jumlah">{{ $belumDibaca > 9 ? '9+' : $belumDibaca }}</span> @endif
        </summary>
        <div class="m-notif-menu">
          <div class="kepala"><b>Notifikasi</b>
            @if ($belumDibaca)
              {{-- <form method="POST" action="{{ route('mahasiswa.notifikasi.baca-semua') }}">@csrf <button>Tandai semua dibaca</button></form> --}}
            @endif
          </div>
          @forelse ($notifTerbaru as $n)
            {{-- <a href="{{ route('mahasiswa.notifikasi.buka', $n->id) }}" class="{{ $n->read_at ? '' : 'baru' }}">
              <span class="jenis">{{ $n->data['jenis'] ?? 'Info' }}</span>
              <b>{{ $n->data['judul'] ?? '' }}</b>
              <small>{{ $n->created_at->diffForHumans() }}</small>
            </a> --}}
          @empty
            <p class="kosong">Belum ada notifikasi.</p>
          @endforelse
          {{-- <a href="{{ route('mahasiswa.notifikasi') }}" class="semua">Lihat semua notifikasi</a> --}}
        </div>
      </details>
    @endif

    <details class="m-user">
      <summary>{{ $peran === 'admin' ? 'ADMIN' : auth()->user()->name }} <i class="fa-solid fa-chevron-down"></i></summary>
      <div class="m-user-menu">
        <a href="{{ route($peran . '.dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
        {{-- @if ($peran === 'mahasiswa')
          <a href="{{ route('mahasiswa.profil') }}"><i class="fa-solid fa-user"></i> Profil</a>
          <a href="{{ route('mahasiswa.nilai') }}"><i class="fa-solid fa-star"></i> Nilai Saya</a>
        @elseif ($peran === 'dosen')
          <a href="{{ route('dosen.profil') }}"><i class="fa-solid fa-user"></i> Profil</a>
          <a href="{{ route('dosen.kelas.create') }}"><i class="fa-solid fa-plus"></i> Buat Kelas</a>
        @else
          <a href="{{ route('admin.users.create') }}"><i class="fa-solid fa-user-plus"></i> Tambah Pengguna</a>
          <a href="{{ route('admin.master.index') }}"><i class="fa-solid fa-database"></i> Data Master</a>
          <a href="{{ route('admin.laporan') }}"><i class="fa-solid fa-chart-line"></i> Laporan Aktivitas</a>
        @endif --}}
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
        </form>
      </div>
    </details>
    </div>
  </header>

  @php $pakaiMenu = $peran !== 'admin' && isset($kelas) && $kelas instanceof \App\Models\Kelas && $kelas->exists && empty($tanpaMenu); @endphp
  <div class="m-wrap {{ $pakaiMenu ? 'with-side' : '' }}">
    {{-- Menu samping hanya muncul di dalam kelas --}}
    @if ($pakaiMenu)
      @php $aktif = $menuAktif ?? ($tab ?? 'materi'); @endphp
      <aside class="m-side">
        <h3>Menu</h3>
        <nav>
          <a href="{{ route($rute, [$kelas, 'tab' => 'materi']) }}" class="{{ $aktif === 'materi' ? 'active' : '' }}">Materi</a>
          <a href="{{ route($rute, [$kelas, 'tab' => 'tugas']) }}" class="{{ $aktif === 'tugas' ? 'active' : '' }}">Tugas</a>
          <a href="{{ route($rute, [$kelas, 'tab' => 'kuis']) }}" class="{{ $aktif === 'kuis' ? 'active' : '' }}">Quiz</a>
          <hr>
          <a href="{{ route($rute, [$kelas, 'tab' => 'absensi']) }}" class="{{ $aktif === 'absensi' ? 'active' : '' }}">Absensi</a>
          <a href="{{ route($rute, [$kelas, 'tab' => 'nilai']) }}" class="{{ $aktif === 'nilai' ? 'active' : '' }}">Nilai</a>
          @if ($peran === 'dosen')
            <a href="{{ route($rute, [$kelas, 'tab' => 'progres']) }}" class="{{ $aktif === 'progres' ? 'active' : '' }}">Progres</a>
            <a href="{{ route($rute, [$kelas, 'tab' => 'anggota']) }}" class="{{ $aktif === 'anggota' ? 'active' : '' }}">Anggota</a>
          @endif
        </nav>
        @if ($peran === 'dosen')
          {{-- Kode kelas untuk dibagikan ke mahasiswa (klik untuk menyalin) --}}
          <button type="button" class="m-kode" title="Klik untuk menyalin kode kelas"
                  onclick="navigator.clipboard && navigator.clipboard.writeText('{{ $kelas->kode_kelas }}'); this.dataset.salin = 'Tersalin!'; setTimeout(() => delete this.dataset.salin, 1500)">{{ $kelas->kode_kelas }}</button>
        @endif
        <a href="@yield('kembali', route($peran . '.dashboard'))" class="m-back"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
      </aside>
    @endif

    <main class="m-main">
      @if (session('sukses')) <div class="m-flash ok"><i class="fa-solid fa-circle-check"></i> {{ session('sukses') }}</div> @endif
      @if (session('gagal')) <div class="m-flash err"><i class="fa-solid fa-circle-exclamation"></i> {{ session('gagal') }}</div> @endif
      @if ($errors->any()) <div class="m-flash err"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div> @endif

      @yield('content')
    </main>
  </div>

  <script>
    // Tutup menu dropdown saat klik di luar
    document.addEventListener('click', e => {
      document.querySelectorAll('details.m-user[open], details.m-more[open], details.m-notif[open]').forEach(d => { if (!d.contains(e.target)) d.removeAttribute('open'); });
    });
    // Tombol dengan data-buka="id-dialog" membuka pop-up
    document.querySelectorAll('[data-buka]').forEach(b => b.addEventListener('click', () => document.getElementById(b.dataset.buka).showModal()));
    document.querySelectorAll('dialog').forEach(d => d.addEventListener('click', e => { if (e.target === d) d.close(); }));
  </script>
  @stack('scripts')
</body>
</html>
