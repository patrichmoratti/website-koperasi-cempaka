<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::where('user_id', auth()->id())->latest()->paginate(20)->withQueryString();
        $unreadCount = Notifikasi::where('user_id', auth()->id())->where('is_read', false)->count();

        return view('anggota.notifikasi.index', compact('notifikasi','unreadCount'));
    }

    public function markRead(Notifikasi $notifikasi)
    {
        abort_if($notifikasi->user_id !== auth()->id(), 403);
        $notifikasi->update(['is_read' => true]);
        return back();
    }

    public function markAllRead()
    {
        Notifikasi::where('user_id', auth()->id())->update(['is_read' => true]);
        return back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }
}
