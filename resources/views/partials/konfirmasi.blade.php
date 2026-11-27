<dialog id="dialog-konfirmasi"
        aria-labelledby="judul-konfirmasi"
        aria-describedby="pesan-konfirmasi"
        class="m-auto w-[calc(100%-2rem)] max-w-md overflow-hidden rounded-2xl
               border border-slate-200 bg-white text-slate-900 shadow-xl
               backdrop:bg-slate-950/55">
  <div class="flex items-start gap-4 p-5 sm:p-6">
    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-[#dfeffe] text-[#173d6e]">
      <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
    </span>

    <div class="min-w-0">
      <h2 id="judul-konfirmasi" class="text-lg font-bold text-[#173d6e]"></h2>
      <p id="pesan-konfirmasi"
         class="mt-1 text-sm leading-6 text-slate-600 [overflow-wrap:anywhere]"></p>
    </div>
  </div>

  <form method="dialog"
        class="flex flex-wrap justify-end gap-2 border-t border-slate-100 bg-slate-50 px-5 py-3 sm:px-6">
    <button type="submit" value="batal" autofocus
            class="rounded-lg border border-slate-200 bg-white px-4 py-2
                   text-sm font-semibold text-[#173d6e] hover:bg-[#dfeffe]">
      Batal
    </button>

    <button type="submit" value="lanjut" data-konfirmasi-lanjut
            class="rounded-lg bg-[#173d6e] px-4 py-2
                   text-sm font-semibold text-white hover:bg-[#2b62a8]">
      Lanjutkan
    </button>
  </form>
</dialog>

<script>
  (() => {
    const dialog = document.getElementById('dialog-konfirmasi');
    const judul = document.getElementById('judul-konfirmasi');
    const pesan = document.getElementById('pesan-konfirmasi');
    const tombolLanjut = dialog.querySelector('[data-konfirmasi-lanjut]');
    let tertunda = null;
    let disetujui = null;

    document.addEventListener('submit', event => {
      const form = event.target;

      if (!(form instanceof HTMLFormElement)
          || !form.hasAttribute('data-konfirmasi')
          || form === disetujui
          || event.defaultPrevented) {
        return;
      }

      event.preventDefault();
      if (dialog.open) return;

      tertunda = { form, tombol: event.submitter };
      judul.textContent = form.dataset.konfirmasiJudul || 'Konfirmasi tindakan';
      pesan.textContent = form.dataset.konfirmasi;
      tombolLanjut.textContent = form.dataset.konfirmasiJudul || 'Lanjutkan';
      dialog.returnValue = '';
      dialog.showModal();
    });

    dialog.addEventListener('close', () => {
      if (dialog.open) return;

      const aksi = tertunda;
      tertunda = null;
      if (!aksi || dialog.returnValue !== 'lanjut') return;

      disetujui = aksi.form;
      try {
        aksi.form.requestSubmit(aksi.tombol ?? undefined);
      } finally {
        disetujui = null;
      }
    });
  })();
</script>