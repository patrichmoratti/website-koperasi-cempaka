<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\ShuDistribution;

class ShuController extends Controller
{
    public function index()
    {
        $distributions = ShuDistribution::with('period')
            ->where('anggota_id', auth()->id())
            ->whereHas('period', fn($q) => $q->where('status','published'))
            ->latest('id')
            ->get();

        $totalShu = $distributions->sum('total_shu_received');

        return view('anggota.shu.index', compact('distributions','totalShu'));
    }
}
