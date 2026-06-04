<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotifikasiService;
use App\Models\RiwayatStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AnggotaController extends Controller
{
    public function __construct(private NotifikasiService $notif) {}

    public function index(Request $request)
    {
        $query = User::where('role', 'anggota');

        if ($request->search) {
            $q = $request->search;
            $query->where(fn($sq) => $sq->where('name','like',"%{$q}%")->orWhere('email','like',"%{$q}%")->orWhere('nik','like',"%{$q}%"));
        }
        if ($request->status) {
            $query->where('account_status', $request->status);
        }

        $anggota = $query->latest()->paginate(15)->withQueryString();
        return view('admin.anggota.index', compact('anggota'));
    }

    public function show(User $user)
    {
        $user->load(['pengajuanGadai.jenisBarang','transaksiGadai.jenisBarang','simpanan','shuDistributions.period']);
        return view('admin.anggota.show', compact('user'));
    }

    public function activate(User $user)
    {
        DB::transaction(function () use ($user) {
            $user->update(['account_status' => 'active']);
            RiwayatStatus::record('users', $user->id, $user->account_status, 'active');
            $this->notif->registrasiDisetujui($user->id);
        });
        return back()->with('success', "Akun {$user->name} berhasil diaktifkan.");
    }

    public function reject(Request $request, User $user)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        DB::transaction(function () use ($request, $user) {
            $user->update(['account_status' => 'rejected', 'rejection_reason' => $request->reason]);
            RiwayatStatus::record('users', $user->id, $user->account_status, 'rejected', null, $request->reason);
            $this->notif->registrasiDitolak($user->id, $request->reason);
        });
        return back()->with('success', "Pendaftaran {$user->name} ditolak.");
    }

    public function suspend(User $user)
    {
        $user->update(['account_status' => 'suspended']);
        return back()->with('success', "Akun {$user->name} disuspend.");
    }

    public function resetPassword(User $user)
    {
        $newPassword = 'Mony' . rand(10000, 99999) . '!';
        $user->update(['password' => Hash::make($newPassword)]);
        return back()->with('success', "Password {$user->name} direset. Password baru: {$newPassword}");
    }
}
