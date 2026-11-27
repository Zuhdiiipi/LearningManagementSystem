@if ($excel === null)
  <p class="rounded-lg bg-slate-50 p-3 text-sm text-slate-700">
    File Excel ini belum bisa dipratinjau.
  </p>
@else
  @php $pilihan = $excel['pilihan']; @endphp

  @if ($excel['lembar'])
    <nav aria-label="Lembar Excel" class="mb-3 flex flex-wrap gap-2">
      @foreach ($excel['lembar'] as $indeks => $nama)
        <a href="{{ $tautanExcel(['lembar' => $indeks]) }}"
           class="{{ $tombol }} {{ ($pilihan['indeks'] ?? null) === $indeks ? $utama : $sekunder }}"
           @if (($pilihan['indeks'] ?? null) === $indeks) aria-current="page" @endif>
          {{ $nama }}
        </a>
      @endforeach
    </nav>
  @endif

  @if ($pilihan && $pilihan['jumlahKolom'] > 0)
    <div role="region" aria-label="Isi lembar Excel" tabindex="0"
         class="overflow-x-auto rounded-lg border border-slate-200
                focus-visible:outline-2 focus-visible:outline-[#2b62a8]">
      <table class="w-full border-collapse text-left text-sm text-slate-800">
        <thead class="bg-[#dfeffe] text-[#173d6e]">
          <tr>
            <th scope="col" class="border border-slate-200 px-3 py-2">Baris</th>
            @for ($kolom = 0; $kolom < $pilihan['jumlahKolom']; $kolom++)
              <th scope="col" class="border border-slate-200 px-3 py-2 whitespace-nowrap">
                {{ $pilihan['awalKolom'] + $kolom + 1 }}
              </th>
            @endfor
          </tr>
        </thead>

        <tbody>
          @foreach ($pilihan['baris'] as $nomor => $isi)
            <tr class="odd:bg-white even:bg-slate-50">
              <th scope="row" class="border border-slate-200 px-3 py-2 text-slate-600">
                {{ $nomor }}
              </th>
              @for ($kolom = 0; $kolom < $pilihan['jumlahKolom']; $kolom++)
                <td class="border border-slate-200 px-3 py-2 align-top
                           whitespace-pre-wrap [overflow-wrap:anywhere]">{{ $isi[$kolom] ?? '' }}</td>
              @endfor
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <p class="rounded-lg bg-slate-50 p-3 text-sm text-slate-700">
      {{ $pilihan ? 'Bagian lembar ini kosong.' : 'Pilih lembar Excel untuk melihat isinya.' }}
    </p>
  @endif

  @if ($pilihan)
    @php
      $navigasiExcel = [
        ['Baris sebelumnya', $pilihan['halaman'] > 1, $pilihan['halaman'] - 1, $pilihan['kolom']],
        ['Baris berikutnya', $pilihan['barisLain'], $pilihan['halaman'] + 1, $pilihan['kolom']],
        ['Kolom sebelumnya', $pilihan['kolom'] > 1, $pilihan['halaman'], $pilihan['kolom'] - 1],
        ['Kolom berikutnya', $pilihan['kolomLain'], $pilihan['halaman'], $pilihan['kolom'] + 1],
      ];
    @endphp

    <nav aria-label="Navigasi isi Excel" class="mt-3 flex flex-wrap gap-2">
      @foreach ($navigasiExcel as [$label, $tersedia, $halaman, $kolom])
        @if ($tersedia)
          <a href="{{ $tautanExcel([
                'lembar' => $pilihan['indeks'],
                'halaman' => $halaman,
                'kolom' => $kolom,
              ]) }}"
             class="{{ $tombol }} {{ $sekunder }}">
            {{ $label }}
          </a>
        @endif
      @endforeach
    </nav>
  @endif
@endif