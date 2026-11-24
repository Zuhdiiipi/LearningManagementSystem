<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LMS - Learning Management System</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/lms.css') }}">
</head>
<body>
  <nav class="navbar">
    <a href="{{ url('/') }}" class="brand"><img src="{{ asset('images/logo.png') }}" alt="Logo"> LMS</a>
    <a href="{{ url('/register') }}" class="btn-regis">Daftar</a>
  </nav>

  <section class="hero">
    <div>
      <div class="hero-box">
        <small>LEARNING MANAGEMENT <span>SYSTEM</span></small>
        <h1>Belajar Lebih Mudah,<br>Bersama LMS</h1>
        <p>Platform pembelajaran digital yang membantu mahasiswa dan dosen dalam proses belajar mengajar secara efektif, fleksibel, dan terintegrasi.</p>
      </div>
      <div class="roles">
        <a href="{{ url('/login/mahasiswa') }}" class="role-card">
          <div class="icon"><i class="fa-solid fa-user-graduate"></i></div>
          <div class="row">Mahasiswa <i class="fa-regular fa-circle-right"></i></div>
        </a>
        <a href="{{ url('/login/dosen') }}" class="role-card">
          <div class="icon"><i class="fa-solid fa-chalkboard-user"></i></div>
          <div class="row">Dosen <i class="fa-regular fa-circle-right"></i></div>
        </a>
      </div>
    </div>
    <img src="{{ asset('images/hero.png') }}" alt="Ilustrasi" class="hero-img">
  </section>

  <footer class="footer">
    <div class="left">
      <div class="brand"><img src="{{ asset('images/logo.png') }}" alt="Logo"> LMS</div>
      <p>Mewujudkan pembelajaran yang lebih baik<br>untuk seluruh komunitas Akademik</p>
    </div>
    <a href="{{ url('/login/admin') }}" class="btn-admin">Admin</a>
  </footer>
</body>
</html>
