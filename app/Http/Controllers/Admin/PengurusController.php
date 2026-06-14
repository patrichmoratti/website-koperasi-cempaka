<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RiwayatStatus;
use App\Services\TransactionLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PengurusController extends Controller
{
    public function __construct(private TransactionLogService $transactionLog) {}

    public function index(Request $request)
    {
        $search = $request->get('search');

        $pengurus = User::where('role', 'pengurus')
            ->when($search, fn($q) => $q->where(fn($sq) =>
                $sq->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%")
            ))
            ->latest()
            ->paginate(18)
            ->withQueryString();

        $stats = [
            'total'  => User::where('role', 'pengurus')->count(),
            'active' => User::where('role', 'pengurus')->where('account_status', 'active')->count(),
        ];

        return view('admin.pengurus.index', compact('pengurus', 'search', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'phone'          => $request->phone,
            'address'        => $request->address,
            'role'           => 'pengurus',
            'account_status' => 'active',
        ]);

        return redirect()->route('admin.pengurus.index')
            ->with('success', "Akun pengurus {$user->name} berhasil dibuat.");
    }

    public function toggleStatus(User $user)
    {
        $oldStatus = $user->account_status;
        $newStatus = $oldStatus === 'active' ? 'suspended' : 'active';
        $action    = $newStatus === 'active' ? 'diaktifkan kembali dari suspend' : 'disuspend';

        $logData = [
            'transaction_type' => 'registrasi',
            'reference_id'     => $user->id,
            'reference_type'   => 'users',
            'anggota_id'       => $user->id,
            'amount'           => null,
            'description'      => "Akun pengurus {$user->name} {$action}",
        ];

        try {
            DB::transaction(function () use ($user, $oldStatus, $newStatus, $logData) {
                $user->update(['account_status' => $newStatus]);
                RiwayatStatus::record('users', $user->id, $oldStatus, $newStatus);
                $this->transactionLog->log($logData);
            });
        } catch (\Throwable $e) {
            $this->transactionLog->logFailure($logData, $e);
            throw $e;
        }

        $label = $newStatus === 'active' ? 'diaktifkan' : 'disuspend';
        return back()->with('success', "Akun {$user->name} berhasil {$label}.");
    }

    public function resetPassword(User $user)
    {
        $newPassword = 'Mony' . rand(10000, 99999) . '!';
        $user->update(['password' => Hash::make($newPassword)]);
        return back()->with('success', "Password {$user->name} direset. Password baru: {$newPassword}");
    }
}