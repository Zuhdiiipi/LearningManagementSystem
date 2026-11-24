@extends('layouts.panel')

@section('title', $kelas->exists ? 'Ubah Kelas' : 'Buat Kelas')

@section('content')
  <form method="POST" action="{{ $kelas->exists ? route('dosen.kelas.update', $kelas) : route('dosen.kelas.store') }}" class="m-box lebar m-form" style="min-height:0">
    @csrf
    @if ($kelas->exists) @method('PUT') @endif

    <label for="nama">Nama Kelas</label>
    <input type="text" id="nama" name="nama" value="{{ old('nama', $kelas->nama) }}" placeholder="Contoh: Informatika B 23" required>

    <label for="mata_kuliah_id">Mata Kuliah</label>
    <select id="mata_kuliah_id" name="mata_kuliah_id" required>
      <option value="">-- Pilih Mata Kuliah --</option>
      @foreach ($matkul as $m)
        <option value="{{ $m->id }}" data-prodi="{{ $m->program_studi_id }}" @selected(old('mata_kuliah_id', $kelas->mata_kuliah_id) == $m->id)>{{ $m->kode }} - {{ $m->nama }}</option>
      @endforeach
    </select>

    <label for="prodi">Program Studi</label>
    <select id="prodi" class="pendek">
      <option value="">Semua</option>
      @foreach ($prodi as $p)
        <option value="{{ $p->id }}" @selected(optional($kelas->mataKuliah)->program_studi_id == $p->id)>{{ $p->nama }}</option>
      @endforeach
    </select>
    <p class="petunjuk">Pilih program studi untuk menyaring daftar mata kuliah di atas.</p>

    <span class="label">Semester</span>
    <div class="pilih">
      <label><input type="radio" name="semester" value="ganjil" @checked(old('semester', $kelas->semester) === 'ganjil')> Ganjil</label>
      <label><input type="radio" name="semester" value="genap" @checked(old('semester', $kelas->semester) === 'genap')> Genap</label>
    </div>

    <label for="tahun_ajaran_id">Tahun Akademik</label>
    <select id="tahun_ajaran_id" name="tahun_ajaran_id" class="pendek" required>
      @foreach ($tahun as $t)
        <option value="{{ $t->id }}" @selected(old('tahun_ajaran_id', $kelas->tahun_ajaran_id) == $t->id)>{{ $t->nama }}{{ $t->is_active ? ' (aktif)' : '' }}</option>
      @endforeach
    </select>

    <span class="label">Status Kelas</span>
    <div class="pilih tegak">
      <label><input type="radio" name="status" value="aktif" @checked(old('status', $kelas->status) === 'aktif')> Aktif</label>
      <label><input type="radio" name="status" value="draft" @checked(old('status', $kelas->status) === 'draft')> Draft</label>
    </div>
    <p class="petunjuk">Kelas Draft belum bisa diikuti mahasiswa.</p>

    <div class="bawah tengah">
      <button class="m-btn navy" style="padding:10px 90px">Simpan</button>
      <p style="margin-top:12px"><a href="{{ route('dosen.dashboard') }}" class="m-muted">Batal</a></p>
    </div>
  </form>
@endsection

@push('scripts')
  <script>
    // Saring mata kuliah sesuai program studi yang dipilih
    const prodi = document.getElementById('prodi'), mk = document.getElementById('mata_kuliah_id');
    function saring() {
      [...mk.options].forEach(o => { if (o.value) o.hidden = prodi.value && o.dataset.prodi !== prodi.value; });
      if (mk.selectedOptions[0]?.hidden) mk.value = '';
    }
    prodi.addEventListener('change', saring);
    mk.addEventListener('change', () => { const p = mk.selectedOptions[0]?.dataset.prodi; if (p) prodi.value = p; });
    saring();
  </script>
@endpush
