@extends('layouts.panel')

@section('title', 'Data Master')

@section('content')
  <div class="a-head" style="margin-top:0"><h1>Data Master</h1></div>
  <div class="a-lama">
  <div class="tabs">
    <a href="?tab=prodi" class="{{ $tab === 'prodi' ? 'active' : '' }}">Program Studi</a>
    <a href="?tab=matkul" class="{{ $tab === 'matkul' ? 'active' : '' }}">Mata Kuliah</a>
    <a href="?tab=tahun" class="{{ $tab === 'tahun' ? 'active' : '' }}">Tahun Ajaran</a>
  </div>

  @if ($tab === 'prodi')
    <div class="panel">
      <h2>Tambah Program Studi</h2>
      <form method="POST" action="{{ route('admin.master.prodi.store') }}" class="inline-form">
        @csrf
        <input name="kode" placeholder="Kode (mis. IF)" required>
        <input name="nama" placeholder="Nama program studi" required class="grow">
        <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah</button>
      </form>
    </div>
    <div class="panel">
      <table>
        <thead><tr><th>Kode</th><th>Nama</th><th>Mata Kuliah</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse ($prodi as $p)
            <tr>
              <td colspan="3">
                <form method="POST" action="{{ route('admin.master.prodi.update', $p) }}" class="inline-form">
                  @csrf @method('PUT')
                  <input name="kode" value="{{ $p->kode }}" required>
                  <input name="nama" value="{{ $p->nama }}" required class="grow">
                  <span class="muted">{{ $p->mata_kuliah_count }} MK</span>
                  <button class="btn btn-sm">Simpan</button>
                </form>
              </td>
              <td>
                <form method="POST" action="{{ route('admin.master.prodi.destroy', $p) }}" onsubmit="return confirm('Hapus {{ $p->nama }}? Mata kuliah di dalamnya ikut terhapus.')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="muted">Belum ada program studi.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  @endif

  @if ($tab === 'matkul')
    <div class="panel">
      <h2>Tambah Mata Kuliah</h2>
      @if ($prodi->isEmpty())
        <p class="muted">Tambahkan program studi terlebih dahulu.</p>
      @else
        <form method="POST" action="{{ route('admin.master.matkul.store') }}" class="inline-form">
          @csrf
          <select name="program_studi_id" required>
            @foreach ($prodi as $p) <option value="{{ $p->id }}">{{ $p->nama }}</option> @endforeach
          </select>
          <input name="kode" placeholder="Kode MK" required>
          <input name="nama" placeholder="Nama mata kuliah" required class="grow">
          <input name="sks" type="number" min="1" max="6" value="3" required style="width:80px" title="SKS">
          <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah</button>
        </form>
      @endif
    </div>
    <div class="panel">
      <table>
        <thead><tr><th>Program Studi / Kode / Nama / SKS</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse ($matkul as $m)
            <tr>
              <td>
                <form method="POST" action="{{ route('admin.master.matkul.update', $m) }}" class="inline-form">
                  @csrf @method('PUT')
                  <select name="program_studi_id">
                    @foreach ($prodi as $p) <option value="{{ $p->id }}" @selected($p->id === $m->program_studi_id)>{{ $p->nama }}</option> @endforeach
                  </select>
                  <input name="kode" value="{{ $m->kode }}" required>
                  <input name="nama" value="{{ $m->nama }}" required class="grow">
                  <input name="sks" type="number" min="1" max="6" value="{{ $m->sks }}" style="width:70px">
                  <button class="btn btn-sm">Simpan</button>
                </form>
              </td>
              <td>
                <form method="POST" action="{{ route('admin.master.matkul.destroy', $m) }}" onsubmit="return confirm('Hapus {{ $m->nama }}?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="2" class="muted">Belum ada mata kuliah.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  @endif

  @if ($tab === 'tahun')
    <div class="panel">
      <h2>Tambah Tahun Ajaran</h2>
      <form method="POST" action="{{ route('admin.master.tahun.store') }}" class="inline-form">
        @csrf
        <input name="nama" placeholder="Contoh: 2026/2027 Ganjil" required class="grow">
        <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah</button>
      </form>
    </div>
    <div class="panel">
      <table>
        <thead><tr><th>Tahun Ajaran</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse ($tahun as $t)
            <tr>
              <td>{{ $t->nama }}</td>
              <td><span class="status {{ $t->is_active ? 'on' : 'off' }}">{{ $t->is_active ? 'Aktif' : 'Tidak aktif' }}</span></td>
              <td><div class="aksi">
                @unless ($t->is_active)
                  <form method="POST" action="{{ route('admin.master.tahun.aktifkan', $t) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm"><i class="fa-solid fa-check"></i> Aktifkan</button>
                  </form>
                @endunless
                <form method="POST" action="{{ route('admin.master.tahun.destroy', $t) }}" onsubmit="return confirm('Hapus {{ $t->nama }}? Kelas di tahun ini ikut terhapus.')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                </form>
              </div></td>
            </tr>
          @empty
            <tr><td colspan="3" class="muted">Belum ada tahun ajaran.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  @endif
  </div>
@endsection
