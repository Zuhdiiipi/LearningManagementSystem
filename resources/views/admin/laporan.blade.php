@extends('layouts.panel')

@section('title', 'Laporan Aktivitas')

@section('content')
  <div class="a-head" style="margin-top:0"><h1>Laporan Aktivitas</h1></div>
  <div class="a-lama">
  <div class="stats">
    @foreach ($statistik as $label => $nilai)
      <div class="stat small"><div><b>{{ $nilai }}</b>{{ $label }}</div></div>
    @endforeach
  </div>

  <div class="panel">
    <h2>Daftar Kelas</h2>
    <table>
      <thead><tr><th>Mata Kuliah</th><th>Kelas</th><th>Dosen</th><th>Mahasiswa</th><th>Dibuat</th></tr></thead>
      <tbody>
        @forelse ($kelas as $k)
          <tr>
            <td>{{ $k->mataKuliah->nama }}</td>
            <td>{{ $k->nama }}</td>
            <td>{{ $k->dosen->name }}</td>
            <td>{{ $k->mahasiswa_count }}</td>
            <td>{{ $k->created_at->translatedFormat('d M Y') }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="muted">Belum ada kelas.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="panel">
    <h2>Log Aktivitas Pengguna</h2>
    <table>
      <thead><tr><th>Waktu</th><th>Pengguna</th><th>Peran</th><th>Aktivitas</th></tr></thead>
      <tbody>
        @forelse ($log as $l)
          <tr>
            <td>{{ $l->created_at->translatedFormat('d M Y H:i') }}</td>
            <td>{{ $l->user->name ?? '-' }}</td>
            <td>{{ ucfirst($l->user->role ?? '-') }}</td>
            <td>{{ $l->aksi }}</td>
          </tr>
        @empty
          <tr><td colspan="4" class="muted">Belum ada aktivitas.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="pagination">
      @if ($log->previousPageUrl()) <a href="{{ $log->previousPageUrl() }}" class="btn btn-sm">&laquo; Sebelumnya</a> @endif
      <span>Halaman {{ $log->currentPage() }} dari {{ $log->lastPage() }}</span>
      @if ($log->nextPageUrl()) <a href="{{ $log->nextPageUrl() }}" class="btn btn-sm">Berikutnya &raquo;</a> @endif
    </div>
  </div>
    <div class="panel">
      <h2>Log Notifikasi Email</h2>
      <table>
        <thead><tr><th>Waktu</th><th>Penerima</th><th>Jenis</th><th>Subjek</th><th>Status</th></tr></thead>
        <tbody>
          @forelse ($email as $e)
            <tr>
              <td>{{ $e->waktu_kirim->translatedFormat('d M Y H:i') }}</td>
              <td>{{ $e->penerima_email }}</td>
              <td>{{ $e->jenis_notif }}</td>
              <td>{{ $e->subjek }}</td>
              <td><span class="status {{ $e->status_pengiriman ? 'on' : 'off' }}">{{ $e->status_pengiriman ? 'Terkirim' : 'Gagal' }}</span></td>
            </tr>
          @empty
            <tr><td colspan="5" class="muted">Belum ada email yang dikirim.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
