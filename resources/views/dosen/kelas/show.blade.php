@extends('layouts.panel')

@php
  use App\Support\RekapNilai;
  use App\Support\Waktu;

  $mataKuliah = $kelas->mataKuliah;
  $aksiTab = [
    'materi' => ['Tambah Materi', 'dosen.materi.create', 'fa-plus'],
    'tugas' => ['Tambah Tugas', 'dosen.tugas.create', 'fa-plus'],
    'kuis' => ['Tambah Kuis', 'dosen.kuis.create', 'fa-plus'],
    'absensi' => ['Tambah Absensi', 'dosen.absensi.create', 'fa-plus'],
    'nilai' => ['Unduh CSV', 'dosen.kelas.export', 'fa-download'],
  ][$tab] ?? null;

  $panel = 'min-w-0 rounded-xl border border-slate-200 bg-white shadow-sm';
  $judulBagian = 'border-b border-slate-100 px-4 py-3 text-base font-bold text-[#173d6e]';
  $baris = 'grid min-w-0 grid-cols-[auto_minmax(0,1fr)] items-start gap-x-3 gap-y-2 px-4 py-3
            md:grid-cols-[auto_minmax(0,1fr)_auto] md:items-center';
  $ikon = 'grid size-9 shrink-0 place-items-center rounded-lg bg-[#dfeffe] text-[#173d6e]';
  $judulItem = 'block font-semibold text-[#173d6e] no-underline [overflow-wrap:anywhere]';
  $meta = 'mt-0.5 text-sm leading-relaxed text-slate-600';
  $aksi = 'col-start-2 flex flex-wrap items-center gap-1 md:col-start-auto md:justify-end';
  $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
  $tombolIkon = "inline-grid size-9 shrink-0 place-items-center rounded-md no-underline $fokus";
  $normal = "$tombolIkon text-[#173d6e] hover:bg-[#dfeffe] disabled:opacity-35 disabled:hover:bg-transparent";
  $hapus = "$tombolIkon text-red-700 hover:bg-red-50";
  $tombol = "inline-flex min-h-9 items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold no-underline $fokus";
  $utama = "$tombol bg-[#173d6e] text-white hover:bg-[#2b62a8]";
  $sekunder = "$tombol border border-slate-200 bg-white text-[#173d6e] hover:bg-slate-50";
  $kosong = 'px-4 py-5 text-sm text-slate-600';
  $tabel = 'w-full min-w-max text-sm tabular-nums [&_th]:px-3 [&_th]:py-2.5 [&_td]:px-3 [&_td]:py-2.5';
  $angka = fn ($n) => $n === null ? '—' : rtrim(rtrim(number_format($n, 2, ',', ''), '0'), ',');
@endphp

@section('title', $mataKuliah->nama)

