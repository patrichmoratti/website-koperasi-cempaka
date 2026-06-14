<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisBarangGadai;
use App\Models\PengajuanGadai;
use App\Models\RiwayatStatus;
use App\Models\TransaksiGadai;
use App\Models\User;
use App\Services\GadaiService;
use App\Services\ReportService;
use App\Services\TransactionLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GadaiController extends Controller
{
    public function __construct(
        private GadaiService $gadaiService,
        private ReportService $reportService,
        private TransactionLogService $transactionLog,
    ) {}

    public function index(Request $request)
    {
        $tab    = $request->get('tab', 'aktif');
        $search = $request->get('search');

        $transaksi = TransaksiGadai::with(['anggota','jenisBarang','pengajuan','pembayaran'])
            ->when($tab === 'aktif',   fn($q) => $q->whereIn('status', ['aktif', 'menunggu_lelang']))
            ->when($tab === 'selesai', fn($q) => $q->whereIn('status', ['selesai','ditebus','dilelang']))
            ->when($search, fn($q) => $q->whereHas('anggota', fn($sq) => $sq->where('name','like',"%{$search}%")))
            ->latest()->paginate(15)->withQueryString();

        $counts = [
            'aktif'   => TransaksiGadai::whereIn('status', ['aktif', 'menunggu_lelang'])->count(),
            'selesai' => TransaksiGadai::whereIn('status', ['selesai','ditebus','dilelang'])->count(),
        ];

        $anggotaList     = User::where('role','anggota')->where('account_status','active')->orderBy('name')->get(['id','name','email']);
        $jenisBarangList = JenisBarangGadai::active()->orderBy('name')->get(['id','name','category']);

        return view('admin.gadai.index', compact('tab','transaksi','counts','search','anggotaList','jenisBarangList'));
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'anggota_id'         => 'required|exists:users,id',
            'jenis_barang_id'    => 'required|exists:jenis_barang_gadai,id',
            'brand_name'         => 'required|string|max:100',
            'condition'          => 'required|string',
            'description'        => 'nullable|string|max:1000',
            'weight_or_quantity' => 'nullable|string|max:50',
            'appraisal_value'    => 'required|numeric|min:1',
            'loan_amount'        => 'required|numeric|min:1',
            'warehouse_location' => 'nullable|string|max:100',
            'pawn_date'          => 'required|date',
            'photos.*'           => 'nullable|image|max:5120',
        ]);

        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('gadai/photos', 'public');
            }
        }

        $logData = [
            'transaction_type' => 'pengajuan_gadai',
            'reference_id'     => $request->anggota_id,
            'reference_type'   => 'transaksi_gadai',
            'anggota_id'       => $request->anggota_id,
            'amount'           => $request->loan_amount,
            'description'      => "Transaksi gadai manual dibuat oleh admin",
        ];

        try {
            $ref = DB::transaction(function () use ($request, $photoPaths, $logData) {
                $pawnDate = Carbon::parse($request->pawn_date);
                $dueDate  = $pawnDate->copy()->addMonths(4);

                $pengajuan = PengajuanGadai::create([
                    'anggota_id'          => $request->anggota_id,
                    'jenis_barang_id'     => $request->jenis_barang_id,
                    'brand_name'          => $request->brand_name,
                    'description'         => $request->description,
                    'weight_or_quantity'  => $request->weight_or_quantity,
                    'condition'           => $request->condition,
                    'estimated_value'     => $request->appraisal_value,
                    'loan_request_amount' => $request->loan_amount,
                    'item_photo_paths'    => $photoPaths ?: null,
                    'status'              => 'diterima',
                    'processed_by'        => auth()->id(),
                    'processed_at'        => now(),
                    'submitted_at'        => now(),
                ]);

                $transaksi = TransaksiGadai::create([
                    'anggota_id'         => $request->anggota_id,
                    'pengajuan_id'       => $pengajuan->id,
                    'jenis_barang_id'    => $request->jenis_barang_id,
                    'item_description'   => $request->description,
                    'item_photo_paths'   => $photoPaths ?: null,
                    'appraisal_value'    => $request->appraisal_value,
                    'loan_amount'        => $request->loan_amount,
                    'interest_rate'      => 8.00,
                    'pawn_date'          => $pawnDate,
                    'due_date'           => $dueDate,
                    'status'             => 'aktif',
                    'warehouse_location' => $request->warehouse_location,
                    'reference_number'   => TransaksiGadai::generateReference(),
                ]);

                RiwayatStatus::record('transaksi_gadai', $transaksi->id, null, 'aktif');

                $this->transactionLog->log(array_merge($logData, [
                    'reference_id' => $transaksi->id,
                    'description'  => "Transaksi gadai manual {$transaksi->reference_number} dibuat oleh admin",
                ]));

                return $transaksi->reference_number;
            });
        } catch (\Throwable $e) {
            $this->transactionLog->logFailure($logData, $e);
            throw $e;
        }

        return redirect()->route('admin.gadai.index', ['tab' => 'aktif'])
            ->with('success', "Transaksi gadai manual {$ref} berhasil dibuat.");
    }

    public function showPengajuan(PengajuanGadai $pengajuan)
    {
        $pengajuan->load(['anggota','jenisBarang','processor']);
        return view('admin.gadai.pengajuan-detail', compact('pengajuan'));
    }

    public function approvePengajuan(PengajuanGadai $pengajuan)
    {
        $this->gadaiService->terimaPengajuan($pengajuan);
        return redirect()->route('admin.gadai.index', ['tab' => 'diterima'])
            ->with('success', 'Pengajuan diterima. Anggota akan membawa barang ke koperasi untuk penilaian.');
    }

    public function nilaiBarang(Request $request, PengajuanGadai $pengajuan)
    {
        $maxLoan = $pengajuan->jenisBarang->maxLoanAmount($pengajuan->estimated_value);

        $request->validate([
            'loan_amount'        => "required|numeric|min:1|max:{$maxLoan}",
            'warehouse_location' => 'nullable|string|max:255',
        ], [
            'loan_amount.max' => 'Pinjaman disetujui maksimal Rp ' . number_format($maxLoan, 0, ',', '.') . ' (' . $pengajuan->jenisBarang->max_loan_percentage . '% dari nilai taksiran).',
        ]);

        $transaksi = $this->gadaiService->nilaiPengajuan($pengajuan, $request->only('loan_amount','warehouse_location'));

        return redirect()->route('admin.gadai.transaksi', $transaksi->id)
            ->with('success', "Transaksi gadai {$transaksi->reference_number} berhasil dibuat.");
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