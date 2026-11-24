<a href="{{ route('mahasiswa.dashboard') }}" class="{{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-house"></i> Dashboard</a>
<a href="{{ route('mahasiswa.kelas.index') }}" class="{{ request()->routeIs('mahasiswa.kelas.*', 'mahasiswa.tugas.*', 'mahasiswa.kuis.*') ? 'active' : '' }}"><i class="fa-solid fa-chalkboard"></i> Kelas Saya</a>
<a href="{{ route('mahasiswa.nilai') }}" class="{{ request()->routeIs('mahasiswa.nilai') ? 'active' : '' }}"><i class="fa-solid fa-star"></i> Nilai Saya</a>
