@extends('layouts.panel')

@section('title', 'Ubah Kelas')

@section('content')
  @php
    $pilihanKelas = [
      'mata_kuliah_id' => ['Mata Kuliah', $matkul->mapWithKeys(fn ($m) => [$m->id => $m->kode . ' — ' . $m->nama])],
      'dosen_id' => ['Dosen Pengampu', $dosen->pluck('name', 'id')],
      'tahun_ajaran_id' => ['Tahun Akademik', $tahun->pluck('nama', 'id')],
    ];

    $pengaturan = [
      'semester' => ['Semester', ['ganjil' => 'Ganjil', 'genap' => 'Genap']],
      'status' => ['Status Kelas', ['aktif' => 'Aktif', 'draft' => 'Draft']],
    ];

    $input = 'mt-1.5 block w-full rounded-lg border bg-white px-3 py-2.5 text-sm text-slate-900
              focus:border-[#2b62a8] focus:outline-none focus:ring-2 focus:ring-[#acd6fb]';
  @endphp

  <h1 class="mb-5 text-2xl font-bold tracking-tight text-[#173d6e] sm:text-3xl">Ubah Kelas</h1>

  <form method="POST" action="{{ route('admin.kelas.update', $kelas) }}"
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    @csrf
    @method('PUT')

    <div class="grid xl:grid-cols-[minmax(0,1fr)_20rem]">
      <section class="min-w-0 p-4 sm:p-6" aria-labelledby="data-kelas">
        <h2 id="data-kelas" class="text-base font-bold text-[#173d6e]">Data Kelas</h2>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
          <div class="min-w-0">
            <label for="nama" class="block text-sm font-semibold text-[#173d6e]">Nama Kelas</label>
            <input id="nama" name="nama" type="text" value="{{ old('nama', $kelas->nama) }}"
                   required maxlength="100"
                   @error('nama') aria-invalid="true" aria-describedby="nama-error" @enderror
                   @class([$input, 'border-red-400' => $errors->has('nama'), 'border-slate-200' => ! $errors->has('nama')])>

            @error('nama')
              <p id="nama-error" class="mt-1 text-xs text-red-700">{{ $message }}</p>
            @enderror
          </div>

          @foreach ($pilihanKelas as $field => [$label, $opsi])
            <div class="min-w-0">
              <label for="{{ $field }}" class="block text-sm font-semibold text-[#173d6e]">{{ $label }}</label>
              <select id="{{ $field }}" name="{{ $field }}" required
                      @error($field) aria-invalid="true" aria-describedby="{{ $field }}-error" @enderror
                      @class([$input, 'border-red-400' => $errors->has($field), 'border-slate-200' => ! $errors->has($field)])>
                <option value="">Pilih {{ $label }}</option>
                @foreach ($opsi as $id => $teks)
                  <option value="{{ $id }}" @selected((string) old($field, $kelas->{$field}) === (string) $id)>
                    {{ $teks }}
                  </option>
                @endforeach
              </select>

              @error($field)
                <p id="{{ $field }}-error" class="mt-1 text-xs text-red-700">{{ $message }}</p>
              @enderror
            </div>
          @endforeach
        </div>
      </section>

      <section class="min-w-0 border-t border-[#acd6fb] bg-[#f1f7ff] p-4 sm:p-6 xl:border-t-0 xl:border-l"
               aria-labelledby="pengaturan-kelas">
        <h2 id="pengaturan-kelas" class="text-base font-bold text-[#173d6e]">Pengaturan Kelas</h2>

        @foreach ($pengaturan as $field => [$label, $opsi])
          <fieldset class="mt-4 min-w-0" @error($field) aria-describedby="{{ $field }}-error" @enderror>
            <legend class="text-sm font-semibold text-[#173d6e]">{{ $label }}</legend>

            <div class="mt-2 grid grid-cols-2 gap-2">
              @foreach ($opsi as $nilai => $teks)
                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200
                              bg-white px-2.5 py-2 text-sm font-medium text-[#173d6e]
                              has-checked:border-[#2b62a8] has-checked:bg-[#dfeffe]">
                  <input type="radio" name="{{ $field }}" value="{{ $nilai }}" required
                         class="size-4 shrink-0 accent-[#173d6e]"
                         @checked(old($field, $kelas->{$field}) === $nilai)>
                  {{ $teks }}
                </label>
              @endforeach
            </div>

            @error($field)
              <p id="{{ $field }}-error" class="mt-1 text-xs text-red-700">{{ $message }}</p>
            @enderror
          </fieldset>
        @endforeach
      </section>
    </div>

    <div class="flex flex-wrap justify-end gap-2 border-t border-slate-100 px-4 py-4 sm:px-6">
      <a href="{{ route('admin.kelas.index') }}"
         class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-[#173d6e] hover:bg-[#dfeffe]">
        Batal
      </a>
      <button type="submit"
              class="rounded-lg bg-[#173d6e] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#2b62a8]">
        Simpan
      </button>
    </div>
  </form>
@endsection