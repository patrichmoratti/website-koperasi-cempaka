<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanGadai;
use App\Models\TransaksiGadai;
use App\Services\GadaiService;
use App\Services\ReportService;
use Illuminate\Http\Request;

class GadaiController extends Controller
{
    public function __construct(private GadaiService $gadaiService, private ReportService $reportService) {}

    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'pengajuan');
        $search = $request->get('search');

        $pengajuan = PengajuanGadai::with(['anggota','jenisBarang'])
            ->when($tab === 'pengajuan', fn($q) => $q->where('status','proses'))
            ->when($tab === 'diterima', fn($q) => $q->where('status','diterima'))
            ->when($tab === 'ditolak', fn($q) => $q->where('status','ditolak'))
            ->when($search, fn($q) => $q->whereHas('anggota', fn($sq) => $sq->where('name','like',"%{$search}%")))
            ->latest()->paginate(15)->withQueryString();

        $transaksi = TransaksiGadai::with(['anggota','jenisBarang'])
            ->when($tab === 'aktif', fn($q) => $q->where('status','aktif'))
            ->when($tab === 'selesai', fn($q) => $q->whereIn('status',['selesai','ditebus','dilelang']))
            ->when($tab === 'semua', fn($q) => $q)
            ->when($search && in_array($tab,['aktif','selesai','semua']), fn($q) => $q->whereHas('anggota', fn($sq) => $sq->where('name','like',"%{$search}%")))
            ->latest()->paginate(15)->withQueryString();

        $counts = [
            'pengajuan' => PengajuanGadai::where('status','proses')->count(),
            'aktif'     => TransaksiGadai::where('status','aktif')->count(),
            'selesai'   => TransaksiGadai::whereIn('status',['selesai','ditebus','dilelang'])->count(),
            'diterima'  => PengajuanGadai::where('status','diterima')->count(),
            'ditolak'   => PengajuanGadai::where('status','ditolak')->count(),
        ];

        return view('admin.gadai.index', compact('tab','pengajuan','transaksi','counts','search'));
    }

    public function showPengajuan(PengajuanGadai $pengajuan)
    {
        $pengajuan->load(['anggota','jenisBarang','processor']);
        return view('admin.gadai.pengajuan-detail', compact('pengajuan'));
    }

    public function approvePengajuan(Request $request, PengajuanGadai $pengajuan)
    {
        $request->validate([
            'appraisal_value'    => 'required|numeric|min:1',
            'loan_amount'        => 'required|numeric|min:1',
            'warehouse_location' => 'nullable|string|max:255',
        ]);

        $transaksi = $this->gadaiService->approvePengajuan($pengajuan, $request->only('appraisal_value','loan_amount','warehouse_location'));

        return redirect()->route('admin.gadai.transaksi', $transaksi->id)
            ->with('success', "Pengajuan disetujui. Transaksi {$transaksi->reference_number} dibuat.");
    }

    public function rejectPengajuan(Request $request, PengajuanGadai $pengajuan)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $this->gadaiService->rejectPengajuan($pengajuan, $request->reason);
        return back()->with('success', 'Pengajuan ditolak.');
    }

    public function showTransaksi(TransaksiGadai $transaksi)
    {
        $transaksi->load(['anggota','jenisBarang','pengajuan','pembayaran.confirmedBy','lelang']);
        return view('admin.gadai.transaksi-detail', compact('transaksi'));
    }

    public function markMenungguLelang(TransaksiGadai $transaksi)
    {
        $this->gadaiService->markMenungguLelang($transaksi);
        return back()->with('success', 'Status diubah ke menunggu lelang.');
    }

    public function prosesLelang(Request $request, TransaksiGadai $transaksi)
    {
        $request->validate([
            'auction_date'  => 'required|date',
            'auction_value' => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string|max:500',
        ]);
        $this->gadaiService->markAsLelang($transaksi, $request->only('auction_date','auction_value','notes'));
        return back()->with('success', 'Barang diproses untuk lelang.');
    }

    public function exportPdf(TransaksiGadai $transaksi)
    {
        return $this->reportService->gadaiPdf($transaksi);
    }
}
