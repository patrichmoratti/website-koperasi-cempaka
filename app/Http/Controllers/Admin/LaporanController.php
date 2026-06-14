<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembayaranGadai;
use App\Models\PengajuanGadai;
use App\Models\Simpanan;
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

        $queryIncome    = PembayaranGadai::with('transaksi.anggota')->where('status','confirmed')->whereYear('confirmed_at',$year);
        $querySimpanan  = Simpanan::confirmed()->whereYear('confirmed_at',$year);
        $queryPengajuan = PengajuanGadai::with(['anggota','jenisBarang'])->whereYear('submitted_at',$year);

        if ($month) {
            $queryIncome->whereMonth('confirmed_at',$month);
            $querySimpanan->whereMonth('confirmed_at',$month);
            $queryPengajuan->whereMonth('submitted_at',$month);
        }

        $pendapatan     = $queryIncome->latest('confirmed_at')->get();
        $totalIncome    = $pendapatan->sum('amount');
        $totalSimpanan  = $querySimpanan->sum('amount');
        $pengajuan      = $queryPengajuan->latest('submitted_at')->get();
        $totalPengajuan = $pengajuan->sum('loan_request_amount');

        // Monthly breakdown for chart
        $monthlyIncome   = [];
        $monthlySimpanan = [];
        $labels          = [];
        for ($m = 1; $m <= 12; $m++) {
            $labels[]            = \Carbon\Carbon::create($year,$m)->isoFormat('MMM');
            $monthlyIncome[]     = PembayaranGadai::where('status','confirmed')->whereYear('confirmed_at',$year)->whereMonth('confirmed_at',$m)->sum('amount');
            $monthlySimpanan[]   = Simpanan::confirmed()->whereYear('confirmed_at',$year)->whereMonth('confirmed_at',$m)->sum('amount');
        }

        return view('admin.laporan.keuangan', compact('year','month','pendapatan','totalIncome','totalSimpanan','pengajuan','totalPengajuan','labels','monthlyIncome','monthlySimpanan'));
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
