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
                    'title'       => 'Gadai: ' . ($t->jenisBarang?->name ?? '-'),
                    'description' => "Ref: {$t->reference_number}",
                    'amount'      => $t->loan_amount,
                    'status'      => $t->status_label,
                    'color'       => $t->status_color,
                    'url'         => route('anggota.gadai.detail', $t->id),
                ]);
            $items = $items->merge($transaksi);
        }

        if (in_array($filter, ['semua', 'pembayaran'])) {
            $pembayaran = PembayaranGadai::whereHas('transaksi', fn($q) => $q->where('anggota_id', $user->id))
                ->with('transaksi')
                ->when($from, fn($q) => $q->whereDate('submitted_at', '>=', $from))
                ->when($to,   fn($q) => $q->whereDate('submitted_at', '<=', $to))
                ->get()
                ->map(fn($p) => (object)[
                    'type'        => 'pembayaran',
                    'date'        => $p->submitted_at,
                    'title'       => 'Bayar ' . $p->payment_type_label,
                    'description' => 'Ref: ' . ($p->transaksi?->reference_number ?? '-'),
                    'amount'      => $p->amount,
                    'status'      => ucfirst($p->status),
                    'color'       => $p->status_color,
                    'url'         => null,
                ]);
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
                    'title'       => $s->type_label,
                    'description' => $s->period_label,
                    'amount'      => $s->amount,
                    'status'      => ucfirst($s->status),
                    'color'       => $s->status_color,
                    'url'         => null,
                ]);
            $items = $items->merge($simpanan);
        }

        $riwayat = $items->sortByDesc('date')->values();

        return view('anggota.riwayat', compact('riwayat','filter'));
    }
}
