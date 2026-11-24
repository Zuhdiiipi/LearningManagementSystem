@extends('layouts.panel')

@section('title', 'Notifikasi')
@section('kembali', route('mahasiswa.dashboard'))

@section('content')
  <div class="m-title-row">
    <h1 class="m-title">Notifikasi</h1>
    @if (auth()->user()->unreadNotifications()->exists())
      <form method="POST" action="{{ route('mahasiswa.notifikasi.baca-semua') }}">@csrf <button class="m-btn navy small">Tandai semua dibaca</button></form>
    @endif
  </div>
  <div class="m-box">
    <div class="m-list">
      @forelse ($notifikasi as $n)
        <a href="{{ route('mahasiswa.notifikasi.buka', $n->id) }}" class="m-item notif {{ $n->read_at ? '' : 'baru' }}">
          <span class="m-icon"><i class="fa-solid fa-{{ ['Materi' => 'book', 'Tugas' => 'file-pen', 'Nilai' => 'star', 'Pengingat' => 'clock', 'Quiz' => 'circle-question'][$n->data['jenis'] ?? ''] ?? 'bell' }}"></i></span>
          <div class="teks">
            <h4>{{ $n->data['judul'] ?? '' }} @unless ($n->read_at) <span class="badge baru">Baru</span> @endunless</h4>
            <p>{{ $n->data['pesan'] ?? '' }}</p>
            <p class="m-muted">{{ $n->data['jenis'] ?? 'Info' }} · {{ $n->created_at->translatedFormat('j M Y, H:i') }} ({{ $n->created_at->diffForHumans() }})</p>
          </div>
        </a>
      @empty
        <div class="m-item"><p class="m-muted">Belum ada notifikasi. Notifikasi muncul saat dosen menambah materi, tugas, quiz, memberi nilai, atau saat deadline tugas tinggal kurang dari 24 jam.</p></div>
      @endforelse
    </div>
    <div style="margin-top:16px">{{ $notifikasi->links() }}</div>
  </div>
@endsection
