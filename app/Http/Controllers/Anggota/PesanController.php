<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Pesan;
use App\Models\User;
use Illuminate\Http\Request;

class PesanController extends Controller
{
    public function index()
    {
        $pengurus = User::whereIn('role',['admin','pengurus'])->where('account_status','active')->get();
        $threads  = collect();
        foreach ($pengurus as $p) {
            $last = Pesan::where(fn($q) => $q->where('sender_id', auth()->id())->where('receiver_id', $p->id))
                ->orWhere(fn($q) => $q->where('sender_id', $p->id)->where('receiver_id', auth()->id()))
                ->latest('sent_at')->first();
            if ($last) {
                $last->partner = $p;
                $threads->push($last);
            }
        }
        return view('anggota.pesan.index', compact('threads','pengurus'));
    }

    public function show(User $user)
    {
        abort_if(!in_array($user->role, ['admin','pengurus']), 403);
        Pesan::where('sender_id', $user->id)->where('receiver_id', auth()->id())->update(['is_read' => true]);
        $messages = Pesan::where(fn($q) => $q->where('sender_id', auth()->id())->where('receiver_id', $user->id))
            ->orWhere(fn($q) => $q->where('sender_id', $user->id)->where('receiver_id', auth()->id()))
            ->orderBy('sent_at')->get();
        return view('anggota.pesan.show', compact('user','messages'));
    }

    public function send(Request $request, User $user)
    {
        $request->validate(['content' => 'required|string|max:2000']);
        Pesan::create(['sender_id' => auth()->id(), 'receiver_id' => $user->id, 'content' => $request->content]);
        return back()->with('success', 'Pesan terkirim.');
    }
}
