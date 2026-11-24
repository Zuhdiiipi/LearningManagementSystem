@extends('layouts.panel')

@section('title', $kuis->exists ? 'Ubah Quiz' : 'Tambah Quiz')
@section('kembali', route('dosen.kelas.show', [$kelas, 'tab' => 'kuis']))

@php
  // Soal awal: dari input lama (kalau gagal validasi), dari database (kalau ubah), atau 1 soal kosong
  $daftarSoal = old('soal', $soal->map(fn ($s) => $s->only(['pertanyaan', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e', 'jawaban']))->all());
  if (empty($daftarSoal)) { $daftarSoal = [[]]; }
@endphp

@section('content')
  <form method="POST" action="{{ $kuis->exists ? route('dosen.kuis.update', $kuis) : route('dosen.kuis.store', $kelas) }}" id="form-quiz">
    @csrf
    @if ($kuis->exists) @method('PUT') @endif

    <div class="m-title-row">
      <h1 class="m-title">Quiz</h1>
      <button class="m-btn navy" style="font-size:24px;padding:8px 30px">Simpan</button>
    </div>

    <div class="m-box form m-form" style="min-height:0">
      <label for="judul">Judul</label>
      <input type="text" id="judul" name="judul" value="{{ old('judul', $kuis->judul) }}" required>

      <div class="baris">
        <div>
          <label for="mulai" style="margin-top:22px">Jadwal Quiz <small class="m-muted">(dimulai saat klik Buka Sesi)</small></label>
          <input type="datetime-local" id="mulai" name="mulai" value="{{ old('mulai', $kuis->mulai?->format('Y-m-d\TH:i')) }}" required style="width:260px">
        </div>
        <div>
          <label for="durasi_menit" style="margin-top:22px">Batas Waktu (menit)</label>
          <input type="number" id="durasi_menit" name="durasi_menit" min="1" max="600" value="{{ old('durasi_menit', $kuis->durasi_menit) }}" required style="width:200px">
        </div>
      </div>

      @if ($terkunci)
        <div class="m-flash err" style="margin-top:24px">Sudah ada mahasiswa yang mengerjakan quiz ini, jadi soal tidak bisa diubah. Judul dan waktu masih bisa diubah.</div>
        @foreach ($soal as $i => $s)
          <div class="q-soal"><span class="no">{{ $i + 1 }}.</span><div style="background:#fff;padding:12px 16px">{{ $s->pertanyaan }}</div></div>
        @endforeach
      @else
        <p class="petunjuk" style="margin-top:20px">Klik bulatan di samping pilihan untuk menandai <b>jawaban yang benar</b>. Pilihan ketiga sampai kelima boleh dikosongkan.</p>
        <div id="daftar-soal">
          @foreach ($daftarSoal as $i => $s)
            <div class="q-soal" data-soal>
              <span class="no">{{ $loop->iteration }}.</span>
              <textarea name="soal[{{ $i }}][pertanyaan]" placeholder="Tulis pertanyaan..." required>{{ $s['pertanyaan'] ?? '' }}</textarea>
              <button type="button" class="hapus-soal" title="Hapus soal" onclick="hapusSoal(this)">✕</button>
              <div class="q-opsi">
                @foreach (['a', 'b', 'c', 'd', 'e'] as $o)
                  <div>
                    <input type="radio" name="soal[{{ $i }}][jawaban]" value="{{ $o }}" @checked(($s['jawaban'] ?? '') === $o) title="Tandai sebagai jawaban benar" @required($o === 'a')>
                    <input type="text" name="soal[{{ $i }}][opsi_{{ $o }}]" value="{{ $s['opsi_' . $o] ?? '' }}" placeholder="Pilihan {{ strtoupper($o) }}" @required(in_array($o, ['a', 'b']))>
                  </div>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
        <div class="q-tambah"><button type="button" class="m-btn light" onclick="tambahSoal()"><i class="fa-solid fa-plus"></i> Tambah Soal</button></div>
      @endif
    </div>
  </form>
@endsection

@push('scripts')
  <script>
    // Menambah dan menghapus soal tanpa memuat ulang halaman
    let urut = {{ count($daftarSoal) }};
    function nomori() {
      document.querySelectorAll('[data-soal] .no').forEach((el, i) => el.textContent = (i + 1) + '.');
    }
    function tambahSoal() {
      const asal = document.querySelector('[data-soal]');
      const baru = asal.cloneNode(true);
      baru.querySelectorAll('textarea, input[type=text]').forEach(el => el.value = '');
      baru.querySelectorAll('input[type=radio]').forEach(el => el.checked = false);
      baru.querySelectorAll('[name]').forEach(el => el.name = el.name.replace(/soal\[\d+\]/, 'soal[' + urut + ']'));
      urut++;
      document.getElementById('daftar-soal').appendChild(baru);
      nomori();
      baru.querySelector('textarea').focus();
    }
    function hapusSoal(btn) {
      if (document.querySelectorAll('[data-soal]').length === 1) { alert('Quiz minimal punya satu soal.'); return; }
      btn.closest('[data-soal]').remove();
      nomori();
    }
  </script>
@endpush
