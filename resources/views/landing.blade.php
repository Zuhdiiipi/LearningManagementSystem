<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LMS - Learning Management System</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  @vite('resources/css/app.css')
</head>

<body class="flex min-h-dvh flex-col bg-[#f4f7fb] font-sans text-[#173d6e]
             [&_a]:no-underline [&_a]:text-inherit">
  @php
    $pilihanMasuk = [
      'mahasiswa' => ['Mahasiswa', 'Akses materi dan tugas.', 'fa-user-graduate'],
      'dosen' => ['Dosen', 'Kelola materi dan kelas.', 'fa-chalkboard-user'],
    ];

    $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#2b62a8]';
  @endphp

  <header class="border-b border-slate-200/80 bg-white">
    <nav aria-label="Navigasi utama"
         class="mx-auto flex min-h-16 max-w-6xl items-center justify-between gap-3 px-4 sm:px-8">
      <a href="{{ route('landing') }}"
         class="{{ $fokus }} inline-flex items-center gap-2.5 rounded-lg">
        <img src="{{ asset('images/logo.png') }}" width="59" height="56" alt=""
             class="size-9 shrink-0 object-contain">

        <span>
          <span class="block text-lg leading-tight font-extrabold">LMS</span>
          <span class="hidden text-xs leading-tight text-slate-600 sm:block">
            Learning Management System
          </span>
        </span>
      </a>

      <a href="{{ route('register') }}"
         class="{{ $fokus }} inline-flex min-h-11 shrink-0 items-center rounded-lg border
                border-[#acd6fb] bg-[#dfeffe] px-4 text-sm font-bold transition-colors hover:bg-[#acd6fb]">
        Daftar akun
      </a>
    </nav>
  </header>

  <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-6 sm:px-8 sm:py-9">
    <section aria-labelledby="judul-beranda"
             class="grid overflow-hidden rounded-2xl bg-[#173d6e] text-white shadow-lg
                    shadow-[#173d6e]/15 sm:rounded-3xl lg:min-h-84 lg:grid-cols-2">
      <div class="flex min-w-0 flex-col justify-center px-5 py-6
                  min-[380px]:py-8 sm:px-10 sm:py-11 lg:pr-0">
        <h1 id="judul-beranda"
            class="text-[1.75rem] leading-[1.1] font-extrabold tracking-tight
                   min-[380px]:text-[2rem] sm:text-[2.75rem] xl:text-[3.125rem]">
          Belajar dan mengajar
          <span class="block text-[#acd6fb]">dalam satu LMS.</span>
        </h1>

        <p class="mt-4 max-w-sm text-base leading-relaxed text-[#dfeffe] sm:mt-5 sm:text-lg">
          Akses materi, kumpulkan tugas, dan kelola kegiatan perkuliahan dari satu tempat.
        </p>
      </div>

      <div class="relative h-28 min-w-0 overflow-hidden min-[380px]:h-32 sm:h-48 lg:h-auto">
        <img src="{{ asset('images/hero.png') }}" width="632" height="546" alt=""
             class="absolute inset-0 size-full object-cover object-[center_38%]
                    lg:object-[center_46%]">

        <div aria-hidden="true"
             class="pointer-events-none absolute inset-0 bg-linear-to-b from-[#173d6e]
                    via-[#173d6e]/0 via-22% to-transparent lg:bg-linear-to-r
                    lg:via-[#173d6e]/10 lg:via-35%">
        </div>
      </div>
    </section>

    <section aria-labelledby="judul-masuk" class="mt-7 sm:mt-9">
      <h2 id="judul-masuk" class="mb-3 text-xl font-bold tracking-tight sm:mb-4 sm:text-2xl">
        Masuk ke LMS
      </h2>

      <div class="grid gap-3 sm:grid-cols-2 sm:gap-4">
        @foreach ($pilihanMasuk as $peran => [$label, $deskripsi, $ikon])
          <a href="{{ route('login.form', ['role' => $peran]) }}"
             aria-label="Masuk sebagai {{ $label }}"
             aria-describedby="deskripsi-{{ $peran }}"
             class="{{ $fokus }} group flex min-w-0 items-center gap-3 rounded-2xl border
                    border-slate-200 bg-white p-4 shadow-sm transition
                    hover:border-[#2b62a8] hover:shadow-md sm:gap-4 sm:p-5">
            <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-[#dfeffe]
                         text-base sm:size-12 sm:text-lg">
              <i class="fa-solid {{ $ikon }}" aria-hidden="true"></i>
            </span>

            <span class="min-w-0 flex-1">
              <span class="block text-base font-bold sm:text-lg">{{ $label }}</span>
              <span id="deskripsi-{{ $peran }}" class="mt-0.5 block text-sm leading-snug text-slate-600">
                {{ $deskripsi }}
              </span>
            </span>

            <span aria-hidden="true"
                  class="shrink-0 rounded-lg bg-[#173d6e] px-3 py-2 text-sm font-bold
                         text-white transition-colors group-hover:bg-[#2b62a8]">
              Masuk
            </span>
          </a>
        @endforeach
      </div>
    </section>
  </main>

  <footer>
    <div class="mx-auto max-w-6xl px-4 sm:px-8">
      <div class="flex items-center justify-between gap-3 border-t border-slate-200 py-2">
        <span class="text-sm font-semibold text-slate-600">LMS</span>

        <a href="{{ route('login.form', ['role' => 'admin']) }}"
           class="{{ $fokus }} inline-flex min-h-11 items-center gap-2 rounded-lg px-3
                  text-sm font-medium transition-colors hover:bg-[#dfeffe]">
          <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
          Masuk sebagai admin
        </a>
      </div>
    </div>
  </footer>
</body>
</html>