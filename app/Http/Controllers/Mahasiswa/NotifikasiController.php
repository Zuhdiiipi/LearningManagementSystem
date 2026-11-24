<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;

// Daftar notifikasi mahasiswa (ikon lonceng): materi baru, tugas baru, quiz, nilai, pengingat deadline
class NotifikasiController extends Controller
{
    public function index()
    {
        return view('mahasiswa.notifikasi.index', [
            'notifikasi' => auth()->user()->notifications()->paginate(15),
        ]);
    }

    // Klik notifikasi: tandai sudah dibaca, lalu buka halaman terkait
    public function buka(string $id)
    {
        $notif = auth()->user()->notifications()->findOrFail($id);
        $notif->markAsRead();

        return redirect($notif->data['url'] ?? route('mahasiswa.dashboard'));
    }

    public function bacaSemua()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('sukses', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
