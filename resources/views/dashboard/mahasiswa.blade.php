@extends('layouts.panel')

@section('title', 'Dashboard Mahasiswa')

@section('content')
  @include('partials.banner', ['label' => 'Dashboard Mahasiswa', 'ikon' => 'fa-graduation-cap', 'sapaan' => auth()->user()->name,
      'judul' => 'Selamat datang di LMS!', 'sub' => 'Lanjutkan perjalanan pembelajaranmu di LMS.'])

  {{-- Pengingat singkat: tugas yang belum dikumpulkan dan kuis yang sedang dibuka --}}
  @if ($tugasBelum->count() || $kuisTersedia->count())
    <div class="m-flash ok" style="margin-top:24px;background:#fff;border-color:var(--line);color:var(--navy)">
      <i class="fa-solid fa-bell"></i>
      @if ($tugasBelum->count())
        {{ $tugasBelum->count() }} tugas belum dikumpulkan, terdekat
        <a href="{{ route('mahasiswa.tugas.show', $tugasBelum->first()) }}"><b>{{ $tugasBelum->first()->judul }}</b></a>
        ({{ $tugasBelum->first()->deadline->diffForHumans() }}).
      @endif
      @if ($kuisTersedia->count())
        {{ $kuisTersedia->count() }} quiz sedang dibuka:
        <a href="{{ route('mahasiswa.kuis.kerjakan', $kuisTersedia->first()) }}"><b>{{ $kuisTersedia->first()->judul }}</b></a>.
      @endif
    </div>
  @endif

  <div class="m-head-row">
    <h2>Kelas Saya</h2>
    <button type="button" class="m-btn yellow" data-buka="modal-gabung"><i class="fa-solid fa-plus"></i> Gabung Kelas</button>
  </div>

  <div class="m-grid">
    @forelse ($daftarKelas as $k)
      <a href="{{ route('mahasiswa.kelas.show', $k) }}" class="m-class">
        <div class="atas">
          <span class="kode">{{ $k->nama }}</span>
          <h3>{{ $k->mataKuliah->nama }}</h3>
          <small>{{ $k->dosen->name }}</small>
          @include('mahasiswa._avatar', ['u' => $k->dosen])
        </div>
        <div class="bawah">
          <span><i class="fa-solid fa-book-open"></i> {{ $k->materi_count }} Materi</span>
          <span><i class="fa-regular fa-file-lines"></i> {{ $k->tugas_count }} Tugas</span>
        </div>
      </a>
    @empty
      <div class="m-empty">Kamu belum bergabung ke kelas mana pun. Klik <b>Gabung Kelas</b> lalu masukkan kode kelas dari dosen.</div>
    @endforelse
  </div>

  {{-- Pop-up Gabung Kelas --}}
  <dialog id="modal-gabung" class="m-modal">
    <h2>Gabung Kelas</h2>
    <form method="POST" action="{{ route('mahasiswa.kelas.gabung') }}">
      @csrf
      <label for="kode_kelas">Masukkan Kode Kelas</label>
      <input type="text" id="kode_kelas" name="kode_kelas" value="{{ old('kode_kelas') }}" maxlength="10" required autocomplete="off" style="text-transform:uppercase">
      <div class="aksi"><button class="m-btn navy">Gabung</button></div>
    </form>
  </dialog>
@endsection

@push('scripts')
  @error('kode_kelas')
    <script>document.getElementById('modal-gabung').showModal();</script>
  @enderror
@endpush
