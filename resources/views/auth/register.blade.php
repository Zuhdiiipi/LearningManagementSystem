<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar Akun - LMS</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/lms.css') }}">
</head>
<body class="auth-page">
  <div class="auth-card">
    <!-- Panel kiri -->
    <div class="auth-left">
      <div class="brand"><img src="{{ asset('images/logo.png') }}" alt="Logo"> LMS</div>
      <div class="auth-role">
        <h2>Bergabung dengan LMS</h2>
      </div>
      <p>Daftar Pengalaman Belajar Yang Lebih Mudah, Fleksibel, dan Terstruktur.</p>
      <img src="{{ asset('images/register.png') }}" alt="" class="auth-img">
    </div>

    <!-- Panel kanan: form registrasi -->
    <div class="auth-right">
      <a href="{{ route('landing') }}" class="back"><i class="fa-solid fa-arrow-left"></i> Kembali Ke Beranda</a>

      <h1 class="register-title">Daftar Akun</h1>

      @if ($errors->any())
        <div class="alert">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('register.submit') }}" class="register-form">
        @csrf
        <label>Nama Lengkap</label>
        <div class="input">
          <i class="fa-regular fa-user"></i>
          <input type="text" name="name" value="{{ old('name') }}" required>
        </div>

        <label>NIM/NIDN</label>
        <div class="input">
          <i class="fa-regular fa-id-card"></i>
          <input type="text" name="nim_nidn" value="{{ old('nim_nidn') }}" required>
        </div>

        <label>Peran</label>
        <div class="input">
          <i class="fa-solid fa-user-tag"></i>
          <select name="role" required>
            <option value="">-- Pilih Peran --</option>
            <option value="mahasiswa" @selected(old('role') == 'mahasiswa')>Mahasiswa</option>
            <option value="dosen" @selected(old('role') == 'dosen')>Dosen</option>
          </select>
        </div>

        <label>Email</label>
        <div class="input">
          <i class="fa-regular fa-envelope"></i>
          <input type="email" name="email" value="{{ old('email') }}" required>
        </div>

        <label>Password</label>
        <div class="input">
          <i class="fa-solid fa-lock"></i>
          <input type="password" name="password" required>
        </div>

        <label>Konfirmasi Password</label>
        <div class="input">
          <i class="fa-solid fa-lock"></i>
          <input type="password" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn-masuk">Daftar</button>
      </form>

      <p class="note">Sudah punya akun? <a href="{{ route('login.form', 'mahasiswa') }}">Masuk di sini.</a></p>
    </div>
  </div>
</body>
</html>
