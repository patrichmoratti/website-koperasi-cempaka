<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TransaksiGadai;
use App\Models\PengajuanGadai;
use App\Models\Simpanan;
use App\Models\PembayaranGadai;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_anggota_aktif'  => User::where('role','anggota')->where('account_status','active')->count(),
            'total_gadai_aktif'    => TransaksiGadai::where('status','aktif')->count(),
            'total_pinjaman'       => TransaksiGadai::where('status','aktif')->sum('loan_amount'),
            'total_simpanan'       => Simpanan::where('status','confirmed')->sum('amount'),
            'pendapatan_bulan_ini' => PembayaranGadai::where('status','confirmed')->whereMonth('confirmed_at', now()->month)->whereYear('confirmed_at', now()->year)->sum('amount'),
            'pending_hari_ini'     => PengajuanGadai::where('status','proses')->whereDate('submitted_at', today())->count(),
        ];

        // Trend pendapatan 12 bulan
        $incomeByMonth = [];
        $gadaiByMonth  = [];
        $labels        = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[]       = $month->isoFormat('MMM YY');
            $incomeByMonth[] = PembayaranGadai::where('status','confirmed')
                ->whereYear('confirmed_at', $month->year)
                ->whereMonth('confirmed_at', $month->month)
                ->sum('amount');
            $gadaiByMonth[]  = PengajuanGadai::whereYear('submitted_at', $month->year)
                ->whereMonth('submitted_at', $month->month)
                ->count();
        }

        // Distribusi jenis barang gadai
        $jenisDistrib = TransaksiGadai::with('jenisBarang')
            ->where('status','aktif')
            ->get()
            ->groupBy('jenis_barang_id')
            ->map(fn($g) => ['label' => $g->first()->jenisBarang?->name ?? 'Lainnya', 'count' => $g->count()])
            ->values();

        // Simpanan pokok vs wajib per bulan
        $simpananPokok = [];
        $simpananWajib = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $simpananPokok[] = Simpanan::where('type','pokok')->where('status','confirmed')
                ->whereYear('confirmed_at', $month->year)->whereMonth('confirmed_at', $month->month)->sum('amount');
            $simpananWajib[] = Simpanan::where('type','wajib')->where('status','confirmed')
                ->whereYear('confirmed_at', $month->year)->whereMonth('confirmed_at', $month->month)->sum('amount');
        }

        $recentActivity = PengajuanGadai::with('anggota','jenisBarang')->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats','labels','incomeByMonth','gadaiByMonth','jenisDistrib','simpananPokok','simpananWajib','recentActivity'));
    }
}
