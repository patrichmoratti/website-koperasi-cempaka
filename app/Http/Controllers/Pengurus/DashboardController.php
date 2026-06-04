<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\PengajuanGadai;
use App\Models\PembayaranGadai;
use App\Models\Simpanan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pending_pengajuan'  => PengajuanGadai::where('status','proses')->count(),
            'pending_pembayaran' => PembayaranGadai::where('status','pending')->count(),
            'pending_simpanan'   => Simpanan::where('status','pending')->count(),
            'pending_registrasi' => User::where('role','anggota')->where('account_status','pending')->count(),
        ];

        $recentPengajuan = PengajuanGadai::with('anggota','jenisBarang')
            ->where('status','proses')->latest()->take(5)->get();

        $recentPembayaran = PembayaranGadai::with('transaksi.anggota')
            ->where('status','pending')->latest()->take(5)->get();

        return view('pengurus.dashboard', compact('stats','recentPengajuan','recentPembayaran'));
    }
}
