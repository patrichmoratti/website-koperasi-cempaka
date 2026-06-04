<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Pesan;
use App\Models\User;
use Illuminate\Http\Request;

class PesanController extends Controller
{
    public function index()
    {
        $threads = Pesan::where('receiver_id', auth()->id())
            ->orWhere('sender_id', auth()->id())
            ->with(['sender','receiver'])
            ->latest()
            ->get()
            ->groupBy(fn($p) => $p->sender_id === auth()->id() ? $p->receiver_id : $p->sender_id)
            ->map(fn($msgs) => $msgs->first());

        $unreadCount = Pesan::where('receiver_id', auth()->id())->where('is_read', false)->count();
        return view('pengurus.pesan.index', compact('threads','unreadCount'));
    }

    public function show(User $user)
    {
        Pesan::where('sender_id', $user->id)->where('receiver_id', auth()->id())->update(['is_read' => true]);
        $messages = Pesan::where(fn($q) => $q->where('sender_id', auth()->id())->where('receiver_id', $user->id))
            ->orWhere(fn($q) => $q->where('sender_id', $user->id)->where('receiver_id', auth()->id()))
            ->orderBy('sent_at')->get();
        return view('pengurus.pesan.show', compact('user','messages'));
    }

    public function reply(Request $request, User $user)
    {
        $request->validate(['content' => 'required|string|max:2000']);
        Pesan::create(['sender_id' => auth()->id(), 'receiver_id' => $user->id, 'content' => $request->content]);
        return back()->with('success', 'Pesan terkirim.');
    }
}
