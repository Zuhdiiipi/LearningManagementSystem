@extends('layouts.panel')

@section('title', 'Dashboard Dosen')

@section('content')
  @include('partials.banner', ['label' => 'Dashboard Dosen', 'ikon' => 'fa-chalkboard-user', 'sapaan' => auth()->user()->name,
      'judul' => 'Selamat datang di LMS!', 'sub' => 'Lanjutkan perjalanan Mengajar di LMS.'])

  @if ($perluDinilai)
    <div class="m-flash ok" style="margin-top:24px;background:#fff;border-color:var(--line);color:var(--navy)">
      <i class="fa-solid fa-bell"></i> Ada <b>{{ $perluDinilai }}</b> tugas/praktikum mahasiswa yang belum dinilai.
    </div>
  @endif

  <div class="m-head-row">
    <h2>Kelas Saya</h2>
    {{-- <a href="{{ route('dosen.kelas.create') }}" class="m-btn yellow"><i class="fa-solid fa-plus"></i> Buat Kelas</a> --}}
  </div>

  <div class="m-grid">
    @forelse ($daftarKelas as $k)
      <div class="m-class-wrap">
        {{-- Menu titik tiga: ubah / hapus kelas --}}
        <details class="m-more">
          <summary title="Pilihan"><i class="fa-solid fa-ellipsis-vertical"></i><span class="sr">Pilihan</span></summary>
          <div>
            {{-- <a href="{{ route('dosen.kelas.edit', $k) }}"><i class="fa-solid fa-pen"></i> Ubah Kelas</a> --}}
            {{-- <form method="POST" action="{{ route('dosen.kelas.destroy', $k) }}" onsubmit="return confirm('Hapus kelas {{ $k->mataKuliah->nama }}? Semua materi, tugas, quiz, dan nilai di kelas ini ikut terhapus.')">
              @csrf @method('DELETE')
              <button><i class="fa-solid fa-trash"></i> Hapus Kelas</button>
            </form> --}}
          </div>
        </details>
        {{-- <a href="{{ route('dosen.kelas.show', $k) }}" class="m-class"> --}}
          <div class="atas">
            <h3>{{ $k->mataKuliah->nama }} @if ($k->status === 'draft')<span class="draft">Draft</span>@endif</h3>
            <small>{{ $k->dosen->name }} · {{ $k->nama }}</small>
            {{-- @include('mahasiswa._avatar', ['u' => $k->dosen]) --}}
          </div>
          <div class="bawah">
            <span><i class="fa-solid fa-book-open"></i> {{ $k->materi_count }} Materi</span>
            <span><i class="fa-regular fa-file-lines"></i> {{ $k->tugas_count }} Tugas</span>
            <span><i class="fa-solid fa-users"></i> {{ $k->mahasiswa_count }} Mahasiswa</span>
          </div>
        </a>
      </div>
    @empty
      <div class="m-empty">Belum ada kelas. Klik <b>Buat Kelas</b> untuk membuat kelas pertama.</div>
    @endforelse
  </div>
@endsection
