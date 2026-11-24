{{-- Tombol hapus (ikon tempat sampah) dengan konfirmasi. Variabel: $aksi, $tanya --}}
<form method="POST" action="{{ $aksi }}" onsubmit="return confirm(@js($tanya))">
  @csrf @method('DELETE')
  <button class="ikon hapus" title="Hapus"><i class="fa-regular fa-trash-can"></i><span class="sr">Hapus</span></button>
</form>
