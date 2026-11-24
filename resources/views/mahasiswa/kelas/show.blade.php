@extends('layouts.panel')

@section('title', $kelas->mataKuliah->nama)

@php use App\Support\Waktu; @endphp

@section('content')
  <h1 class="m-title">{{ $tab === 'nilai' ? 'Nilai ' : '' }}{{ $kelas->mataKuliah->nama }}</h1>

  <div class="m-box">

    {{-- ================= MATERI ================= --}}
    @if ($tab === 'materi')
      <h2>Materi</h2>
      <div class="m-list">
        @forelse ($materi as $m)
          <a href="{{ route('mahasiswa.materi.show', $m) }}" class="m-item">
            <span class="m-icon"><i class="fa-solid fa-book-open"></i></span>
            <div class="teks">
              <h4>{{ $m->judul }}</h4>
              <p>{{ Waktu::singkat($m->created_at) }}</p>
            </div>
          </a>
        @empty
          <div class="m-item"><p class="m-muted">Materi belum tersedia. Dosen belum menambahkan materi di kelas ini.</p></div>
        @endforelse
      </div>
    @endif

    {{-- ================= TUGAS & PRAKTIKUM ================= --}}
    @if ($tab === 'tugas')
      @foreach (['tugas' => 'Tugas', 'praktikum' => 'Praktikum'] as $jenis => $label)
        <h2>{{ $label }}</h2>
        <div class="m-list">
          @forelse ($tugas->where('jenis', $jenis)->sortByDesc('created_at') as $t)
            @php
              $k = $pengumpulan[$t->id] ?? null;
              if ($k) { $k->setRelation('tugas', $t); }
              [$kelasBadge, $teksBadge] = $k ? ($k->terlambat() ? ['telat', 'Terlambat'] : ['selesai', 'Selesai']) : ($t->sudahLewat() ? ['tidak', 'Belum Dikumpulkan'] : ['proses', 'Proses']);
            @endphp
            <a href="{{ route('mahasiswa.tugas.show', $t) }}" class="m-item">
              <span class="m-icon"><i class="fa-solid {{ $jenis === 'tugas' ? 'fa-file-code' : 'fa-clipboard-list' }}"></i></span>
              <div class="teks">
                <h4>{{ $t->judul }}</h4>
                <p>{{ Waktu::singkat($t->created_at) }} &nbsp;|&nbsp; Batas: {{ Waktu::singkat($t->deadline) }}</p>
              </div>
              <span class="kanan"><span class="badge {{ $kelasBadge }}">{{ $teksBadge }}</span></span>
            </a>
          @empty
            <div class="m-item"><p class="m-muted">Belum ada {{ strtolower($label) }}.</p></div>
          @endforelse
        </div>
      @endforeach
    @endif

    {{-- ================= QUIZ ================= --}}
    @if ($tab === 'kuis')
      <h2>Quiz</h2>
      <div class="m-list">
        @forelse ($kuis->sortByDesc('mulai') as $q)
          @php $h = $hasilKuis[$q->id] ?? null; @endphp
          <a href="{{ $h ? route('mahasiswa.kuis.hasil', $q) : route('mahasiswa.kuis.kerjakan', $q) }}" class="m-item">
            <span class="m-icon"><i class="fa-regular fa-circle-question"></i></span>
            <div class="teks">
              <h4>{{ $q->judul }}</h4>
              <p>Waktu Mulai {{ $q->mulai->translatedFormat('j M Y') }}, {{ $q->mulai->format('H : i') }}</p>
              <p>{{ $q->durasi_menit }} menit · {{ $q->soal_count }} soal
                @if (! $h)
                  · @if ($q->sedangDibuka()) <b style="color:#16a34a">Sedang dibuka sampai {{ $q->selesai->format('H:i') }}, klik untuk mengerjakan</b> @else {{ $q->status() }} @endif
                @endif
              </p>
            </div>
            <span class="kanan"><span class="badge">{{ $h ? rtrim(rtrim(number_format($h->skor, 2, ',', ''), '0'), ',') : 0 }} / 100</span></span>
          </a>
        @empty
          <div class="m-item"><p class="m-muted">Belum ada quiz.</p></div>
        @endforelse
      </div>
    @endif

    {{-- ================= ABSENSI ================= --}}
    @if ($tab === 'absensi')
      <h2>Absensi</h2>
      <div class="m-list">
        @forelse ($kelas->pertemuan->sortByDesc('nomor') as $p)
          @php
            $sesi = $p->sesiAbsensi;
            $catatan = $sesi ? ($absensiSaya[$sesi->id] ?? null) : null;
            $hari = $p->tanggal ? $p->tanggal->translatedFormat('l') : '-';
            $waktu = $sesi ? $sesi->dibuka_pada->format('H:i') . ' - ' . $sesi->ditutup_pada->format('H:i') : '-';
          @endphp
          <div class="m-item">
            <span class="m-icon"><i class="fa-regular fa-calendar-check"></i></span>
            <div class="teks">
              <h4>Pertemuan {{ $p->nomor }}</h4>
              @if ($p->judul) <p>{{ $p->judul }}</p> @endif
              <p>Hari : {{ $hari }}@if ($p->tanggal), {{ $p->tanggal->translatedFormat('j F Y') }}@endif</p>
              <p>Waktu : {{ $waktu }}</p>
            </div>
            <span class="kanan">
              @if ($catatan)
                <span class="status-{{ $catatan->status === 'hadir' ? 'hadir' : ($catatan->status === 'alpa' ? 'alpa' : 'izin') }}">{{ ucfirst($catatan->status) }}</span>
              @elseif ($sesi && $sesi->sedangDibuka())
                <button type="button" class="badge" data-buka="absen-{{ $p->id }}">Tandai</button>
              @elseif ($sesi && $sesi->sudahDitutup())
                <span class="status-alpa">Alpa</span>
              @else
                <span class="status-belum">Belum dibuka</span>
              @endif
            </span>
          </div>

          @if ($sesi && $sesi->sedangDibuka() && ! $catatan)
            <dialog id="absen-{{ $p->id }}" class="m-modal absen">
              <h2>Absensi Kelas Perkuliahan</h2>
              <h4>Informasi Kelas</h4>
              <p>Pertemuan ke-{{ $p->nomor }}<br>
                 Nama Kelas : {{ $kelas->mataKuliah->nama }} ({{ $kelas->nama }})<br>
                 Hari : {{ $hari }}<br>
                 Waktu : {{ $waktu }}</p>
              <h4>Absensi</h4>
              <p>Absensi telah dibuka</p>
              <form method="POST" action="{{ route('mahasiswa.absensi.hadir', $p) }}" class="aksi">
                @csrf
                <button class="m-btn navy">Tandai Kehadiran</button>
              </form>
            </dialog>
          @endif
        @empty
          <div class="m-item"><p class="m-muted">Belum ada pertemuan.</p></div>
        @endforelse
      </div>
    @endif

    {{-- ================= NILAI ================= --}}
    @if ($tab === 'nilai')
      @foreach (['tugas' => 'Tugas', 'praktikum' => 'Praktikum'] as $jenis => $label)
        <h2>{{ $label }}</h2>
        <div class="m-list">
          @forelse ($tugas->where('jenis', $jenis) as $t)
            @php $k = $pengumpulan[$t->id] ?? null; @endphp
            <a href="{{ route('mahasiswa.tugas.show', $t) }}" class="m-item">
              <span class="m-icon"><i class="fa-solid {{ $jenis === 'tugas' ? 'fa-file-code' : 'fa-clipboard-list' }}"></i></span>
              <div class="teks">
                <h4>{{ $t->judul }}</h4>
                <p>{{ Waktu::singkat($t->created_at) }} &nbsp;|&nbsp; {{ Waktu::singkat($t->deadline) }}</p>
              </div>
              <span class="kanan">
                <span class="badge">{{ $k?->nilai !== null ? $k->nilai : ($k ? 'Belum dinilai' : 0) }}{{ $k?->nilai !== null || ! $k ? ' / 100' : '' }}</span>
              </span>
            </a>
          @empty
            <div class="m-item"><p class="m-muted">Belum ada {{ strtolower($label) }}.</p></div>
          @endforelse
        </div>
      @endforeach

      <h2>Quiz</h2>
      <div class="m-list">
        @forelse ($kuis as $q)
          @php $h = $hasilKuis[$q->id] ?? null; @endphp
          <div class="m-item">
            <span class="m-icon"><i class="fa-regular fa-circle-question"></i></span>
            <div class="teks">
              <h4>{{ $q->judul }}</h4>
              <p>Waktu Mulai {{ $q->mulai->format('H : i') }}</p>
              <p>{{ $q->durasi_menit }} menit</p>
            </div>
            <span class="kanan"><span class="badge">{{ $h ? rtrim(rtrim(number_format($h->skor, 2, ',', ''), '0'), ',') : 0 }} / 100</span></span>
          </div>
        @empty
          <div class="m-item"><p class="m-muted">Belum ada quiz.</p></div>
        @endforelse
      </div>

      @php $b = \App\Support\RekapNilai::BOBOT; @endphp
      <p class="m-note">
        Kehadiran: {{ $rekap['hadir'] ?? 0 }} dari {{ $rekap['total_sesi'] ?? 0 }} pertemuan.
        Total nilai = Tugas {{ $b['tugas'] }}% + Praktikum {{ $b['praktikum'] }}% + Quiz {{ $b['kuis'] }}% + Kehadiran {{ $b['kehadiran'] }}%.
      </p>

      <div class="m-total">
        <span>Total Nilai @if ($rekap) ({{ $rekap['huruf'] }}) @endif</span>
        <span class="badge">{{ str_replace('.', ',', rtrim(rtrim(number_format($rekap['akhir'] ?? 0, 2, '.', ''), '0'), '.')) }}</span>
      </div>
    @endif
  </div>
@endsection
