{{-- Banner sambutan di dashboard. Variabel: $label, $ikon, $sapaan, $judul, $sub --}}
<section class="m-banner">
  <div class="isi">
    <span class="pill"><i class="fa-solid {{ $ikon }}"></i> {{ $label }}</span>
    <p>Halo, {{ $sapaan }}</p>
    <h1>{{ $judul }}</h1>
    <div>{{ $sub }}</div>
  </div>
  <div class="gambar"></div>
</section>
