@extends('layouts.panel')

@section('title', 'Data Master')

@section('content')
  @php
    $tabMaster = [
      'prodi' => 'Program Studi',
      'matkul' => 'Mata Kuliah',
      'tahun' => 'Tahun Ajaran',
    ];

    $tabAktif = is_string($tab) && isset($tabMaster[$tab]) ? $tab : 'prodi';
    $labelTab = $tabMaster[$tabAktif];
    $ruteMaster = 'admin.master.' . $tabAktif;

    [$daftar, $kolom, $dampakHapus] = match ($tabAktif) {
      'prodi' => [
        $prodi,
        ['Kode', 'Nama Program Studi', 'Mata Kuliah'],
        'Mata kuliah dan kelas terkait juga akan terhapus.',
      ],
      'matkul' => [
        $matkul,
        ['Program Studi', 'Kode', 'Nama Mata Kuliah', 'SKS'],
        'Kelas terkait juga akan terhapus.',
      ],
      'tahun' => [
        $tahun,
        ['Tahun Ajaran', 'Status'],
        'Kelas pada tahun ajaran ini juga akan terhapus.',
      ],
    };

    $input = 'w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900
              focus:border-[#2b62a8] focus:outline-none focus:ring-2 focus:ring-[#acd6fb]';
    $labelInput = 'mb-1.5 block text-sm font-semibold text-[#173d6e]';

    $utama = 'inline-flex items-center justify-center gap-2 rounded-lg bg-[#173d6e] px-3 py-2
              text-sm font-semibold text-white hover:bg-[#2b62a8]';
    $sekunder = 'inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2
                 text-sm font-semibold text-[#173d6e] hover:bg-[#dfeffe]';
    $hapus = 'inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-white
              px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50';
  @endphp

  <h1 class="mb-5 text-2xl font-bold tracking-tight text-[#173d6e] sm:text-3xl">
    Data Master
  </h1>

  <nav aria-label="Kategori Data Master"
       class="mb-5 flex w-fit max-w-full flex-wrap gap-1 rounded-xl border border-slate-200 bg-white p-1">
    @foreach ($tabMaster as $idTab => $namaTab)
      <a href="{{ route('admin.master.index', ['tab' => $idTab]) }}"
         @if ($tabAktif === $idTab) aria-current="page" @endif
         @class([
           'rounded-lg px-3 py-2 text-sm font-semibold',
           'bg-[#173d6e] text-white' => $tabAktif === $idTab,
           'text-[#4d6a92] hover:bg-[#dfeffe] hover:text-[#173d6e]' => $tabAktif !== $idTab,
         ])>
        {{ $namaTab }}
      </a>
    @endforeach
  </nav>

  <section class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
           aria-labelledby="tambah-master">
    <div class="border-b border-[#acd6fb] bg-[#f1f7ff] px-4 py-3 sm:px-5">
      <h2 id="tambah-master" class="text-base font-bold text-[#173d6e]">
        Tambah {{ $labelTab }}
      </h2>
    </div>

    @if ($tabAktif === 'matkul' && $prodi->isEmpty())
      <p class="p-4 text-sm text-slate-600 sm:p-5">
        Tambahkan
        <a href="{{ route('admin.master.index', ['tab' => 'prodi']) }}"
           class="font-semibold text-[#2b62a8] underline">program studi</a>
        terlebih dahulu.
      </p>
    @else
      <form method="POST" action="{{ route($ruteMaster . '.store') }}"
            class="flex flex-wrap items-end gap-3 p-4 sm:p-5">
        @csrf

        @if ($tabAktif === 'matkul')
          <div class="w-full min-w-0 sm:w-48">
            <label for="master-prodi" class="{{ $labelInput }}">Program Studi</label>
            <select id="master-prodi" name="program_studi_id" class="{{ $input }}" required>
              @foreach ($prodi as $p)
                <option value="{{ $p->id }}">{{ $p->nama }}</option>
              @endforeach
            </select>
          </div>
        @endif

        @unless ($tabAktif === 'tahun')
          <div class="w-full sm:w-28">
            <label for="master-kode" class="{{ $labelInput }}">Kode</label>
            <input id="master-kode" name="kode" type="text" maxlength="20"
                   placeholder="{{ $tabAktif === 'prodi' ? 'Mis. IF' : 'Mis. IF101' }}"
                   class="{{ $input }}" required>
          </div>
        @endunless

        <div class="w-full min-w-0 sm:w-auto sm:min-w-48 sm:flex-1">
          <label for="master-nama" class="{{ $labelInput }}">
            {{ $tabAktif === 'tahun' ? 'Tahun Ajaran' : 'Nama ' . $labelTab }}
          </label>
          <input id="master-nama" name="nama" type="text" class="{{ $input }}"
                 maxlength="{{ $tabAktif === 'tahun' ? 50 : 100 }}"
                 placeholder="{{ $tabAktif === 'tahun' ? 'Mis. 2026/2027 Ganjil' : 'Masukkan nama' }}"
                 required>
        </div>

        @if ($tabAktif === 'matkul')
          <div class="w-20">
            <label for="master-sks" class="{{ $labelInput }}">SKS</label>
            <input id="master-sks" name="sks" type="number" min="1" max="6" value="3"
                   class="{{ $input }}" required>
          </div>
        @endif

        <button type="submit" class="{{ $utama }}">
          <i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah
        </button>
      </form>
    @endif
  </section>

  <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
           aria-labelledby="daftar-master">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-4 py-3 sm:px-5">
      <h2 id="daftar-master" class="text-base font-bold text-[#173d6e]">
        Daftar {{ $labelTab }}
      </h2>
      <span class="text-sm text-slate-600">
        {{ number_format($daftar->count(), 0, ',', '.') }} data
      </span>
    </div>

    <div class="overflow-x-auto" tabindex="0" role="region" aria-labelledby="daftar-master">
      <table @class([
        'w-full border-collapse text-left text-sm',
        'min-w-[52rem]' => $tabAktif === 'matkul',
        'min-w-[36rem]' => $tabAktif !== 'matkul',
      ])>
        <thead class="bg-[#dfeffe] text-[#173d6e]">
          <tr>
            @foreach ($kolom as $judulKolom)
              <th scope="col" class="px-3 py-3 font-semibold">{{ $judulKolom }}</th>
            @endforeach
            <th scope="col" class="px-3 py-3 text-right font-semibold">Aksi</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          @forelse ($daftar as $item)
            @php $formEdit = 'ubah-' . $tabAktif . '-' . $item->id; @endphp

            <tr class="hover:bg-slate-50">
              @if ($tabAktif === 'tahun')
                <th scope="row" class="px-3 py-3 font-semibold text-[#173d6e]">
                  {{ $item->nama }}
                </th>
                <td class="px-3 py-3">
                  <span @class([
                    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                    'bg-emerald-50 text-emerald-800' => $item->is_active,
                    'bg-slate-100 text-slate-600' => ! $item->is_active,
                  ])>{{ $item->is_active ? 'Aktif' : 'Tidak aktif' }}</span>
                </td>
              @else
                @if ($tabAktif === 'matkul')
                  <td class="w-48 px-3 py-3">
                    <select name="program_studi_id" form="{{ $formEdit }}" class="{{ $input }}"
                            aria-label="Program studi untuk {{ $item->nama }}" required>
                      @foreach ($prodi as $p)
                        <option value="{{ $p->id }}"
                                @selected((string) $p->id === (string) $item->program_studi_id)>
                          {{ $p->nama }}
                        </option>
                      @endforeach
                    </select>
                  </td>
                @endif

                <td class="w-28 px-3 py-3">
                  <input name="kode" form="{{ $formEdit }}" type="text" value="{{ $item->kode }}"
                         maxlength="20" class="{{ $input }}"
                         aria-label="Kode untuk {{ $item->nama }}" required>
                </td>

                <td class="min-w-40 px-3 py-3">
                  <input name="nama" form="{{ $formEdit }}" type="text" value="{{ $item->nama }}"
                         maxlength="100" class="{{ $input }}"
                         aria-label="Nama untuk {{ $item->nama }}" required>
                </td>

                @if ($tabAktif === 'prodi')
                  <td class="px-3 py-3 text-slate-600">{{ $item->mata_kuliah_count }} MK</td>
                @else
                  <td class="w-24 px-3 py-3">
                    <input name="sks" form="{{ $formEdit }}" type="number" min="1" max="6"
                           value="{{ $item->sks }}" class="{{ $input }}"
                           aria-label="SKS untuk {{ $item->nama }}" required>
                  </td>
                @endif
              @endif

              <td class="px-3 py-3">
                <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                  @if ($tabAktif !== 'tahun')
                    <form id="{{ $formEdit }}" method="POST"
                          action="{{ route($ruteMaster . '.update', $item) }}">
                      @csrf
                      @method('PUT')
                      <button type="submit" class="{{ $sekunder }}">Simpan</button>
                    </form>
                  @elseif (! $item->is_active)
                    <form method="POST" action="{{ route('admin.master.tahun.aktifkan', $item) }}">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="{{ $sekunder }}">Aktifkan</button>
                    </form>
                  @endif

                  <form method="POST" action="{{ route($ruteMaster . '.destroy', $item) }}"
                        data-konfirmasi-judul="Hapus {{ $labelTab }}"
                        data-konfirmasi="Hapus {{ $item->nama }}? {{ $dampakHapus }}">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="{{ $hapus }}" aria-label="Hapus {{ $item->nama }}">
                      <i class="fa-solid fa-trash" aria-hidden="true"></i> Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="{{ count($kolom) + 1 }}" class="px-4 py-8 text-center text-slate-600">
                Belum ada {{ mb_strtolower($labelTab) }}.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection