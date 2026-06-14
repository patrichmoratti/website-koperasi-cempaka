<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\TransaksiGadai;
use App\Models\PembayaranGadai;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $user   = auth()->user();
        $filter = $request->get('filter', 'semua');
        $from   = $request->from ? Carbon::parse($request->from) : null;
        $to     = $request->to   ? Carbon::parse($request->to)   : null;

        $statusLabels = ['pending' => 'Menunggu Konfirmasi', 'confirmed' => 'Dikonfirmasi', 'rejected' => 'Ditolak'];

        $items = collect();

        if (in_array($filter, ['semua', 'gadai'])) {
            $transaksi = TransaksiGadai::where('anggota_id', $user->id)
                ->with('jenisBarang')
                ->when($from, fn($q) => $q->whereDate('pawn_date', '>=', $from))
                ->when($to,   fn($q) => $q->whereDate('pawn_date', '<=', $to))
                ->get()
                ->map(fn($t) => (object)[
                    'type'        => 'gadai',
                    'date'        => $t->pawn_date,
                    'sort_date'   => $t->updated_at,
                    'title'       => 'Gadai: ' . ($t->jenisBarang?->name ?? '-'),
                    'description' => "Ref: {$t->reference_number}",
                    'amount'      => $t->loan_amount,
                    'status'      => $t->status_label,
                    'color'       => $t->status_color,
                    'url'         => route('anggota.gadai.detail', $t->id),
                    'detail'      => [
                        'title'       => 'Gadai: ' . ($t->jenisBarang?->name ?? '-'),
                        'subtitle'    => $t->reference_number,
                        'status'      => $t->status,
                        'statusLabel' => $t->status_label,
                        'rows'        => array_values(array_filter([
                            ['Jenis Barang', $t->jenisBarang?->name ?? '-'],
                            ['Nilai Taksiran', 'Rp ' . number_format($t->appraisal_value, 0, ',', '.')],
                            ['Jumlah Pinjaman', 'Rp ' . number_format($t->loan_amount, 0, ',', '.')],
                            ['Tanggal Gadai', $t->pawn_date?->format('d M Y') ?? '-'],
                            ['Jatuh Tempo', $t->due_date?->format('d M Y') ?? '-'],
                        ])),
                        'note' => null,
                        'link' => route('anggota.gadai.detail', $t->id),
                    ],
                ]);
            $items = $items->merge($transaksi);
        }

        if (in_array($filter, ['semua', 'pembayaran'])) {
            $pembayaran = PembayaranGadai::whereHas('transaksi', fn($q) => $q->where('anggota_id', $user->id))
                ->with('transaksi.jenisBarang')
                ->when($from, fn($q) => $q->whereDate('submitted_at', '>=', $from))
                ->when($to,   fn($q) => $q->whereDate('submitted_at', '<=', $to))
                ->get()
                ->map(function ($p) use ($statusLabels) {
                    $months = $p->paid_months ?? [];
                    return (object)[
                        'type'        => 'pembayaran',
                        'date'        => $p->submitted_at,
                        'sort_date'   => $p->confirmed_at ?? $p->submitted_at,
                        'title'       => 'Bayar ' . $p->payment_type_label,
                        'description' => 'Ref: ' . ($p->transaksi?->reference_number ?? '-'),
                        'amount'      => $p->amount,
                        'status'      => $statusLabels[$p->status] ?? ucfirst($p->status),
                        'color'       => $p->status_color,
                        'url'         => null,
                        'detail'      => [
                            'title'       => 'Bayar ' . $p->payment_type_label,
                            'subtitle'    => ($p->transaksi?->reference_number ?? '-') . ' — ' . ($p->transaksi?->jenisBarang?->name ?? '-'),
                            'status'      => $p->status,
                            'statusLabel' => $statusLabels[$p->status] ?? ucfirst($p->status),
                            'rows'        => array_values(array_filter([
                                ['Jenis Pembayaran', $p->payment_type_label],
                                count($months) ? ['Bulan Dibayar', count($months) . ' bulan'] : null,
                                ['Jumlah Transfer', 'Rp ' . number_format($p->amount, 0, ',', '.')],
                                ['Tanggal Kirim', $p->submitted_at?->format('d M Y, H:i') ?? '-'],
                            ])),
                            'note' => $p->status === 'confirmed'
                                ? ($p->confirmed_at ? 'Dikonfirmasi pada ' . $p->confirmed_at->format('d M Y, H:i') : null)
                                : ($p->status === 'rejected' ? $p->rejection_reason : null),
                            'link' => null,
                        ],
                    ];
                });
            $items = $items->merge($pembayaran);
        }

        if (in_array($filter, ['semua', 'simpanan'])) {
            $simpanan = Simpanan::where('anggota_id', $user->id)
                ->when($from, fn($q) => $q->whereDate('submitted_at', '>=', $from))
                ->when($to,   fn($q) => $q->whereDate('submitted_at', '<=', $to))
                ->get()
                ->map(fn($s) => (object)[
                    'type'        => 'simpanan',
                    'date'        => $s->submitted_at,
                    'sort_date'   => $s->confirmed_at ?? $s->submitted_at,
                    'title'       => $s->type_label,
                    'description' => $s->period_label,
                    'amount'      => $s->amount,
                    'status'      => $statusLabels[$s->status] ?? ucfirst($s->status),
                    'color'       => $s->status_color,
                    'url'         => null,
                    'detail'      => [
                        'title'       => $s->type_label,
                        'subtitle'    => 'Riwayat Simpanan',
                        'status'      => $s->status,
                        'statusLabel' => $statusLabels[$s->status] ?? ucfirst($s->status),
                        'rows'        => array_values(array_filter([
                            ['Tipe Simpanan', $s->type_label],
                            $s->type === 'pokok' ? ['Periode', $s->period_label] : null,
                            ['Jumlah Transfer', 'Rp ' . number_format($s->amount, 0, ',', '.')],
                            ['Tanggal Kirim', $s->submitted_at->format('d M Y, H:i')],
                        ])),
                        'note' => $s->status === 'confirmed'
                            ? ($s->confirmed_at ? 'Dikonfirmasi pada ' . $s->confirmed_at->format('d M Y, H:i') : null)
                            : ($s->status === 'rejected' ? $s->rejection_reason : null),
                        'link' => null,
                    ],
                ]);
            $items = $items->merge($simpanan);
        }

        $riwayat = $items->sortByDesc('sort_date')->values();

        return view('anggota.riwayat', compact('riwayat','filter'));
    }
}
