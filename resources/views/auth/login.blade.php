<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $teks['judul'] }} - LMS</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/lms.css') }}">
</head>
<body class="auth-page">
  <div class="auth-card">
    <!-- Panel kiri: identitas peran -->
    <div class="auth-left">
      <div class="brand"><img src="{{ asset('images/logo.png') }}" alt="Logo"> LMS</div>
      <div class="auth-role">
        <div class="icon"><i class="fa-solid {{ $teks['ikon'] }}"></i></div>
        <h2>{{ $teks['judul'] }}</h2>
      </div>
      <p>{{ $teks['deskripsi'] }}</p>
      <img src="{{ asset('images/login-' . $role . '.png') }}" alt="" class="auth-img">
    </div>

    <!-- Panel kanan: form login -->
    <div class="auth-right">
      <a href="{{ route('landing') }}" class="back"><i class="fa-solid fa-arrow-left"></i> Kembali Ke Beranda</a>

      <h1>Masuk Ke Akun {{ ucfirst($role) }}</h1>

      @if ($errors->any())
        <div class="alert">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('login.submit', $role) }}">
        @csrf
        <label>Email</label>
        <div class="input">
          <i class="fa-regular fa-envelope"></i>
          <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <label>Password</label>
        <div class="input">
          <i class="fa-solid fa-lock"></i>
          <input type="password" name="password" required>
        </div>

        <button type="submit" class="btn-masuk">Masuk</button>
      </form>

      <p class="note">Lupa password? Hubungi Admin.</p>
      @if ($role === 'mahasiswa')
        <p class="note">Belum punya akun? <a href="{{ url('/register') }}">Daftar di sini.</a></p>
      @endif
    </div>
  </div>
</body>
</html>
