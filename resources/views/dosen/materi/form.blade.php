@extends('layouts.panel')

@php
  $ubah = $materi->exists;
  $judulHalaman = $ubah ? 'Ubah Materi' : 'Tambah Materi';
  $kembali = route('dosen.kelas.show', [$kelas, 'tab' => 'materi']);
  $lampiran = $materi->tipe === 'file' ? $materi->daftarLampiran() : [];
  $errorFile = $errors->first('files') ?: $errors->first('files.*') ?: $errors->first('file') ?: $errors->first('jumlah_file');
  $panel = 'min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5';
  $label = 'mb-1.5 block text-sm font-semibold text-[#173d6e]';
  $input = 'min-h-11 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2 text-base
            text-slate-900 focus:border-[#2b62a8] focus:outline-none focus:ring-2 focus:ring-[#acd6fb]
            aria-invalid:border-red-500';
  $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
  $tombol = "inline-flex min-h-11 items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold no-underline $fokus";
@endphp

@section('title', $judulHalaman)
@section('kembali', $kembali)

@section('content')
  <div class="mx-auto max-w-4xl space-y-4">
    <header class="flex items-start gap-3 rounded-xl border border-[#acd6fb] bg-[#dfeffe] p-4 sm:p-5">
      <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-white text-[#173d6e]">
        <i class="fa-solid fa-book-open" aria-hidden="true"></i>
      </span>
      <div class="min-w-0 [overflow-wrap:anywhere]">
        <h1 class="text-xl font-extrabold tracking-tight text-[#173d6e] sm:text-2xl">{{ $judulHalaman }}</h1>
        <p class="mt-1 text-sm text-slate-700">{{ $kelas->mataKuliah->nama }} · {{ $kelas->nama }}</p>
      </div>
    </header>

    <form method="POST" enctype="multipart/form-data"
          action="{{ $ubah ? route('dosen.materi.update', $materi) : route('dosen.materi.store', $kelas) }}"
          class="space-y-4">
      @csrf
      @if ($ubah)
        @method('PUT')
      @endif

      <section class="{{ $panel }} space-y-4" aria-labelledby="judul-informasi">
        <h2 id="judul-informasi" class="text-base font-bold text-[#173d6e]">Informasi Materi</h2>

        <div>
          <label for="judul" class="{{ $label }}">Judul Materi</label>
          <input type="text" id="judul" name="judul" value="{{ old('judul', $materi->judul) }}"
                 maxlength="150" class="{{ $input }}" @error('judul') aria-invalid="true" @enderror required>
          @error('judul')
            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="deskripsi" class="{{ $label }}">Deskripsi <span class="font-normal text-slate-600">(opsional)</span></label>
          <textarea id="deskripsi" name="deskripsi" rows="3" maxlength="3000"
                    class="{{ $input }} min-h-24 resize-y"
                    @error('deskripsi') aria-invalid="true" @enderror>{{ old('deskripsi', $materi->deskripsi) }}</textarea>
          @error('deskripsi')
            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
          @enderror
        </div>
      </section>

      <section class="{{ $panel }} space-y-4" aria-labelledby="judul-sumber">
        <div>
          <h2 id="judul-sumber" class="text-base font-bold text-[#173d6e]">Sumber Materi</h2>
          <p class="mt-1 text-sm text-slate-600">
            Pilih satu atau beberapa file, atau isi tautan. Jika keduanya diisi, LMS menyimpan file yang diunggah.
          </p>
        </div>

        <div class="grid items-start gap-4 sm:grid-cols-2">
          <div class="min-w-0">
            <label for="file" class="{{ $label }}">File Materi</label>
            <input type="hidden" name="jumlah_file">
            <input type="file" id="file" name="files[]" multiple
                   onchange="this.form.elements.jumlah_file.value = this.files.length"
                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt,.png,.jpg,.jpeg,.mp4"
                   aria-describedby="format-file{{ $errorFile ? ' error-file' : '' }}"
                   @if ($errorFile) aria-invalid="true" @endif
                   class="w-full min-w-0 rounded-lg border border-slate-300 bg-white p-1 text-sm text-slate-700
                          file:mr-2 file:rounded-md file:border-0 file:bg-[#dfeffe] file:px-3 file:py-2
                          file:font-semibold file:text-[#173d6e] hover:file:bg-[#acd6fb] {{ $fokus }}">
            <p id="format-file" class="mt-1 text-xs text-slate-600">
              PDF, Word, PowerPoint, Excel, ZIP, RAR, TXT, PNG, JPG, atau MP4. Maksimal 500 MB per file.
            </p>

            @if ($lampiran)
              <div class="mt-2 rounded-lg bg-[#f4f7fb] p-2 text-sm text-slate-700 [overflow-wrap:anywhere]">
                <p class="font-semibold">Lampiran saat ini</p>
                <ul class="ml-4 list-disc">
                  @foreach ($lampiran as $file)
                    <li>{{ $file['nama'] }}</li>
                  @endforeach
                </ul>
                <p class="mt-1">File baru mengganti semua lampiran di atas. Kosongkan file dan tautan untuk mempertahankannya.</p>
              </div>
            @endif

            @if ($errorFile)
              <p id="error-file" class="mt-1 text-sm text-red-700">{{ $errorFile }}</p>
            @endif
          </div>

          <div class="min-w-0">
            <label for="url" class="{{ $label }}">Tautan Materi</label>
            <input type="url" id="url" name="url" value="{{ old('url', $materi->url) }}"
                   placeholder="https://…" class="{{ $input }}" @error('url') aria-invalid="true" @enderror>
            @error('url')
              <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
            @enderror
          </div>
        </div>
      </section>

      <div class="flex flex-wrap justify-end gap-2">
        <a href="{{ $kembali }}" class="{{ $tombol }} border border-slate-300 bg-white text-[#173d6e] hover:bg-slate-50">
          Batal
        </a>
        <button type="submit" class="{{ $tombol }} bg-[#173d6e] text-white hover:bg-[#2b62a8]">
          Simpan Materi
        </button>
      </div>
    </form>
  </div>
@endsection