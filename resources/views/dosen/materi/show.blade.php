@extends('layouts.panel')

@php
    $berkas = $materi->tipe === 'file';
    $lampiran = $berkas ? $materi->daftarLampiran() : [];
    $adaTautan = filled($materi->url);

    $jenisSumber = $berkas
        ? ($adaTautan ? 'File dan tautan materi' : 'File materi')
        : 'Tautan materi';

    $panel = 'min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5';
    $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2b62a8]';
    $tombol = "inline-flex min-h-10 items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold no-underline $fokus";
@endphp

@section('title', $materi->judul)
@section('kembali', route('dosen.kelas.show', [$kelas, 'tab' => 'materi']))

@section('content')
    <div class="space-y-4">
        <header class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-[#acd6fb] bg-[#dfeffe] p-4 sm:p-5">
            <div class="flex min-w-0 items-start gap-3">
                <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-white text-[#173d6e]">
                    <i class="fa-solid {{ $berkas ? 'fa-file-lines' : 'fa-link' }}" aria-hidden="true"></i>
                </span>

                <div class="min-w-0 [overflow-wrap:anywhere]">
                    <h1 class="text-xl font-extrabold tracking-tight text-[#173d6e] sm:text-2xl">
                        {{ $materi->judul }}
                    </h1>
                    <p class="mt-1 text-sm text-slate-700">
                        {{ $jenisSumber }} · {{ \App\Support\Waktu::singkat($materi->created_at) }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dosen.materi.edit', $materi) }}"
                   class="{{ $tombol }} border border-slate-300 bg-white text-[#173d6e] hover:bg-slate-50">
                    <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i> Ubah
                </a>

                <form method="POST" action="{{ route('dosen.materi.destroy', $materi) }}"
                      data-konfirmasi-judul="Hapus Materi"
                      data-konfirmasi="Hapus materi {{ $materi->judul }} dari kelas ini?">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="{{ $tombol }} border border-red-200 bg-white text-red-700 hover:bg-red-50">
                        <i class="fa-regular fa-trash-can" aria-hidden="true"></i> Hapus
                    </button>
                </form>
            </div>
        </header>

        <section class="{{ $panel }} space-y-4" aria-labelledby="judul-deskripsi">
            <div>
                <h2 id="judul-deskripsi" class="text-base font-bold text-[#173d6e]">Deskripsi</h2>
                <p class="mt-2 text-sm leading-relaxed whitespace-pre-wrap text-slate-700 [overflow-wrap:anywhere]">{{ $materi->deskripsi ?: 'Belum ada deskripsi.' }}</p>
            </div>

            @if ($berkas)
                <section class="border-t border-slate-100 pt-4 [&_a]:no-underline" aria-labelledby="judul-lampiran">
                    <h2 id="judul-lampiran" class="mb-2 text-base font-bold text-[#173d6e]">Lampiran</h2>

                    <ul class="grid gap-2">
                        @forelse ($lampiran as $indeks => $file)
                            @php $mimeVideo = $materi->mimeVideo($file['nama']); @endphp

                            <li>
                                @if ($mimeVideo)
                                    <details name="video-materi" class="group rounded-lg border border-slate-200"
                                             ontoggle="if (!this.open) this.querySelector('video').pause()">
                                        <summary class="{{ $fokus }} flex cursor-pointer list-none flex-wrap items-center
                                                        gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-[#173d6e]
                                                        hover:bg-[#dfeffe] [&::-webkit-details-marker]:hidden">
                                            <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-[#dfeffe]"
                                                  aria-hidden="true">
                                                <i class="fa-solid fa-film"></i>
                                            </span>

                                            <span class="min-w-0 flex-1 [overflow-wrap:anywhere]">
                                                {{ $file['nama'] }}
                                            </span>
                                            <span class="shrink-0 group-open:hidden">Putar video</span>
                                            <span class="hidden shrink-0 group-open:inline">Tutup pemutar</span>
                                        </summary>

                                        <div class="space-y-2 border-t border-slate-200 p-3">
                                            <video controls playsinline preload="none" aria-label="{{ $file['nama'] }}"
                                                   class="block max-h-[65vh] w-full rounded-lg bg-black">
                                                <source src="{{ route('file.materi', [$materi, 'lampiran' => $indeks]) }}"
                                                        type="{{ $mimeVideo }}">
                                                Gunakan tombol Unduh video jika pemutar tidak didukung.
                                            </video>

                                            <a href="{{ route('file.materi', [$materi, 'lampiran' => $indeks, 'unduh' => 1]) }}"
                                               class="{{ $tombol }} border border-[#acd6fb] text-[#173d6e] hover:bg-[#dfeffe]">
                                                <i class="fa-solid fa-download" aria-hidden="true"></i> Unduh video
                                            </a>
                                        </div>
                                    </details>
                                @else
                                    @include('mahasiswa._file', [
                                        'judul' => $file['nama'],
                                        'url' => route('file.materi', [$materi, 'lampiran' => $indeks]),
                                        'namaFile' => $file['nama'],
                                        'baruTab' => $materi->bisaDilihat($file['nama']),
                                    ])
                                @endif
                            </li>
                        @empty
                            <li class="text-sm text-slate-600">Belum ada file.</li>
                        @endforelse
                    </ul>
                </section>
            @endif

            @if ($adaTautan || ! $berkas)
                <section class="border-t border-slate-100 pt-4 [&_a]:no-underline" aria-labelledby="judul-tautan">
                    <h2 id="judul-tautan" class="mb-2 text-base font-bold text-[#173d6e]">Tautan Materi</h2>

                    @if ($adaTautan)
                        @include('mahasiswa._file', [
                            'judul' => $materi->judul,
                            'url' => $materi->url,
                            'link' => true,
                        ])
                    @else
                        <p class="text-sm text-slate-600">Tautan materi belum tersedia.</p>
                    @endif
                </section>
            @endif
        </section>
    </div>
@endsection