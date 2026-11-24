@extends('layouts.panel')

@section('title', $kelas->mataKuliah->nama)

@php use App\Support\Waktu; @endphp

@section('content')
  @php
    $tombol = [
        'materi' => ['Tambah Materi', route('dosen.materi.create', $kelas)],
        'tugas' => ['Tambah Tugas', route('dosen.tugas.create', $kelas)],
        'kuis' => ['Tambah Quiz', route('dosen.kuis.create', $kelas)],
        'absensi' => ['Tambah Absensi', route('dosen.absensi.create', $kelas)],
        'nilai' => ['Unduh Excel', route('dosen.kelas.export', $kelas)],
    ][$tab] ?? null;
  @endphp

  <div class="m-title-row">
    <h1 class="m-title">{{ $kelas->mataKuliah->nama }}</h1>
    @if ($tombol)
      <a href="{{ $tombol[1] }}" class="m-btn navy"><i class="fa-solid {{ $tab === 'nilai' ? 'fa-download' : 'fa-plus' }}"></i> {{ $tombol[0] }}</a>
    @endif
  </div>

  <div class="m-box">

    {{-- ================= MATERI ================= --}}
    @if ($tab === 'materi')
      <h2>Materi</h2>
      <div class="m-list">
        @forelse ($materi as $m)
          <div class="m-item">
            <span class="m-icon"><i class="fa-solid fa-book-open"></i></span>
            <a href="{{ route('dosen.materi.show', $m) }}" class="teks">
              <h4>{{ $m->judul }}</h4>
              <p>{{ Waktu::singkat($m->created_at) }}@if ($m->tipe === 'file') &nbsp;·&nbsp; {{ $m->boleh_unduh ? 'Boleh diunduh' : 'Hanya dilihat' }}@endif</p>
            </a>
            <div class="aksi-ikon">
              @if ($m->tipe === 'file')
                <a href="{{ route('dosen.materi.show', $m) }}#akses" class="ikon kunci" title="Pengaturan akses (izin unduh)"><i class="fa-solid {{ $m->boleh_unduh ? 'fa-lock-open' : 'fa-lock' }}"></i><span class="sr">Akses</span></a>
              @endif
              <a href="{{ route('dosen.materi.edit', $m) }}" class="ikon ubah" title="Ubah"><i class="fa-regular fa-pen-to-square"></i><span class="sr">Ubah</span></a>
              @include('dosen._hapus', ['aksi' => route('dosen.materi.destroy', $m), 'tanya' => 'Hapus materi ' . $m->judul . '?'])
            </div>
          </div>
        @empty
          <div class="m-item"><p class="m-muted">Belum ada materi. Klik <b>Tambah Materi</b>.</p></div>
        @endforelse
      </div>
    @endif

    {{-- ================= TUGAS & PRAKTIKUM ================= --}}
    @if ($tab === 'tugas')
      @foreach (['tugas' => 'Tugas', 'praktikum' => 'Praktikum'] as $jenis => $label)
        <h2>{{ $label }}</h2>
        <div class="m-list">
          @forelse ($tugas->where('jenis', $jenis)->sortByDesc('created_at') as $t)
            <div class="m-item">
              <span class="m-icon"><i class="fa-solid {{ $jenis === 'tugas' ? 'fa-file-code' : 'fa-clipboard-list' }}"></i></span>
              <a href="{{ route('dosen.tugas.show', $t) }}" class="teks">
                <h4>{{ $t->judul }}</h4>
                <p>{{ Waktu::singkat($t->created_at) }} &nbsp;|&nbsp; Batas: {{ Waktu::singkat($t->deadline) }}
                  · {{ $t->pengumpulan_count }}/{{ $anggota->count() }} mengumpulkan</p>
              </a>
              <div class="aksi-ikon">
                <a href="{{ route('dosen.tugas.terkumpul', $t) }}" class="ikon lihat" title="Lihat yang mengumpulkan"><i class="fa-regular fa-eye"></i><span class="sr">Lihat</span></a>
                <a href="{{ route('dosen.tugas.edit', $t) }}" class="ikon ubah" title="Ubah"><i class="fa-regular fa-pen-to-square"></i><span class="sr">Ubah</span></a>
                @include('dosen._hapus', ['aksi' => route('dosen.tugas.destroy', $t), 'tanya' => 'Hapus ' . $t->judul . '? Semua jawaban mahasiswa ikut terhapus.'])
              </div>
            </div>
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
          <div class="m-item">
            <span class="m-icon"><i class="fa-regular fa-circle-question"></i></span>
            <div class="teks">
              <h4>{{ $q->judul }}</h4>
              <p>Waktu Mulai {{ $q->mulai->translatedFormat('j M Y') }}, {{ $q->mulai->format('H : i') }}</p>
              <p>{{ $q->durasi_menit }} menit · {{ $q->soal_count }} soal · {{ $q->hasil_count }} sudah mengerjakan
                · @if ($q->sedangDibuka()) <b style="color:#16a34a">Sedang dibuka sampai {{ $q->selesai->format('H:i') }}</b> @else <span class="m-muted">{{ $q->status() }}</span> @endif</p>
            </div>
            <div class="aksi-ikon">
              @if ($q->sedangDibuka())
                <form method="POST" action="{{ route('dosen.kuis.tutup', $q) }}" onsubmit="return confirm('Tutup sesi quiz {{ addslashes($q->judul) }}? Mahasiswa tidak bisa mengerjakan lagi.')">@csrf <button class="m-btn outline small sesi-tutup">Tutup Sesi</button></form>
              @else
                <form method="POST" action="{{ route('dosen.kuis.buka', $q) }}">@csrf <button class="m-btn navy small sesi-buka">Buka Sesi</button></form>
              @endif
              <a href="{{ route('dosen.kuis.show', $q) }}" class="ikon lihat" title="Live Score"><i class="fa-regular fa-eye"></i><span class="sr">Lihat</span></a>
              <a href="{{ route('dosen.kuis.edit', $q) }}" class="ikon ubah" title="Ubah"><i class="fa-regular fa-pen-to-square"></i><span class="sr">Ubah</span></a>
              @include('dosen._hapus', ['aksi' => route('dosen.kuis.destroy', $q), 'tanya' => 'Hapus ' . $q->judul . '? Nilai mahasiswa ikut terhapus.'])
            </div>
          </div>
        @empty
          <div class="m-item"><p class="m-muted">Belum ada quiz.</p></div>
        @endforelse
      </div>
    @endif

    {{-- ================= ABSENSI ================= --}}
    @if ($tab === 'absensi')
      <h2>Absensi</h2>
      <div class="m-list">
        @php $urutan = $kelas->pertemuan->sortBy('nomor')->values(); @endphp
        @forelse ($urutan as $i => $p)
          @php $sesi = $p->sesiAbsensi; @endphp
          <div class="m-item">
            <span class="m-icon"><i class="fa-regular fa-calendar-check"></i></span>
            <a href="{{ route('dosen.absensi.show', $p) }}" class="teks">
              <h4>Pertemuan {{ $p->nomor }}@if ($p->judul) : {{ $p->judul }}@endif</h4>
              <p>Hari : {{ $p->tanggal ? $p->tanggal->translatedFormat('l, j F Y') : '-' }}</p>
              <p>Waktu : {{ $sesi ? $sesi->dibuka_pada->format('H:i') . ' - ' . $sesi->ditutup_pada->format('H:i') : '-' }}
                @if ($sesi?->sedangDibuka()) · <b style="color:#16a34a">Sedang dibuka</b> @endif</p>
            </a>
            <div class="aksi-ikon">
              {{-- Urutkan pertemuan --}}
              @foreach (['naik' => ['fa-arrow-up', 'Naikkan urutan', $i > 0], 'turun' => ['fa-arrow-down', 'Turunkan urutan', $i < $urutan->count() - 1]] as $arah => [$ikonArah, $judulArah, $boleh])
                <form method="POST" action="{{ route('dosen.absensi.urut', [$p, $arah]) }}">
                  @csrf @method('PATCH')
                  <button class="ikon urut" title="{{ $judulArah }}" @disabled(! $boleh)><i class="fa-solid {{ $ikonArah }}"></i><span class="sr">{{ $judulArah }}</span></button>
                </form>
              @endforeach
              <a href="{{ route('dosen.absensi.show', $p) }}" class="ikon lihat" title="Lihat kehadiran"><i class="fa-regular fa-eye"></i><span class="sr">Lihat</span></a>
              <a href="{{ route('dosen.absensi.edit', $p) }}" class="ikon ubah" title="Ubah pertemuan"><i class="fa-regular fa-pen-to-square"></i><span class="sr">Ubah</span></a>
              @include('dosen._hapus', ['aksi' => route('dosen.absensi.destroy', $p), 'tanya' => 'Hapus absensi pertemuan ' . $p->nomor . '?'])
            </div>
          </div>
        @empty
          <div class="m-item"><p class="m-muted">Belum ada absensi. Klik <b>Tambah Absensi</b>.</p></div>
        @endforelse
      </div>
    @endif

    {{-- ================= REKAP NILAI ================= --}}
    @if ($tab === 'nilai')
      @php
        $daftarTugas = $tugas->where('jenis', 'tugas')->values();
        $daftarPraktikum = $tugas->where('jenis', 'praktikum')->values();
        $angka = fn ($n) => $n === null ? '' : str_replace('.', ',', rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.'));
      @endphp
      <h2>Rekap Nilai</h2>
      <div class="rekap-wrap">
        <table class="rekap">
          <tr>
            <th rowspan="2">Nama</th>
            <th colspan="{{ max($daftarTugas->count(), 1) }}">Tugas</th>
            <th colspan="{{ max($daftarPraktikum->count(), 1) }}">Praktikum</th>
            <th colspan="{{ max($kuis->count(), 1) }}">Quiz</th>
            <th rowspan="2">Hadir</th>
            <th rowspan="2">Akhir</th>
            <th rowspan="2">Huruf</th>
          </tr>
          <tr>
            @forelse ($daftarTugas as $i => $t) <th class="kecil" title="{{ $t->judul }}">{{ $i + 1 }}</th> @empty <th class="kecil">-</th> @endforelse
            @forelse ($daftarPraktikum as $i => $t) <th class="kecil" title="{{ $t->judul }}">{{ $i + 1 }}</th> @empty <th class="kecil">-</th> @endforelse
            @forelse ($kuis as $i => $q) <th class="kecil" title="{{ $q->judul }}">{{ $i + 1 }}</th> @empty <th class="kecil">-</th> @endforelse
          </tr>
          @forelse ($anggota as $mhs)
            @php $r = $rekap[$mhs->id] ?? null; $nt = $nilaiTugas[$mhs->id] ?? collect(); $nk = $nilaiKuis[$mhs->id] ?? collect(); @endphp
            <tr>
              <td class="nama">{{ $mhs->name }}</td>
              @forelse ($daftarTugas as $t) <td>{{ $nt[$t->id]->nilai ?? '' }}</td> @empty <td></td> @endforelse
              @forelse ($daftarPraktikum as $t) <td>{{ $nt[$t->id]->nilai ?? '' }}</td> @empty <td></td> @endforelse
              @forelse ($kuis as $q) <td>{{ isset($nk[$q->id]) ? $angka($nk[$q->id]->skor) : '' }}</td> @empty <td></td> @endforelse
              <td>{{ $r ? $angka($r['kehadiran']) . '%' : '' }}</td>
              <td class="akhir">{{ $r ? $angka($r['akhir']) : '' }}</td>
              <td class="akhir">{{ $r['huruf'] ?? '' }}</td>
            </tr>
          @empty
            <tr><td colspan="20" class="nama">Belum ada mahasiswa di kelas ini.</td></tr>
          @endforelse
        </table>
      </div>
      @php $b = \App\Support\RekapNilai::BOBOT; @endphp
      <p class="m-note">Arahkan kursor ke nomor kolom untuk melihat judulnya. Kosong = belum dikumpulkan / belum dinilai.
        Nilai akhir = Tugas {{ $b['tugas'] }}% + Praktikum {{ $b['praktikum'] }}% + Quiz {{ $b['kuis'] }}% + Kehadiran {{ $b['kehadiran'] }}%.</p>
    @endif

    {{-- ================= PROGRES & AKTIVITAS ================= --}}
    @if ($tab === 'progres')
      @php $angka = fn ($n) => str_replace('.', ',', rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.')); @endphp
      <h2>Progres Mahasiswa</h2>
      <div class="rekap-wrap">
        <table class="rekap">
          <tr><th>Nama</th><th>Tugas & Praktikum Dikumpulkan</th><th>Quiz Dikerjakan</th><th>Kehadiran</th><th>Nilai Sementara</th></tr>
          @forelse ($rekap as $r)
            @php $kurang = $r['total_tugas'] - $r['dikumpulkan']; @endphp
            <tr>
              <td class="nama">{{ $r['mahasiswa']->name }}</td>
              <td>{{ $r['dikumpulkan'] }} / {{ $r['total_tugas'] }} @if ($kurang > 0) <span style="color:#dc2626">({{ $kurang }} belum)</span> @endif</td>
              <td>{{ $r['kuis_dikerjakan'] }} / {{ $r['total_kuis'] }}</td>
              <td>{{ $r['hadir'] }} / {{ $r['total_sesi'] }} ({{ $angka($r['kehadiran']) }}%)</td>
              <td class="akhir">{{ $angka($r['akhir']) }} ({{ $r['huruf'] }})</td>
            </tr>
          @empty
            <tr><td colspan="5" class="nama">Belum ada mahasiswa di kelas ini.</td></tr>
          @endforelse
        </table>
      </div>

      <h2>Aktivitas Terbaru</h2>
      <div class="m-list">
        @forelse ($aktivitas as $a)
          <div class="m-item">
            <span class="m-icon"><i class="fa-solid {{ $a['ikon'] }}"></i></span>
            <div class="teks">
              <h4 style="font-size:18px">{{ $a['nama'] }} {{ $a['teks'] }}</h4>
              <p>{{ Waktu::singkat($a['waktu']) }}</p>
            </div>
          </div>
        @empty
          <div class="m-item"><p class="m-muted">Belum ada aktivitas mahasiswa.</p></div>
        @endforelse
      </div>
    @endif

    {{-- ================= ANGGOTA ================= --}}
    @if ($tab === 'anggota')
      <h2>Anggota</h2>
      <p class="m-note" style="margin:-6px 0 14px">{{ $anggota->count() }} mahasiswa. Bagikan kode <b>{{ $kelas->kode_kelas }}</b> agar mahasiswa bisa bergabung.</p>
      <div class="m-list">
        @forelse ($anggota as $mhs)
          <div class="m-orang">
            <div>
              <h4>{{ $mhs->name }}</h4>
              <p>{{ $mhs->nim_nidn ?? '-' }}</p>
            </div>
            <form method="POST" action="{{ route('dosen.kelas.keluarkan', [$kelas, $mhs->id]) }}" onsubmit="return confirm('Keluarkan {{ $mhs->name }} dari kelas?')">
              @csrf @method('DELETE')
              <button class="m-btn outline small" style="padding:6px 14px">Keluarkan</button>
            </form>
          </div>
        @empty
          <div class="m-orang"><p class="m-muted">Belum ada mahasiswa yang bergabung.</p></div>
        @endforelse
      </div>
    @endif
  </div>
@endsection
