<span data-avatar aria-hidden="true"
      class="grid h-10 w-10 shrink-0 place-items-center overflow-hidden rounded-full border border-white
             bg-lms-sky text-sm font-bold text-lms-navy {{ $kelasCss ?? '' }}">
  @if ($u->foto)
    <img src="{{ route('file.foto', $u) }}" alt="" width="40" height="40"
         class="block h-full w-full object-cover">
  @else
    {{ \App\Support\Waktu::inisial($u->name) }}
  @endif
</span>