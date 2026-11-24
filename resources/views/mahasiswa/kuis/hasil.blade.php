@extends('layouts.panel')

@section('title', 'Hasil ' . $kuis->judul)

@section('content')
  <div class="m-quiz">
    <h1>{{ $kuis->judul }}</h1>
    <div class="m-score">
      <b>{{ str_replace('.', ',', rtrim(rtrim(number_format($hasil->skor, 2, '.', ''), '0'), '.')) }} / 100</b>
      <p>{{ $hasil->benar }} dari {{ $hasil->jumlah_soal }} jawaban benar · dikerjakan {{ $hasil->created_at->translatedFormat('j F Y, H:i') }}</p>
    </div>

    <h3 style="color:var(--navy);margin-bottom:8px">Hasil Jawaban</h3>
    @unless ($tampilKunci)
      <p class="m-note" style="margin-bottom:20px">Kunci jawaban ditampilkan setelah sesi quiz ditutup (paling lambat {{ $kuis->selesai->translatedFormat('j F Y, H:i') }}).</p>
    @endunless
    @foreach ($soal as $i => $s)
      @php $pilih = $hasil->jawaban[$s->id] ?? null; $benar = $pilih === $s->jawaban; @endphp
      <div class="m-q">
        <div class="tanya"><span>{{ $i + 1 }}.</span> <span>{{ $s->pertanyaan }}</span></div>
        <div class="poin" style="color:{{ $benar ? '#16a34a' : '#dc2626' }}">{{ $benar ? 'Benar' : ($pilih ? 'Salah' : 'Tidak dijawab') }}</div>
        <div class="opsi">
          @foreach (['a', 'b', 'c', 'd', 'e'] as $o)
            @if ($s->{'opsi_' . $o})
              @php
                $kelasOpsi = '';
                if ($pilih === $o) { $kelasOpsi = $benar ? 'benar' : 'salah'; }
                elseif ($tampilKunci && $s->jawaban === $o) { $kelasOpsi = 'benar'; }
              @endphp
              <label class="{{ $kelasOpsi }}">
                <input type="radio" disabled @checked($pilih === $o)> <span>{{ $s->{'opsi_' . $o} }}
                  @if ($pilih === $o) <small>(jawaban kamu)</small> @endif
                  @if ($tampilKunci && $s->jawaban === $o) ✓ @endif</span>
              </label>
            @endif
          @endforeach
        </div>
      </div>
    @endforeach
    <div class="kirim"><a href="{{ route('mahasiswa.kelas.show', [$kuis->kelas, 'tab' => 'kuis']) }}" class="m-btn navy">Kembali ke Kelas</a></div>
  </div>
@endsection
