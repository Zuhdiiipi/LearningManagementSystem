@extends('layouts.panel')

@section('title', $kuis->judul)

@php $poin = $soal->count() ? round(100 / $soal->count(), 2) : 0; @endphp

@section('content')
  <form method="POST" action="{{ route('mahasiswa.kuis.submit', $kuis) }}" id="form-kuis" class="m-quiz"
        onsubmit="return this.dataset.auto === '1' || confirm('Kirim jawaban sekarang? Jawaban tidak bisa diubah lagi.')">
    @csrf
    <div class="timer" id="timer">--:--:--</div>
    <h1>{{ $kuis->judul }}</h1>
    @if ($kuis->deskripsi) <p class="desk">{{ $kuis->deskripsi }}</p> @endif

    @foreach ($soal as $i => $s)
      <div class="m-q">
        <div class="tanya"><span>{{ $i + 1 }}.</span> <span>{{ $s->pertanyaan }}</span></div>
        <div class="poin">{{ str_replace('.', ',', $poin) }} point</div>
        <div class="opsi">
          @foreach (['a', 'b', 'c', 'd', 'e'] as $o)
            @if ($s->{'opsi_' . $o})
              <label><input type="radio" name="jawaban[{{ $s->id }}]" value="{{ $o }}"> <span>{{ $s->{'opsi_' . $o} }}</span></label>
            @endif
          @endforeach
        </div>
      </div>
    @endforeach

    <div class="kirim"><button class="m-btn navy" style="padding:14px 60px;font-size:22px">Kirim Jawaban</button></div>
    <p class="m-note" style="text-align:center">Jawaban dikirim otomatis saat waktu habis.</p>
  </form>
@endsection

@push('scripts')
  <script>
    // Hitung mundur format jam:menit:detik; saat habis, jawaban dikirim otomatis
    let sisa = {{ $sisaDetik }};
    const el = document.getElementById('timer');
    const form = document.getElementById('form-kuis');
    const dua = n => String(n).padStart(2, '0');
    function tick() {
      if (sisa <= 0) { el.classList.add('habis'); form.dataset.auto = '1'; form.submit(); return; }
      el.textContent = dua(Math.floor(sisa / 3600)) + ':' + dua(Math.floor(sisa % 3600 / 60)) + ':' + dua(sisa % 60);
      if (sisa <= 60) el.classList.add('habis');
      sisa--;
      setTimeout(tick, 1000);
    }
    tick();
  </script>
@endpush
