<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembayaranGadai;
use App\Models\BiayaOperasional;
use App\Models\TransaksiGadai;
use App\Services\ReportService;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function keuangan(Request $request)
    {
        $year  = (int) ($request->year  ?? now()->year);
        $month = $request->month ? (int) $request->month : null;

        $queryIncome = PembayaranGadai::where('status','confirmed')->whereYear('confirmed_at',$year);
        $queryBiaya  = BiayaOperasional::whereYear('date',$year);

        if ($month) {
            $queryIncome->whereMonth('confirmed_at',$month);
            $queryBiaya->whereMonth('date',$month);
        }

        $pendapatan   = $queryIncome->get();
        $biaya        = $queryBiaya->get();
        $totalIncome  = $pendapatan->sum('amount');
        $totalExpense = $biaya->sum('amount');
        $laba         = $totalIncome - $totalExpense;

        // Monthly breakdown for chart
        $monthlyIncome  = [];
        $monthlyExpense = [];
        $labels         = [];
        for ($m = 1; $m <= 12; $m++) {
            $labels[]         = \Carbon\Carbon::create($year,$m)->isoFormat('MMM');
            $monthlyIncome[]  = PembayaranGadai::where('status','confirmed')->whereYear('confirmed_at',$year)->whereMonth('confirmed_at',$m)->sum('amount');
            $monthlyExpense[] = BiayaOperasional::whereYear('date',$year)->whereMonth('date',$m)->sum('amount');
        }

        return view('admin.laporan.keuangan', compact('year','month','pendapatan','biaya','totalIncome','totalExpense','laba','labels','monthlyIncome','monthlyExpense'));
    }

    public function gadai(Request $request)
    {
        $query = TransaksiGadai::with(['anggota','jenisBarang']);
        if ($request->status) $query->where('status', $request->status);
        if ($request->jenis)  $query->where('jenis_barang_id', $request->jenis);
        if ($request->from)   $query->whereDate('pawn_date','>=',$request->from);
        if ($request->to)     $query->whereDate('pawn_date','<=',$request->to);

        $transaksi = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'aktif'         => TransaksiGadai::where('status','aktif')->count(),
            'selesai'       => TransaksiGadai::where('status','selesai')->count(),
            'ditebus'       => TransaksiGadai::where('status','ditebus')->count(),
            'dilelang'      => TransaksiGadai::where('status','dilelang')->count(),
        ];

        return view('admin.laporan.gadai', compact('transaksi','stats'));
    }

    public function exportKeuanganPdf(Request $request)
    {
        return $this->reportService->keuanganPdf((int)($request->year ?? now()->year), $request->month ? (int)$request->month : null);
    }

    public function exportGadaiExcel()
    {
        return $this->reportService->gadaiExcel();
    }
}
