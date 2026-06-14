<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembayaranGadai;
use App\Models\Simpanan;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SimpananController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function index(Request $request)
    {
        $search = $request->search;
        $jenis  = $request->jenis;
        $month  = $request->month;
        $year   = $request->year;

        $items = collect();

        if (!$jenis || str_starts_with($jenis, 'simpanan')) {
            $simpananQuery = Simpanan::with(['anggota', 'confirmedBy'])->confirmed();

            if ($jenis === 'simpanan_pokok') $simpananQuery->pokok();
            if ($jenis === 'simpanan_wajib') $simpananQuery->wajib();
            if ($search) {
                $simpananQuery->whereHas('anggota', fn($q) => $q->where('name', 'like', "%{$search}%"));
            }
            if ($year)  $simpananQuery->whereYear('confirmed_at', $year);
            if ($month) $simpananQuery->whereMonth('confirmed_at', $month);

            $items = $items->concat($simpananQuery->get()->map(fn($s) => [
                'anggota'      => $s->anggota,
                'jenis'        => $s->type_label,
                'badge'        => $s->type === 'pokok' ? 'info' : 'primary',
                'keterangan'   => 'Periode ' . $s->period_label,
                'amount'       => $s->amount,
                'confirmed_by' => $s->confirmedBy,
                'confirmed_at' => $s->confirmed_at,
                'detail_route' => null,
            ]));
        }

        if (!$jenis || str_starts_with($jenis, 'gadai')) {
            $pembayaranQuery = PembayaranGadai::with(['transaksi.anggota', 'transaksi.jenisBarang', 'confirmedBy'])->confirmed();

            if ($jenis === 'gadai_bunga') $pembayaranQuery->where('payment_type', 'bunga');
            if ($jenis === 'gadai_tebus') $pembayaranQuery->where('payment_type', 'tebus');
            if ($search) {
                $pembayaranQuery->whereHas('transaksi.anggota', fn($q) => $q->where('name', 'like', "%{$search}%"));
            }
            if ($year)  $pembayaranQuery->whereYear('confirmed_at', $year);
            if ($month) $pembayaranQuery->whereMonth('confirmed_at', $month);

            $items = $items->concat($pembayaranQuery->get()->map(fn($p) => [
                'anggota'      => $p->transaksi?->anggota,
                'jenis'        => 'Gadai - ' . $p->payment_type_label,
                'badge'        => $p->payment_type === 'bunga' ? 'warning' : 'success',
                'keterangan'   => ($p->transaksi?->jenisBarang?->name ?? '-') . ' · ' . ($p->transaksi?->reference_number ?? '-'),
                'amount'       => $p->amount,
                'confirmed_by' => $p->confirmedBy,
                'confirmed_at' => $p->confirmed_at,
                'detail_route' => $p->transaksi ? route('admin.gadai.transaksi', $p->transaksi) : null,
            ]));
        }

        $items = $items->sortByDesc(fn($i) => $i['confirmed_at'])->values();

        $perPage = 20;
        $page    = $request->get('page', 1);
        $pembayaran = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $summary = [
            'total_simpanan' => Simpanan::confirmed()->sum('amount'),
            'total_gadai'    => PembayaranGadai::confirmed()->sum('amount'),
        ];

        return view('admin.simpanan.index', compact('pembayaran', 'summary'));
    }

    public function exportExcel()
    {
        return $this->reportService->pembayaranExcel();
    }
}