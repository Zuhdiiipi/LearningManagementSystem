@extends('layouts.panel')

@php
  $ubah = $kelas->exists;
  $judul = $ubah ? 'Ubah Kelas' : 'Buat Kelas';
  $mataKuliahId = old('mata_kuliah_id', $kelas->mata_kuliah_id);
  $prodiId = $matkul->firstWhere('id', $mataKuliahId)?->program_studi_id;

  $label = 'mb-1.5 block text-sm font-semibold text-[#173d6e]';
  $input = 'min-h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2 text-base
            text-slate-900 focus:border-[#2b62a8] focus:outline-none focus:ring-2 focus:ring-[#acd6fb]';
  $opsi = 'flex min-h-11 cursor-pointer items-center gap-2 rounded-lg border border-slate-300
           px-3 py-2 text-sm font-medium text-[#173d6e] hover:bg-slate-50
           has-checked:border-[#2b62a8] has-checked:bg-[#dfeffe]';
  $tombol = 'inline-flex min-h-11 items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold
             no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
@endphp

@section('title', $judul)

@section('content')
  <div class="mx-auto max-w-4xl space-y-4">
    <header class="flex items-start gap-3 rounded-xl border border-[#acd6fb] bg-[#dfeffe] p-4 sm:p-5">
      <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-white text-[#173d6e]">
        <i class="fa-solid fa-chalkboard-user" aria-hidden="true"></i>
      </span>
      <div class="min-w-0">
        <h1 class="text-xl font-extrabold tracking-tight text-[#173d6e] sm:text-2xl">{{ $judul }}</h1>
        <p class="mt-1 text-sm text-slate-700 [overflow-wrap:anywhere]">
          {{ $ubah ? $kelas->nama : 'Pilih mata kuliah, tahun akademik, dan status kelas.' }}
        </p>
      </div>
    </header>

    <form method="POST"
          action="{{ $ubah ? route('dosen.kelas.update', $kelas) : route('dosen.kelas.store') }}"
          class="rounded-xl border border-slate-200 bg-white shadow-sm">
      @csrf
      @if ($ubah)
        @method('PUT')
      @endif

      <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5">
        <div class="min-w-0 sm:col-span-2">
          <label for="nama" class="{{ $label }}">Nama Kelas</label>
          <input id="nama" name="nama" type="text" value="{{ old('nama', $kelas->nama) }}"
                 placeholder="Contoh: Informatika B 23" maxlength="100" class="{{ $input }}" required>
        </div>

        <div class="min-w-0">
          <label for="prodi" class="{{ $label }}">Program Studi</label>
          <select id="prodi" class="{{ $input }}" aria-describedby="petunjuk-prodi">
            <option value="">Semua program studi</option>
            @foreach ($prodi as $p)
              <option value="{{ $p->id }}" @selected($prodiId == $p->id)>{{ $p->nama }}</option>
            @endforeach
          </select>
          <p id="petunjuk-prodi" class="mt-1 text-xs text-slate-600">
            Saring pilihan mata kuliah berdasarkan program studi.
          </p>
        </div>

        <div class="min-w-0">
          <label for="mata_kuliah_id" class="{{ $label }}">Mata Kuliah</label>
          <select id="mata_kuliah_id" name="mata_kuliah_id" class="{{ $input }}" required>
            <option value="">Pilih mata kuliah</option>
            @foreach ($matkul as $m)
              <option value="{{ $m->id }}" data-prodi="{{ $m->program_studi_id }}"
                      @selected($mataKuliahId == $m->id)>{{ $m->kode }} — {{ $m->nama }}</option>
            @endforeach
          </select>
        </div>

        <div class="min-w-0">
          <label for="tahun_ajaran_id" class="{{ $label }}">Tahun Akademik</label>
          <select id="tahun_ajaran_id" name="tahun_ajaran_id" class="{{ $input }}" required>
            @forelse ($tahun as $t)
              <option value="{{ $t->id }}" @selected(old('tahun_ajaran_id', $kelas->tahun_ajaran_id) == $t->id)>
                {{ $t->nama }}{{ $t->is_active ? ' (aktif)' : '' }}
              </option>
            @empty
              <option value="">Belum ada tahun akademik</option>
            @endforelse
          </select>
        </div>

        <fieldset class="min-w-0">
          <legend class="{{ $label }}">Semester</legend>
          <div class="grid grid-cols-2 gap-2">
            @foreach (['ganjil' => 'Ganjil', 'genap' => 'Genap'] as $nilai => $teks)
              <label class="{{ $opsi }}">
                <input type="radio" name="semester" value="{{ $nilai }}" class="accent-[#173d6e]"
                       @checked(old('semester', $kelas->semester) === $nilai) required>
                {{ $teks }}
              </label>
            @endforeach
          </div>
        </fieldset>

        <fieldset class="min-w-0 sm:col-span-2" aria-describedby="petunjuk-status">
          <legend class="{{ $label }}">Status Kelas</legend>
          <div class="grid grid-cols-2 gap-2 sm:max-w-sm">
            @foreach (['aktif' => 'Aktif', 'draft' => 'Draf'] as $nilai => $teks)
              <label class="{{ $opsi }}">
                <input type="radio" name="status" value="{{ $nilai }}" class="accent-[#173d6e]"
                       @checked(old('status', $kelas->status) === $nilai) required>
                {{ $teks }}
              </label>
            @endforeach
          </div>
          <p id="petunjuk-status" class="mt-1.5 text-sm text-slate-600">
            Mahasiswa baru bisa bergabung saat kelas berstatus aktif.
          </p>
        </fieldset>
      </div>

      <div class="flex flex-wrap justify-end gap-2 border-t border-slate-100 px-4 py-3 sm:px-5">
        <a href="{{ route('dosen.dashboard') }}"
           class="{{ $tombol }} border border-slate-200 text-[#173d6e] hover:bg-slate-50">
          Batal
        </a>
        <button type="submit" class="{{ $tombol }} bg-[#173d6e] text-white hover:bg-[#2b62a8]">
          {{ $ubah ? 'Simpan Perubahan' : 'Simpan Kelas' }}
        </button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
  <script>
    (() => {
      const prodi = document.getElementById('prodi');
      const mataKuliah = document.getElementById('mata_kuliah_id');
      const pilihan = [...mataKuliah.querySelectorAll('option[data-prodi]')];

      function saring() {
        pilihan.forEach(opsi => {
          const tersembunyi = prodi.value !== '' && opsi.dataset.prodi !== prodi.value;
          opsi.hidden = tersembunyi;
          opsi.disabled = tersembunyi;
        });

        if (mataKuliah.selectedOptions[0]?.disabled) mataKuliah.value = '';

        mataKuliah.options[0].textContent = pilihan.some(opsi => !opsi.disabled)
          ? 'Pilih mata kuliah'
          : 'Belum ada mata kuliah';
      }

      prodi.addEventListener('change', saring);
      mataKuliah.addEventListener('change', () => {
        const programStudi = mataKuliah.selectedOptions[0]?.dataset.prodi;
        if (programStudi) prodi.value = programStudi;
        saring();
      });

      saring();
    })();
  </script>
@endpush