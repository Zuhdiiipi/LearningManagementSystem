{{-- Foto profil; kalau belum ada foto, tampilkan inisial nama --}}
@if ($u->foto)
  <img src="{{ route('file.foto', $u) }}" alt="{{ $u->name }}" class="avatar {{ $kelasCss ?? '' }}">
@else
  <span class="avatar {{ $kelasCss ?? '' }}">{{ \App\Support\Waktu::inisial($u->name) }}</span>
@endif
