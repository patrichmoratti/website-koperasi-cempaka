<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\ShuDistribution;
use App\Models\Simpanan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $gadaiAktif  = $user->transaksiGadai()->where('status','aktif')->with('jenisBarang')->get();
        $totalGadai  = $gadaiAktif->count();
        $totalPinjaman = $gadaiAktif->sum('loan_amount');

        $totalSimpanan = $user->totalSimpanan();

        $shuTahunIni = ShuDistribution::whereHas('period', fn($q) => $q->where('year', now()->year)->where('status','published'))
            ->where('anggota_id', $user->id)
            ->sum('total_shu_received');

        $tagihan = 0;
        foreach ($gadaiAktif as $t) {
            $tagihan += $t->monthlyInterest();
        }

        $recentNotif = $user->notifikasi()->latest()->take(3)->get();

        return view('anggota.dashboard', compact(
            'gadaiAktif','totalGadai','totalPinjaman','totalSimpanan','shuTahunIni','tagihan','recentNotif'
        ));
    }
}
