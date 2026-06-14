<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotifikasiService;
use App\Services\TransactionLogService;
use App\Models\RiwayatStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AnggotaController extends Controller
{
    public function __construct(private NotifikasiService $notif, private TransactionLogService $transactionLog) {}

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

        $anggota = $query->with(['transaksiGadai.jenisBarang','simpanan'])->latest()->paginate(15)->withQueryString();
        return view('admin.anggota.index', compact('anggota'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6',
            'phone'        => 'required|string|max:20',
            'nik'          => 'required|string|size:16|unique:users,nik',
            'address'      => 'required|string|max:500',
            'ktp_photo'    => 'nullable|image|max:5120',
            'selfie_photo' => 'nullable|image|max:5120',
        ]);

        $ktpPath    = $request->file('ktp_photo')?->store('kyc/ktp', 'public');
        $selfiePath = $request->file('selfie_photo')?->store('kyc/selfie', 'public');

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'phone'          => $request->phone,
            'nik'            => $request->nik,
            'address'        => $request->address,
            'role'           => 'anggota',
            'account_status' => 'active',
            'ktp_photo'      => $ktpPath,
            'selfie_photo'   => $selfiePath,
        ]);

        return redirect()->route('admin.anggota.index')
            ->with('success', "Anggota {$user->name} berhasil didaftarkan.");
    }

    public function show(User $user)
    {
        $user->load(['pengajuanGadai.jenisBarang','transaksiGadai.jenisBarang','simpanan']);
        return view('admin.anggota.show', compact('user'));
    }

    public function activate(User $user)
    {
        $logData = [
            'transaction_type' => 'registrasi',
            'reference_id'     => $user->id,
            'reference_type'   => 'users',
            'anggota_id'       => $user->id,
            'amount'           => null,
            'description'      => "Registrasi akun {$user->name} diaktifkan",
        ];

        try {
            DB::transaction(function () use ($user, $logData) {
                $user->update(['account_status' => 'active']);
                RiwayatStatus::record('users', $user->id, $user->account_status, 'active');
                $this->notif->registrasiDisetujui($user->id);
                $this->transactionLog->log($logData);
            });
        } catch (\Throwable $e) {
            $this->transactionLog->logFailure($logData, $e);
            throw $e;
        }
        return back()->with('success', "Akun {$user->name} berhasil diaktifkan.");
    }

    public function reject(Request $request, User $user)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $logData = [
            'transaction_type' => 'registrasi',
            'reference_id'     => $user->id,
            'reference_type'   => 'users',
            'anggota_id'       => $user->id,
            'amount'           => null,
            'description'      => "Registrasi akun {$user->name} ditolak: {$request->reason}",
        ];

        try {
            DB::transaction(function () use ($request, $user, $logData) {
                $user->update(['account_status' => 'rejected', 'rejection_reason' => $request->reason]);
                RiwayatStatus::record('users', $user->id, $user->account_status, 'rejected', null, $request->reason);
                $this->notif->registrasiDitolak($user->id, $request->reason);
                $this->transactionLog->log($logData);
            });
        } catch (\Throwable $e) {
            $this->transactionLog->logFailure($logData, $e);
            throw $e;
        }
        return back()->with('success', "Pendaftaran {$user->name} ditolak.");
    }

    public function suspend(User $user)
    {
        $user->update(['account_status' => 'suspended']);
        return back()->with('success', "Akun {$user->name} disuspend.");
    }

    public function reactivate(User $user)
    {
        $logData = [
            'transaction_type' => 'registrasi',
            'reference_id'     => $user->id,
            'reference_type'   => 'users',
            'anggota_id'       => $user->id,
            'amount'           => null,
            'description'      => "Akun {$user->name} diaktifkan kembali dari suspend",
        ];

        try {
            DB::transaction(function () use ($user, $logData) {
                $oldStatus = $user->account_status;
                $user->update(['account_status' => 'active']);
                RiwayatStatus::record('users', $user->id, $oldStatus, 'active');
                $this->notif->akunDiaktifkanKembali($user->id);
                $this->transactionLog->log($logData);
            });
        } catch (\Throwable $e) {
            $this->transactionLog->logFailure($logData, $e);
            throw $e;
        }
        return back()->with('success', "Akun {$user->name} berhasil diaktifkan kembali.");
    }

    public function resetPassword(User $user)
    {
        $newPassword = 'Mony' . rand(10000, 99999) . '!';
        $user->update(['password' => Hash::make($newPassword)]);
        return back()->with('success', "Password {$user->name} direset. Password baru: {$newPassword}");
    }
}
