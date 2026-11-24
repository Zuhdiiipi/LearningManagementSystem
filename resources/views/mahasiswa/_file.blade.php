{{-- Kartu file seperti di desain (ikon PDF + nama file). $judul, $url, $namaFile, $link (opsional) --}}
@php
  $ext = $link ?? false ? 'LINK' : strtoupper(pathinfo($namaFile ?? '', PATHINFO_EXTENSION) ?: 'FILE');
  $warna = match (true) {
      $ext === 'LINK' => 'link',
      in_array($ext, ['DOC', 'DOCX', 'XLS', 'XLSX', 'TXT']) => 'doc',
      in_array($ext, ['PPT', 'PPTX']) => 'ppt',
      in_array($ext, ['ZIP', 'RAR']) => 'zip',
      in_array($ext, ['PNG', 'JPG', 'JPEG', 'MP4']) => 'img',
      default => '',
  };
@endphp
<a href="{{ $url }}" class="m-file" @if (($link ?? false) || ($baruTab ?? false)) target="_blank" rel="noopener" @endif>
  <span class="ext {{ $warna }}">@if ($ext === 'LINK')<i class="fa-solid fa-link"></i>@else{{ mb_substr($ext, 0, 4) }}@endif</span>
  <span><b>{{ $judul }}</b><small>{{ $ext === 'LINK' ? 'Buka link' : $ext }}</small></span>
</a>
