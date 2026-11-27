<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar Akun - LMS</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  @vite('resources/css/app.css')
</head>

<body class="m-0 flex min-h-dvh flex-col bg-[#f4f7fb] font-sans text-[#173d6e]">
  @php
    $fokus = 'focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#2b62a8]';
    $kelasInput = 'box-border min-h-12 w-full rounded-xl border border-solid border-slate-300
                   bg-slate-50 py-2.5 pr-3 pl-10 font-sans text-base text-slate-900
                   placeholder:text-slate-500 focus:border-[#2b62a8] focus:bg-white
                   focus:outline-none focus:ring-2 focus:ring-[#acd6fb] aria-invalid:border-red-400';

    $peran = ['mahasiswa' => 'Mahasiswa', 'dosen' => 'Dosen'];

    $kolom = [
      'name' => [
        'label' => 'Nama lengkap', 'type' => 'text', 'ikon' => 'fa-regular fa-user',
        'placeholder' => 'Sesuai data kampus', 'autocomplete' => 'name', 'maxlength' => 100,
      ],
      'nim_nidn' => [
        'label' => 'NIM / NIDN', 'type' => 'text', 'ikon' => 'fa-regular fa-id-card',
        'placeholder' => 'NIM atau NIDN', 'autocomplete' => 'off', 'maxlength' => 20,
      ],
      'role' => [
        'label' => 'Peran', 'type' => 'select', 'ikon' => 'fa-solid fa-user-tag',
      ],
      'email' => [
        'label' => 'Email', 'type' => 'email', 'ikon' => 'fa-regular fa-envelope',
        'placeholder' => 'nama@contoh.com', 'autocomplete' => 'email',
      ],
      'password' => [
        'label' => 'Kata sandi', 'type' => 'password', 'ikon' => 'fa-solid fa-lock',
        'placeholder' => 'Minimal 8 karakter', 'autocomplete' => 'new-password',
      ],
      'password_confirmation' => [
        'label' => 'Konfirmasi kata sandi', 'type' => 'password', 'ikon' => 'fa-solid fa-lock',
        'placeholder' => 'Ulangi kata sandi', 'autocomplete' => 'new-password',
      ],
    ];
  @endphp

  <header class="border-0 border-b border-solid border-slate-200 bg-white">
    <nav aria-label="Navigasi utama"
         class="mx-auto flex min-h-16 max-w-6xl items-center justify-between gap-3 px-4 sm:px-8">
      <a href="{{ route('landing') }}"
         class="{{ $fokus }} inline-flex items-center gap-2.5 rounded-lg text-[#173d6e] no-underline">
        <img src="{{ asset('images/logo.png') }}" width="59" height="56" alt=""
             class="size-9 shrink-0 object-contain">

        <span>
          <span class="block text-lg leading-tight font-extrabold">LMS</span>
          <span class="hidden text-xs leading-tight text-slate-600 sm:block">
            Learning Management System
          </span>
        </span>
      </a>

      <a href="{{ route('landing') }}"
         class="{{ $fokus }} inline-flex min-h-11 shrink-0 items-center gap-2 rounded-lg px-2.5
                text-sm font-semibold text-[#173d6e] no-underline transition-colors hover:bg-[#dfeffe]">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
        Kembali ke beranda
      </a>
    </nav>
  </header>

  <main class="flex flex-1 items-center justify-center px-4 py-6 sm:px-8 sm:py-10">
    <div class="grid w-full max-w-2xl overflow-hidden rounded-2xl border border-solid border-slate-200
                bg-white shadow-lg shadow-[#173d6e]/10 sm:rounded-3xl
                lg:max-w-5xl lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">
      <div aria-hidden="true"
           class="relative hidden items-center justify-center overflow-hidden bg-linear-to-br
                  from-[#173d6e] to-[#102e55] p-7 lg:flex">
        <div class="absolute top-1/2 left-1/2 aspect-square w-4/5 -translate-x-1/2 -translate-y-1/2
                    rounded-full border border-solid border-[#acd6fb]/20 bg-[#2b62a8]/45
                    ring-24 ring-[#acd6fb]/5">
        </div>

        <img src="{{ asset('images/register.png') }}" width="407" height="297" alt=""
             class="relative block h-auto w-full drop-shadow-xl">
      </div>

      <section aria-labelledby="judul-daftar" class="min-w-0 px-5 py-7 sm:p-9 lg:p-10">
        <span class="mb-3 grid size-11 place-items-center rounded-xl bg-[#dfeffe] text-lg">
          <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
        </span>

        <h1 id="judul-daftar" class="m-0 text-2xl leading-tight font-extrabold tracking-tight sm:text-3xl">
          Daftar akun
        </h1>

        <p class="mt-2 mb-0 text-sm leading-relaxed text-slate-600 sm:text-base">
          Buat akun LMS sebagai mahasiswa atau dosen.
        </p>

        @if ($errors->any())
          <div role="alert"
               class="mt-4 flex items-start gap-2 rounded-xl border border-solid border-red-200
                      bg-red-50 p-3 text-sm text-red-800">
            <i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>
            <span>Periksa kembali kolom yang ditandai di bawah.</span>
          </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}" class="mt-5">
          @csrf

          <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($kolom as $nama => $field)
              @php $adaError = $errors->has($nama); @endphp

              <div class="min-w-0">
                <label for="{{ $nama }}" class="mb-1.5 block text-sm font-semibold">
                  {{ $field['label'] }}
                </label>

                <div class="relative">
                  <i class="{{ $field['ikon'] }} pointer-events-none absolute top-1/2 left-3.5
                            -translate-y-1/2 text-sm text-[#4d6a92]" aria-hidden="true"></i>

                  @if ($field['type'] === 'select')
                    <select id="{{ $nama }}" name="{{ $nama }}" required
                            aria-invalid="{{ $adaError ? 'true' : 'false' }}"
                            @if ($adaError) aria-describedby="error-{{ $nama }}" @endif
                            class="{{ $kelasInput }} invalid:text-slate-500">
                      <option value="" disabled @selected(! in_array(old('role'), array_keys($peran), true))>
                        Pilih peran
                      </option>

                      @foreach ($peran as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(old('role') === $nilai)>
                          {{ $label }}
                        </option>
                      @endforeach
                    </select>
                  @else
                    <input id="{{ $nama }}" name="{{ $nama }}" type="{{ $field['type'] }}"
                           autocomplete="{{ $field['autocomplete'] }}"
                           placeholder="{{ $field['placeholder'] }}"
                           @if ($field['type'] === 'password')
                             minlength="8"
                           @else
                             value="{{ old($nama) }}"
                           @endif
                           @isset($field['maxlength']) maxlength="{{ $field['maxlength'] }}" @endisset
                           aria-invalid="{{ $adaError ? 'true' : 'false' }}"
                           @if ($adaError) aria-describedby="error-{{ $nama }}" @endif
                           class="{{ $kelasInput }}" required>
                  @endif
                </div>

                @error($nama)
                  <p id="error-{{ $nama }}" class="mt-1.5 mb-0 text-sm leading-snug text-red-700">
                    {{ $message }}
                  </p>
                @enderror
              </div>
            @endforeach
          </div>

          <button type="submit"
                  class="{{ $fokus }} mt-5 min-h-12 w-full cursor-pointer rounded-xl border-0 bg-[#173d6e]
                         px-4 py-2.5 font-sans text-sm font-bold text-white transition-colors hover:bg-[#2b62a8]">
            Daftar akun
          </button>
        </form>

        <div class="mt-5 flex flex-wrap items-baseline gap-x-3 gap-y-1 border-0 border-t border-solid
                    border-slate-200 pt-4 text-sm text-slate-600">
          <span>Sudah punya akun?</span>

          @foreach ($peran as $nilai => $label)
            <a href="{{ route('login.form', ['role' => $nilai]) }}"
               class="{{ $fokus }} rounded-sm font-semibold text-[#173d6e] no-underline hover:text-[#2b62a8]">
              Masuk {{ $label }}
            </a>
          @endforeach
        </div>
      </section>
    </div>
  </main>
</body>
</html>