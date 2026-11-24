@extends('layouts.panel')

@section('title', 'Kelola Kelas')

@section('content')
  <div class="a-head">
    <h1>Kelola Kelas</h1>
    <div class="kanan">
      <form method="GET" class="a-cari">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari kelas / dosen">
      </form>
      <span class="total">Total Kelas : {{ $kelas->count() }}</span>
    </div>
  </div>

  <div class="a-kelas">
    @forelse ($kelas as $k)
      <div class="kartu">
        <h3>{{ $k->mataKuliah->nama }}</h3>
        <div class="dosen">{{ $k->dosen->name }}</div>
        <p>Kelas : {{ $k->nama }}</p>
        <p>Anggota : {{ $k->mahasiswa_count }}</p>
        <p>Status : {{ ucfirst($k->status) }}</p>
        <div class="aksi-ikon">
          <a href="{{ route('admin.kelas.show', $k) }}" class="ikon lihat" title="Lihat"><i class="fa-regular fa-eye"></i><span class="sr">Lihat</span></a>
          <a href="{{ route('admin.kelas.edit', $k) }}" class="ikon ubah" title="Ubah"><i class="fa-regular fa-pen-to-square"></i><span class="sr">Ubah</span></a>
          {{-- @include('dosen._hapus', ['aksi' => route('admin.kelas.destroy', $k), 'tanya' => 'Hapus kelas ' . $k->mataKuliah->nama . ' (' . $k->nama . ')? Semua materi, tugas dan nilai ikut terhapus.']) --}}
        </div>
      </div>
    @empty
      <p class="m-muted">Belum ada kelas.</p>
    @endforelse
  </div>
@endsection
