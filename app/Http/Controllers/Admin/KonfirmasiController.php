<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembayaranGadai;
use App\Models\Simpanan;
use App\Models\User;
use App\Services\GadaiService;
use App\Services\NotifikasiService;
use App\Models\RiwayatStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KonfirmasiController extends Controller
{
    public function __construct(private GadaiService $gadaiService, private NotifikasiService $notif) {}

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pembayaran_gadai');

        $pembayaranGadai = PembayaranGadai::with(['transaksi.anggota','confirmedBy'])
            ->where('status','pending')
            ->latest()->paginate(15)->withQueryString();

        $simpanan = Simpanan::with(['anggota','confirmedBy'])
            ->where('status','pending')
            ->latest()->paginate(15)->withQueryString();

        $registrasi = User::where('role','anggota')
            ->where('account_status','pending')
            ->latest()->paginate(15)->withQueryString();

        $counts = [
            'pembayaran_gadai' => PembayaranGadai::where('status','pending')->count(),
            'simpanan'         => Simpanan::where('status','pending')->count(),
            'registrasi'       => User::where('role','anggota')->where('account_status','pending')->count(),
        ];

        return view('admin.konfirmasi.index', compact('tab','pembayaranGadai','simpanan','registrasi','counts'));
    }

    public function confirmPembayaranGadai(PembayaranGadai $pembayaran)
    {
        $this->gadaiService->confirmPembayaran($pembayaran);
        return back()->with('success', 'Pembayaran dikonfirmasi.');
    }

    public function rejectPembayaranGadai(Request $request, PembayaranGadai $pembayaran)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $this->gadaiService->rejectPembayaran($pembayaran, $request->reason);
        return back()->with('success', 'Pembayaran ditolak.');
    }

    public function confirmSimpanan(Simpanan $simpanan)
    {
        DB::transaction(function () use ($simpanan) {
            $simpanan->update([
                'status'       => 'confirmed',
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now(),
            ]);
            $this->notif->simpananDikonfirmasi($simpanan->anggota_id, $simpanan->type_label);
        });
        return back()->with('success', 'Simpanan dikonfirmasi.');
    }

    public function rejectSimpanan(Request $request, Simpanan $simpanan)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $simpanan->update([
            'status'           => 'rejected',
            'confirmed_by'     => auth()->id(),
            'confirmed_at'     => now(),
            'rejection_reason' => $request->reason,
        ]);
        return back()->with('success', 'Simpanan ditolak.');
    }

    public function confirmRegistrasi(User $user)
    {
        DB::transaction(function () use ($user) {
            $user->update(['account_status' => 'active']);
            RiwayatStatus::record('users', $user->id, 'pending', 'active');
            $this->notif->registrasiDisetujui($user->id);
        });
        return back()->with('success', "Registrasi {$user->name} disetujui.");
    }

    public function rejectRegistrasi(Request $request, User $user)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        DB::transaction(function () use ($request, $user) {
            $user->update(['account_status' => 'rejected', 'rejection_reason' => $request->reason]);
            RiwayatStatus::record('users', $user->id, 'pending', 'rejected', null, $request->reason);
            $this->notif->registrasiDitolak($user->id, $request->reason);
        });
        return back()->with('success', "Registrasi {$user->name} ditolak.");
    }
}