@section('content')
  <div class="min-w-0 space-y-4">
    <header class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-[#acd6fb] bg-[#dfeffe] p-4 sm:p-5">
      <div class="min-w-0 [overflow-wrap:anywhere]">
        <p class="text-xs font-semibold text-[#173d6e]">{{ $mataKuliah->kode }}</p>
        <h1 class="mt-0.5 text-xl font-extrabold tracking-tight text-[#173d6e] sm:text-2xl">{{ $mataKuliah->nama }}</h1>
        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-700">
          <span>{{ $kelas->nama }}</span>
          <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold
                       {{ $kelas->status === 'aktif' ? 'text-emerald-800' : 'text-slate-700' }}">
            {{ $kelas->status === 'aktif' ? 'Aktif' : 'Draf' }}
          </span>
        </div>
      </div>

      @if ($aksiTab)
        <a href="{{ route($aksiTab[1], $kelas) }}" class="{{ $utama }}">
          <i class="fa-solid {{ $aksiTab[2] }}" aria-hidden="true"></i>
          {{ $aksiTab[0] }}
        </a>
      @endif
    </header>

    @if ($tab === 'materi')
      <section class="{{ $panel }}">
        <h2 class="{{ $judulBagian }}">Materi</h2>
        <ul class="divide-y divide-slate-100">
          @forelse ($materi as $m)
            <li class="{{ $baris }}">
              <span class="{{ $ikon }}"><i class="fa-solid fa-book-open" aria-hidden="true"></i></span>
              <div class="min-w-0">
                <a href="{{ route('dosen.materi.show', $m) }}" class="{{ $judulItem }} hover:underline">{{ $m->judul }}</a>
                <p class="{{ $meta }}">
                  {{ Waktu::singkat($m->created_at) }}
                  · {{ $m->tipe === 'file' ? 'Lampiran' : 'Tautan' }}
                </p>
              </div>
              <div class="{{ $aksi }}">
                <a href="{{ route('dosen.materi.edit', $m) }}" class="{{ $normal }}" title="Ubah materi" aria-label="Ubah {{ $m->judul }}">
                  <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                </a>
                <form method="POST" action="{{ route('dosen.materi.destroy', $m) }}"
                      data-konfirmasi-judul="Hapus Materi" data-konfirmasi="Hapus materi {{ $m->judul }}?">
                  @csrf @method('DELETE')
                  <button type="submit" class="{{ $hapus }}" title="Hapus materi" aria-label="Hapus {{ $m->judul }}">
                    <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                  </button>
                </form>
              </div>
            </li>
          @empty
            <li class="{{ $kosong }}">Belum ada materi.</li>
          @endforelse
        </ul>
      </section>

    @elseif ($tab === 'tugas')
      @foreach (['tugas' => 'Tugas', 'praktikum' => 'Praktikum'] as $jenis => $label)
        <section class="{{ $panel }}">
          <h2 class="{{ $judulBagian }}">{{ $label }}</h2>
          <ul class="divide-y divide-slate-100">
            @forelse ($tugas->where('jenis', $jenis)->sortByDesc('created_at') as $t)
              <li class="{{ $baris }}">
                <span class="{{ $ikon }}">
                  <i class="fa-solid {{ $jenis === 'tugas' ? 'fa-file-code' : 'fa-clipboard-list' }}" aria-hidden="true"></i>
                </span>
                <div class="min-w-0">
                  <a href="{{ route('dosen.tugas.show', $t) }}" class="{{ $judulItem }} hover:underline">{{ $t->judul }}</a>
                  <p class="{{ $meta }}">
                    Dibuat {{ Waktu::singkat($t->created_at) }} · Batas {{ Waktu::singkat($t->deadline) }}
                    · {{ $t->pengumpulan_count }}/{{ $anggota->count() }} mengumpulkan
                  </p>
                </div>
                <div class="{{ $aksi }}">
                  <a href="{{ route('dosen.tugas.terkumpul', $t) }}" class="{{ $normal }}" title="Lihat pengumpulan" aria-label="Lihat pengumpulan {{ $t->judul }}">
                    <i class="fa-regular fa-eye" aria-hidden="true"></i>
                  </a>
                  <a href="{{ route('dosen.tugas.edit', $t) }}" class="{{ $normal }}" title="Ubah {{ strtolower($label) }}" aria-label="Ubah {{ $t->judul }}">
                    <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                  </a>
                  <form method="POST" action="{{ route('dosen.tugas.destroy', $t) }}"
                        data-konfirmasi-judul="Hapus {{ $label }}"
                        data-konfirmasi="Hapus {{ $t->judul }}? Semua jawaban mahasiswa ikut terhapus.">
                    @csrf @method('DELETE')
                    <button type="submit" class="{{ $hapus }}" title="Hapus {{ strtolower($label) }}" aria-label="Hapus {{ $t->judul }}">
                      <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                    </button>
                  </form>
                </div>
              </li>
            @empty
              <li class="{{ $kosong }}">Belum ada {{ strtolower($label) }}.</li>
            @endforelse
          </ul>
        </section>
      @endforeach

    @elseif ($tab === 'kuis')
      <section class="{{ $panel }}">
        <h2 class="{{ $judulBagian }}">Kuis</h2>
        <ul class="divide-y divide-slate-100">
          @forelse ($kuis->sortByDesc('mulai') as $q)
            @php $dibuka = $q->sedangDibuka(); @endphp
            <li class="{{ $baris }}">
              <span class="{{ $ikon }}"><i class="fa-regular fa-circle-question" aria-hidden="true"></i></span>
              <div class="min-w-0">
                <h3 class="{{ $judulItem }}">{{ $q->judul }}</h3>
                <p class="{{ $meta }}">
                  Mulai {{ $q->mulai->translatedFormat('j M Y, H:i') }} · {{ $q->durasi_menit }} menit
                  · {{ $q->soal_count }} soal · {{ $q->hasil_count }} sudah mengerjakan
                </p>
                <p class="mt-1 text-xs font-semibold {{ $dibuka ? 'text-emerald-800' : 'text-slate-600' }}">
                  {{ $dibuka ? 'Sedang dibuka sampai ' . $q->selesai->format('H:i') : $q->status() }}
                </p>
              </div>
              <div class="{{ $aksi }}">
                <form method="POST" action="{{ route($dibuka ? 'dosen.kuis.tutup' : 'dosen.kuis.buka', $q) }}"
                      @if ($dibuka)
                        data-konfirmasi-judul="Tutup Sesi"
                        data-konfirmasi="Tutup kuis {{ $q->judul }}? Mahasiswa tidak bisa melanjutkan pengerjaan."
                      @endif>
                  @csrf
                  <button type="submit" class="{{ $dibuka ? $sekunder : $utama }}">{{ $dibuka ? 'Tutup Sesi' : 'Buka Sesi' }}</button>
                </form>
                <a href="{{ route('dosen.kuis.show', $q) }}" class="{{ $normal }}" title="Live Score" aria-label="Lihat skor {{ $q->judul }}">
                  <i class="fa-regular fa-eye" aria-hidden="true"></i>
                </a>
                <a href="{{ route('dosen.kuis.edit', $q) }}" class="{{ $normal }}" title="Ubah kuis" aria-label="Ubah {{ $q->judul }}">
                  <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                </a>
                <form method="POST" action="{{ route('dosen.kuis.destroy', $q) }}"
                      data-konfirmasi-judul="Hapus Kuis" data-konfirmasi="Hapus {{ $q->judul }}? Nilai mahasiswa ikut terhapus.">
                  @csrf @method('DELETE')
                  <button type="submit" class="{{ $hapus }}" title="Hapus kuis" aria-label="Hapus {{ $q->judul }}">
                    <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                  </button>
                </form>
              </div>
            </li>
          @empty
            <li class="{{ $kosong }}">Belum ada kuis.</li>
          @endforelse
        </ul>
      </section>

    @elseif ($tab === 'absensi')
      <section class="{{ $panel }}">
        <h2 class="{{ $judulBagian }}">Absensi</h2>
        <ul class="divide-y divide-slate-100">
          @forelse ($kelas->pertemuan->sortBy('nomor') as $p)
            @php $sesi = $p->sesiAbsensi; @endphp
            <li class="{{ $baris }}">
              <span class="{{ $ikon }}"><i class="fa-regular fa-calendar-check" aria-hidden="true"></i></span>
              <div class="min-w-0">
                <a href="{{ route('dosen.absensi.show', $p) }}" class="{{ $judulItem }} hover:underline">
                  Pertemuan {{ $p->nomor }}{{ $p->judul ? ' · ' . $p->judul : '' }}
                </a>
                <p class="{{ $meta }}">
                  {{ $p->tanggal?->translatedFormat('l, j F Y') ?? 'Tanggal belum ditentukan' }}
                  @if ($sesi) · {{ $sesi->dibuka_pada->format('H:i') }}–{{ $sesi->ditutup_pada->format('H:i') }} @endif
                </p>
                @if ($sesi?->sedangDibuka())
                  <p class="mt-1 text-xs font-semibold text-emerald-800">Sedang dibuka</p>
                @endif
              </div>
              <div class="{{ $aksi }}">
                @foreach (['naik' => ['fa-arrow-up', 'Naikkan urutan', ! $loop->first], 'turun' => ['fa-arrow-down', 'Turunkan urutan', ! $loop->last]] as $arah => [$ikonArah, $teks, $boleh])
                  <form method="POST" action="{{ route('dosen.absensi.urut', [$p, $arah]) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="{{ $normal }}" title="{{ $teks }}" aria-label="{{ $teks }} pertemuan {{ $p->nomor }}" @disabled(! $boleh)>
                      <i class="fa-solid {{ $ikonArah }}" aria-hidden="true"></i>
                    </button>
                  </form>
                @endforeach
                <a href="{{ route('dosen.absensi.edit', $p) }}" class="{{ $normal }}" title="Ubah pertemuan" aria-label="Ubah pertemuan {{ $p->nomor }}">
                  <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                </a>
                <form method="POST" action="{{ route('dosen.absensi.destroy', $p) }}"
                      data-konfirmasi-judul="Hapus Absensi" data-konfirmasi="Hapus absensi pertemuan {{ $p->nomor }}?">
                  @csrf @method('DELETE')
                  <button type="submit" class="{{ $hapus }}" title="Hapus absensi" aria-label="Hapus absensi pertemuan {{ $p->nomor }}">
                    <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                  </button>
                </form>
              </div>
            </li>
          @empty
            <li class="{{ $kosong }}">Belum ada absensi.</li>
          @endforelse
        </ul>
      </section>

    @elseif ($tab === 'nilai')
      @php
        $kelompok = [
          'Tugas' => $tugas->where('jenis', 'tugas')->values(),
          'Praktikum' => $tugas->where('jenis', 'praktikum')->values(),
          'Kuis' => $kuis->values(),
        ];
        $jumlahKolom = 4 + collect($kelompok)->sum(fn ($daftar) => max($daftar->count(), 1));
        $bobot = RekapNilai::BOBOT;
      @endphp
      <section class="{{ $panel }}">
        <h2 id="judul-nilai" class="{{ $judulBagian }}">Rekap Nilai</h2>
        <div class="overflow-x-auto {{ $fokus }}" tabindex="0" role="region" aria-labelledby="judul-nilai">
          <table class="{{ $tabel }} text-center">
            <colgroup><col></colgroup>
            @foreach ($kelompok as $daftar)
              <colgroup span="{{ max($daftar->count(), 1) }}"></colgroup>
            @endforeach
            <colgroup span="3"></colgroup>
            <thead class="bg-[#dfeffe] text-[#173d6e]">
              <tr>
                <th scope="col" rowspan="2" class="text-left">Nama</th>
                @foreach ($kelompok as $label => $daftar)
                  <th scope="colgroup" colspan="{{ max($daftar->count(), 1) }}">{{ $label }}</th>
                @endforeach
                <th scope="col" rowspan="2">Hadir</th>
                <th scope="col" rowspan="2">Akhir</th>
                <th scope="col" rowspan="2">Huruf</th>
              </tr>
              <tr class="border-t border-[#acd6fb]">
                @foreach ($kelompok as $daftar)
                  @forelse ($daftar as $item)
                    <th scope="col" class="text-xs" title="{{ $item->judul }}" aria-label="{{ $item->judul }}">{{ $loop->iteration }}</th>
                  @empty
                    <th scope="col" class="text-xs">—</th>
                  @endforelse
                @endforeach
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @forelse ($anggota as $mhs)
                @php
                  $r = $rekap[$mhs->id] ?? null;
                  $nt = $nilaiTugas[$mhs->id] ?? [];
                  $nk = $nilaiKuis[$mhs->id] ?? [];
                @endphp
                <tr class="hover:bg-slate-50">
                  <th scope="row" class="text-left font-semibold">{{ $mhs->name }}</th>
                  @foreach ($kelompok as $label => $daftar)
                    @forelse ($daftar as $item)
                      <td>{{ $label === 'Kuis' ? $angka($nk[$item->id]->skor ?? null) : ($nt[$item->id]->nilai ?? '—') }}</td>
                    @empty
                      <td>—</td>
                    @endforelse
                  @endforeach
                  <td>{{ $r ? $angka($r['kehadiran']) . '%' : '—' }}</td>
                  <td class="font-bold text-[#173d6e]">{{ $angka($r['akhir'] ?? null) }}</td>
                  <td class="font-bold text-[#173d6e]">{{ $r['huruf'] ?? '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="{{ $jumlahKolom }}" class="text-left text-slate-600">Belum ada mahasiswa di kelas ini.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <p class="border-t border-slate-100 px-4 py-3 text-sm text-slate-600">
          Tanda — berarti nilai belum tersedia. Bobot nilai akhir menggunakan tugas {{ $bobot['tugas'] }}%,
          praktikum {{ $bobot['praktikum'] }}%, kuis {{ $bobot['kuis'] }}%, dan kehadiran {{ $bobot['kehadiran'] }}%.
        </p>
      </section>

    @elseif ($tab === 'progres')
      <section class="{{ $panel }}">
        <h2 id="judul-progres" class="{{ $judulBagian }}">Progres Mahasiswa</h2>
        <div class="overflow-x-auto {{ $fokus }}" tabindex="0" role="region" aria-labelledby="judul-progres">
          <table class="{{ $tabel }} text-left">
            <thead class="bg-[#dfeffe] text-[#173d6e]">
              <tr>
                @foreach (['Nama', 'Tugas & Praktikum', 'Kuis', 'Kehadiran', 'Nilai Sementara'] as $label)
                  <th scope="col">{{ $label }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @forelse ($rekap as $r)
                @php $kurang = $r['total_tugas'] - $r['dikumpulkan']; @endphp
                <tr class="hover:bg-slate-50">
                  <th scope="row" class="font-semibold">{{ $r['mahasiswa']->name }}</th>
                  <td>
                    {{ $r['dikumpulkan'] }}/{{ $r['total_tugas'] }}
                    @if ($kurang > 0)<span class="text-red-700">({{ $kurang }} belum)</span>@endif
                  </td>
                  <td>{{ $r['kuis_dikerjakan'] }}/{{ $r['total_kuis'] }}</td>
                  <td>{{ $r['hadir'] }}/{{ $r['total_sesi'] }} ({{ $angka($r['kehadiran']) }}%)</td>
                  <td class="font-bold text-[#173d6e]">{{ $angka($r['akhir']) }} ({{ $r['huruf'] }})</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-slate-600">Belum ada mahasiswa di kelas ini.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>

      <section class="{{ $panel }}">
        <h2 class="{{ $judulBagian }}">Aktivitas Terbaru</h2>
        <ul class="divide-y divide-slate-100">
          @forelse ($aktivitas as $a)
            <li class="flex items-start gap-3 px-4 py-3">
              <span class="{{ $ikon }}"><i class="fa-solid {{ $a['ikon'] }}" aria-hidden="true"></i></span>
              <div class="min-w-0 [overflow-wrap:anywhere]">
                <p class="text-sm"><strong class="font-semibold text-[#173d6e]">{{ $a['nama'] }}</strong> {{ $a['teks'] }}</p>
                <p class="{{ $meta }}">{{ Waktu::singkat($a['waktu']) }}</p>
              </div>
            </li>
          @empty
            <li class="{{ $kosong }}">Belum ada aktivitas mahasiswa.</li>
          @endforelse
        </ul>
      </section>

    @elseif ($tab === 'anggota')
      <section class="{{ $panel }}">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-4 py-3">
          <h2 class="text-base font-bold text-[#173d6e]">Anggota</h2>
          <span class="text-sm text-slate-600">{{ $anggota->count() }} mahasiswa</span>
        </div>
        <ul class="divide-y divide-slate-100">
          @forelse ($anggota as $mhs)
            <li class="{{ $baris }}">
              <span class="{{ $ikon }}"><i class="fa-regular fa-user" aria-hidden="true"></i></span>
              <div class="min-w-0">
                <p class="{{ $judulItem }}">{{ $mhs->name }}</p>
                <p class="{{ $meta }}">{{ $mhs->nim_nidn ?? '—' }}</p>
              </div>
              <form method="POST" action="{{ route('dosen.kelas.keluarkan', [$kelas, $mhs->id]) }}" class="{{ $aksi }}"
                    data-konfirmasi-judul="Keluarkan Mahasiswa" data-konfirmasi="Keluarkan {{ $mhs->name }} dari kelas?">
                @csrf @method('DELETE')
                <button type="submit" class="{{ $sekunder }}" aria-label="Keluarkan {{ $mhs->name }}">Keluarkan</button>
              </form>
            </li>
          @empty
            <li class="{{ $kosong }}">Belum ada mahasiswa yang bergabung.</li>
          @endforelse
        </ul>
      </section>
    @endif
  </div>
@endsection