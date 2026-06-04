<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function __construct(private NotifikasiService $notif) {}

    public function kirim()
    {
        $anggota = User::where('role','anggota')->where('account_status','active')->get(['id','name','email']);
        return view('admin.notifikasi.kirim', compact('anggota'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'target'   => 'required|in:all,specific',
            'user_ids' => 'required_if:target,specific|array',
            'user_ids.*'=> 'exists:users,id',
            'category' => 'required|in:mendesak,pengingat,update',
            'title'    => 'required|string|max:255',
            'message'  => 'required|string|max:1000',
        ]);

        if ($request->target === 'all') {
            $this->notif->sendToAll($request->category, $request->title, $request->message);
        } else {
            foreach ($request->user_ids as $userId) {
                $this->notif->send((int)$userId, $request->category, $request->title, $request->message);
            }
        }

        return back()->with('success', 'Notifikasi berhasil dikirim.');
    }
}
