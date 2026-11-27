@extends('layouts.panel')

@section('title', $kelas->mataKuliah->nama)

@php use App\Support\Waktu; @endphp

@section('content')
  @php
    $lihatNilai = $tab === 'nilai';
    $jenisTugas = ['tugas' => 'Tugas', 'praktikum' => 'Praktikum'];
    $panel = 'min-w-0 overflow-hidden rounded-xl border border-solid border-slate-200 bg-white shadow-sm';
    $kepala = 'm-0 border-0 border-b border-solid border-slate-100 px-4 py-3 text-base font-bold';
    $daftar = 'divide-y divide-solid divide-slate-100';
    $baris = 'grid min-w-0 grid-cols-[auto_minmax(0,1fr)] items-start gap-x-3 gap-y-1 px-4 py-3.5
              sm:grid-cols-[auto_minmax(0,1fr)_auto] sm:items-center';
    $tautan = 'text-[#173d6e] no-underline hover:bg-[#f4f7fb] focus-visible:outline-2
               focus-visible:-outline-offset-2 focus-visible:outline-[#2b62a8]';
    $ikon = 'grid size-9 shrink-0 place-items-center rounded-lg bg-[#dfeffe] text-sm text-[#173d6e]';
    $isi = 'min-w-0 [overflow-wrap:anywhere]';
    $judul = 'm-0 text-sm leading-snug font-bold sm:text-base';
    $detail = 'mt-1 mb-0 text-sm leading-snug text-slate-600';
    $kanan = 'col-start-2 justify-self-start sm:col-start-3 sm:row-start-1 sm:justify-self-end';
    $badge = 'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold';
    $netral = 'bg-slate-100 text-slate-700';
    $biru = 'bg-[#dfeffe] text-[#173d6e]';
    $hijau = 'bg-emerald-50 text-emerald-800';
    $kuning = 'bg-amber-50 text-amber-900';
    $merah = 'bg-red-50 text-red-800';
    $kosong = 'm-0 px-4 py-5 text-sm text-slate-600';
    $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
    $tombol = 'min-h-10 cursor-pointer rounded-lg px-3 py-2 font-sans text-sm font-semibold';
    $utama = 'border-0 bg-[#173d6e] text-white hover:bg-[#2b62a8]';
    $formatNilai = static fn ($nilai) => rtrim(rtrim(number_format($nilai, 2, ',', '.'), '0'), ',');
    $formatWaktu = static fn ($waktu) => $waktu?->copy()->locale('id')->translatedFormat('j M Y, H:i') ?? '—';
  @endphp

  <div class="space-y-5 text-[#173d6e]">
    <header class="rounded-xl border border-solid border-[#acd6fb] bg-[#dfeffe] px-4 py-4 sm:px-5">
      <h1 class="m-0 text-xl leading-tight font-extrabold tracking-tight [overflow-wrap:anywhere] sm:text-2xl">
        {{ $kelas->mataKuliah->nama }}
      </h1>
      <p class="mt-1 mb-0 flex flex-wrap gap-x-2 gap-y-1 text-sm text-[#4d6a92]">
        <span class="font-semibold text-[#173d6e]">{{ $kelas->nama }}</span>
        @if ($kelas->dosen)
          <span aria-hidden="true">·</span>
          <span>{{ $kelas->dosen->name }}</span>
        @endif
      </p>
    </header>

    @if ($lihatNilai)
      @php $bobot = \App\Support\RekapNilai::bobot($kelas); @endphp
      <section aria-labelledby="judul-nilai" class="{{ $panel }} grid sm:grid-cols-[auto_minmax(0,1fr)]">
        <div class="bg-[#173d6e] px-5 py-4 text-white">
          <h2 id="judul-nilai" class="m-0 text-sm font-semibold text-[#acd6fb]">Nilai akhir</h2>
          <p class="mt-1 mb-0 flex flex-wrap items-baseline gap-2">
            <strong class="text-3xl font-extrabold tabular-nums">{{ $formatNilai($rekap['akhir'] ?? 0) }}</strong>
            @if (isset($rekap['huruf']))
              <span class="{{ $badge }} bg-white/15 text-white">{{ $rekap['huruf'] }}</span>
            @endif
          </p>
        </div>

        <div class="p-4">
          <h3 class="m-0 text-sm font-bold">Bobot penilaian</h3>
          <dl class="mt-2 mb-0 grid grid-cols-2 gap-2 lg:grid-cols-4">
            @foreach ($jenisTugas + ['kuis' => 'Kuis', 'kehadiran' => 'Kehadiran'] as $jenis => $label)
              <div class="rounded-lg bg-[#f4f7fb] px-3 py-2">
                <dt class="text-xs text-slate-600">{{ $label }}</dt>
                <dd class="m-0 text-sm font-bold tabular-nums">{{ $bobot[$jenis] }}%</dd>
              </div>
            @endforeach
          </dl>
          <p class="{{ $detail }}">Hadir pada {{ $rekap['hadir'] ?? 0 }} dari {{ $rekap['total_sesi'] ?? 0 }} sesi absensi.</p>
        </div>
      </section>
    @endif

    @if ($tab === 'materi')
      <section aria-labelledby="judul-materi" class="{{ $panel }}">
        <h2 id="judul-materi" class="{{ $kepala }}">Materi</h2>
        <div class="{{ $daftar }}">
          @forelse ($materi as $m)
            <a href="{{ route('mahasiswa.materi.show', $m) }}" class="{{ $baris }} {{ $tautan }}">
              <span class="{{ $ikon }}"><i class="fa-solid fa-book-open" aria-hidden="true"></i></span>
              <div class="{{ $isi }}">
                <h3 class="{{ $judul }}">{{ $m->judul }}</h3>
                <p class="{{ $detail }}">{{ Waktu::singkat($m->created_at?->copy()->locale('id')) }}</p>
              </div>
              <i class="fa-solid fa-chevron-right hidden text-xs text-[#2b62a8] sm:block" aria-hidden="true"></i>
            </a>
          @empty
            <p class="{{ $kosong }}">Dosen belum menambahkan materi di kelas ini.</p>
          @endforelse
        </div>
      </section>
    @endif

    @if ($tab === 'tugas' || $lihatNilai)
      <div class="grid items-start gap-4 xl:grid-cols-2">
        @foreach ($jenisTugas as $jenis => $label)
          @php
            $daftarTugas = $tugas->where('jenis', $jenis);
            if (! $lihatNilai) {
                $daftarTugas = $daftarTugas->sortByDesc('created_at');
            }
          @endphp

          <section aria-labelledby="judul-{{ $jenis }}" class="{{ $panel }}">
            <h2 id="judul-{{ $jenis }}" class="{{ $kepala }}">{{ $label }}</h2>
            <div class="{{ $daftar }}">
              @forelse ($daftarTugas as $t)
                @php
                  $jawaban = $pengumpulan[$t->id] ?? null;

                  if ($lihatNilai) {
                      [$warna, $status] = $jawaban && $jawaban->nilai === null
                          ? [$kuning, 'Belum dinilai']
                          : [$biru, $formatNilai($jawaban?->nilai ?? 0) . ' / 100'];
                  } else {
                      $jawaban?->setRelation('tugas', $t);
                      [$warna, $status] = match (true) {
                          $jawaban?->terlambat() === true => [$merah, 'Terlambat'],
                          $jawaban !== null => [$hijau, 'Terkumpul'],
                          $t->sudahLewat() => [$netral, 'Lewat tenggat'],
                          default => [$kuning, 'Belum dikumpulkan'],
                      };
                  }
                @endphp

                <a href="{{ route('mahasiswa.tugas.show', $t) }}" class="{{ $baris }} {{ $tautan }}">
                  <span class="{{ $ikon }}">
                    <i class="fa-solid {{ $jenis === 'tugas' ? 'fa-file-lines' : 'fa-clipboard-list' }}" aria-hidden="true"></i>
                  </span>
                  <div class="{{ $isi }}">
                    <h3 class="{{ $judul }}">{{ $t->judul }}</h3>
                    <p class="{{ $detail }}">
                      {{ Waktu::singkat($t->created_at?->copy()->locale('id')) }} · Tenggat {{ $formatWaktu($t->deadline) }}
                    </p>
                  </div>
                  <span class="{{ $kanan }} {{ $badge }} {{ $warna }}">{{ $status }}</span>
                </a>
              @empty
                <p class="{{ $kosong }}">Belum ada {{ strtolower($label) }}.</p>
              @endforelse
            </div>
          </section>
        @endforeach
      </div>
    @endif

    @if ($tab === 'kuis' || $lihatNilai)
      <section aria-labelledby="judul-kuis" class="{{ $panel }}">
        <h2 id="judul-kuis" class="{{ $kepala }}">Kuis</h2>
        <div class="{{ $daftar }}">
          @forelse ($lihatNilai ? $kuis : $kuis->sortByDesc('mulai') as $q)
            @php
              $hasil = $hasilKuis[$q->id] ?? null;
              $dibuka = $q->sedangDibuka();
              $skor = $formatNilai($hasil?->skor ?? 0) . ' / 100';
            @endphp
            <article class="{{ $baris }} relative">
              <span class="{{ $ikon }}"><i class="fa-regular fa-circle-question" aria-hidden="true"></i></span>
              <div class="{{ $isi }}">
                <h3 class="{{ $judul }}">
                  @if ($lihatNilai)
                    {{ $q->judul }}
                  @else
                    <a href="{{ route($hasil ? 'mahasiswa.kuis.hasil' : 'mahasiswa.kuis.kerjakan', $q) }}"
                       class="{{ $fokus }} rounded-sm text-[#173d6e] no-underline after:absolute
                              after:inset-0 hover:text-[#2b62a8] hover:underline">
                      {{ $q->judul }}
                    </a>
                  @endif
                </h3>
                <p class="{{ $detail }}">{{ $formatWaktu($q->mulai) }} · {{ $q->durasi_menit }} menit · {{ $q->soal_count }} soal</p>
                @if (! $lihatNilai && ! $hasil && $dibuka)
                  <p class="{{ $detail }}">Ditutup {{ $formatWaktu($q->selesai) }}</p>
                @endif
              </div>

              @if ($lihatNilai || $hasil)
                <span class="{{ $kanan }} {{ $badge }} {{ $biru }}">{{ $skor }}</span>
              @else
                <span class="{{ $kanan }} {{ $badge }} {{ $dibuka ? $hijau : $netral }}">{{ $q->status() }}</span>
              @endif
            </article>
          @empty
            <p class="{{ $kosong }}">Belum ada kuis.</p>
          @endforelse
        </div>
      </section>
    @endif

    @if ($tab === 'absensi')
      <section aria-labelledby="judul-absensi" class="{{ $panel }}">
        <h2 id="judul-absensi" class="{{ $kepala }}">Absensi</h2>
        <div class="{{ $daftar }}">
          @forelse ($kelas->pertemuan->sortByDesc('nomor') as $p)
            @php
              $sesi = $p->sesiAbsensi;
              $catatan = $sesi ? ($absensiSaya[$sesi->id] ?? null) : null;
              $bolehAbsen = ! $catatan && $sesi?->sedangDibuka();
              $tanggal = $p->tanggal?->copy()->locale('id')->translatedFormat('l, j F Y') ?? 'Tanggal belum ditentukan';
              $waktu = $sesi ? $sesi->dibuka_pada->format('H:i') . '–' . $sesi->ditutup_pada->format('H:i') : null;
              $statusAbsen = $catatan?->status ?? ($sesi?->sudahDitutup() ? 'alpa' : 'belum dibuka');
              $warnaAbsen = match ($statusAbsen) {
                  'hadir' => $hijau,
                  'alpa' => $merah,
                  'belum dibuka' => $netral,
                  default => $kuning,
              };
            @endphp

            <article class="{{ $baris }}">
              <span class="{{ $ikon }}"><i class="fa-regular fa-calendar-check" aria-hidden="true"></i></span>
              <div class="{{ $isi }}">
                <h3 class="{{ $judul }}">Pertemuan {{ $p->nomor }}</h3>
                @if ($p->judul)
                  <p class="{{ $detail }}">{{ $p->judul }}</p>
                @endif
                <p class="{{ $detail }}">{{ $tanggal }}{{ $waktu ? ' · ' . $waktu : '' }}</p>
              </div>

              @if ($bolehAbsen)
                <button type="button" data-buka="absen-{{ $p->id }}" aria-haspopup="dialog"
                        class="{{ $kanan }} {{ $tombol }} {{ $utama }} {{ $fokus }}">
                  Tandai hadir
                </button>
              @else
                <span class="{{ $kanan }} {{ $badge }} {{ $warnaAbsen }}">{{ ucfirst($statusAbsen) }}</span>
              @endif
            </article>
          @empty
            <p class="{{ $kosong }}">Belum ada pertemuan.</p>
          @endforelse
        </div>
      </section>

      @foreach ($kelas->pertemuan as $p)
        @php $sesi = $p->sesiAbsensi; @endphp
        @continue(! $sesi || isset($absensiSaya[$sesi->id]) || ! $sesi->sedangDibuka())

        <dialog id="absen-{{ $p->id }}" aria-labelledby="judul-absen-{{ $p->id }}"
                class="m-auto max-h-[calc(100dvh_-_2rem)] w-[calc(100%_-_2rem)] max-w-sm
                       overflow-y-auto rounded-2xl border border-solid border-slate-200
                       bg-white p-0 text-[#173d6e] shadow-xl backdrop:bg-slate-900/45">
          <div class="p-5">
            <h2 id="judul-absen-{{ $p->id }}" class="m-0 text-lg font-bold">Tandai kehadiran</h2>
            <div class="mt-3 rounded-lg bg-[#f4f7fb] p-3">
              <p class="m-0 font-semibold [overflow-wrap:anywhere]">{{ $kelas->mataKuliah->nama }}</p>
              <p class="{{ $detail }}">Pertemuan {{ $p->nomor }} · {{ $kelas->nama }}</p>
              <p class="{{ $detail }}">Sesi dibuka {{ $formatWaktu($sesi->dibuka_pada) }}</p>
              <p class="{{ $detail }}">Sesi ditutup {{ $formatWaktu($sesi->ditutup_pada) }}</p>
            </div>

            <div class="mt-4 flex flex-wrap justify-end gap-2">
              <form method="dialog">
                <button type="submit" autofocus
                        class="{{ $tombol }} {{ $fokus }} border border-solid border-slate-300 bg-white text-[#173d6e] hover:bg-[#f4f7fb]">
                  Batal
                </button>
              </form>
              <form method="POST" action="{{ route('mahasiswa.absensi.hadir', $p) }}">
                @csrf
                <button type="submit" class="{{ $tombol }} {{ $utama }} {{ $fokus }}">Tandai hadir</button>
              </form>
            </div>
          </div>
        </dialog>
      @endforeach
    @endif
  </div>
@endsection