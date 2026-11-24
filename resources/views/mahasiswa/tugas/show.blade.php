@extends('layouts.panel', ['menuAktif' => 'tugas'])
@section('title', $tugas->judul)
@section('kembali', route('mahasiswa.kelas.show', [$kelas, 'tab' => 'tugas']))
@php use App\Support\Waktu; $bisaUbah = $kumpul?->nilai === null; @endphp
@section('content')
  <div class="m-detail-head">
    <span class="m-icon"><i class="fa-solid {{ $tugas->jenis === 'tugas' ? 'fa-file-code' : 'fa-clipboard-list' }}"></i></span>
    <div>
      <h1>{{ $tugas->judul }}</h1>
      <p>{{ Waktu::singkat($tugas->created_at) }} &nbsp;|&nbsp; Batas: {{ $tugas->deadline->translatedFormat('l, j F Y') }}, {{ $tugas->deadline->format('H : i') }}
        @if ($tugas->sudahLewat()) <b style="color:#dc2626">(batas waktu sudah lewat)</b> @else ({{ $tugas->deadline->diffForHumans() }}) @endif
      </p>
    </div>
  </div>

  <div class="m-detail {{ $kumpul ? 'dua' : '' }}">
    <div class="m-paper">
      <div class="isi">{{ $tugas->instruksi }}</div>
      @if ($tugas->file_path)
        <div>@include('mahasiswa._file', ['judul' => $tugas->file_name, 'url' => route('file.tugas', $tugas), 'namaFile' => $tugas->file_name])</div>
      @endif

      {{-- Belum mengumpulkan: tombol Kumpul Tugas membuka pilihan file --}}
      @unless ($kumpul)
        <div class="bawah">
          @if ($tugas->sudahLewat())
            <p class="m-telat"><i class="fa-solid fa-triangle-exclamation"></i> Batas waktu sudah lewat. Kamu masih bisa mengumpulkan, tetapi akan ditandai <b>Terlambat</b>.</p>
          @endif
            <form method="POST" action="{{ route('mahasiswa.tugas.kumpul', $tugas) }}" enctype="multipart/form-data" id="form-kumpul">
              @csrf
              <input type="file" name="file" id="file-jawaban" hidden accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt,.png,.jpg,.jpeg"
                     onchange="if (this.files.length) this.form.submit()">
              <button type="button" class="m-btn navy" onclick="document.getElementById('file-jawaban').click()">{{ $tugas->sudahLewat() ? 'Kumpul Terlambat' : 'Kumpul ' . ucfirst($tugas->jenis) }}</button>
              <p class="m-note">Format: PDF, Word, PowerPoint, Excel, ZIP/RAR, gambar. Maksimal 20 MB.</p>
            </form>
        </div>
      @endunless
    </div>

    {{-- Sudah mengumpulkan: kartu file jawaban + Batal Kirim + Selesai --}}
    @if ($kumpul)
      <div class="m-side-col">
        <div class="m-side-card">
          <h4>{{ ucfirst($tugas->jenis) }}</h4>
          @include('mahasiswa._file', ['judul' => $kumpul->file_name, 'url' => route('file.pengumpulan', $kumpul), 'namaFile' => $kumpul->file_name])
          <p class="m-note">Dikirim {{ $kumpul->waktuKumpul()->translatedFormat('l, j F Y') }}, {{ $kumpul->waktuKumpul()->format('H : i') }}</p>
          @if ($kumpul->terlambat())
            <p class="m-telat"><i class="fa-solid fa-clock"></i> Terlambat {{ $kumpul->lamaTerlambat() }}</p>
          @else
            <p class="m-tepat"><i class="fa-solid fa-circle-check"></i> Tepat waktu</p>
          @endif

          @if ($kumpul->nilai !== null)
            <div class="m-nilai-box">
              <b>Nilai: {{ $kumpul->nilai }} / 100</b>
              @if ($kumpul->feedback)<br>Catatan dosen: {{ $kumpul->feedback }}@endif
            </div>
          @endif

          <div class="bawah">
            @if ($bisaUbah)
              <form method="POST" action="{{ route('mahasiswa.tugas.batal', $tugas) }}" onsubmit="return confirm('Batalkan pengiriman? File jawaban akan dihapus.')">
                @csrf @method('DELETE')
                <button class="m-btn outline">Batal Kirim</button>
              </form>
            @endif
          </div>
        </div>
        <div class="selesai">
          <a href="{{ route('mahasiswa.kelas.show', [$kelas, 'tab' => 'tugas']) }}" class="m-btn green">Selesai</a>
        </div>
      </div>
    @endif
  </div>
@endsection
