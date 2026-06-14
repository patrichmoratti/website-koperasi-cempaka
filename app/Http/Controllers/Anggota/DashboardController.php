<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Simpanan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $gadaiAktif  = $user->transaksiGadai()->where('status','aktif')->with(['jenisBarang','pengajuan'])->get();
        $totalGadai  = $gadaiAktif->count();
        $totalPinjaman = $gadaiAktif->sum('loan_amount');

        $totalSimpanan = $user->totalSimpanan();

        $tagihan = 0;
        foreach ($gadaiAktif as $t) {
            $tagihan += $t->monthlyInterest();
        }

        $recentNotif = $user->notifikasi()->latest()->take(3)->get();

        return view('anggota.dashboard', compact(
            'gadaiAktif','totalGadai','totalPinjaman','totalSimpanan','tagihan','recentNotif'
        ));
    }
}
