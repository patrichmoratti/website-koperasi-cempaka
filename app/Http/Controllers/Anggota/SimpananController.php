<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Simpanan;
use App\Models\KoperasiInfo;
use App\Models\PembayaranGadai;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $summary = [
            'pokok' => $user->totalSimpananPokok(),
            'wajib' => $user->totalSimpananWajib(),
            'total' => $user->totalSimpanan(),
        ];

        $gadaiAktif   = $user->transaksiGadai()->where('status','aktif')->with(['jenisBarang','pembayaran'])->get();
        $tagihanBunga = $gadaiAktif->sum(fn($t) => $t->monthlyInterest());
        $info         = KoperasiInfo::getInstance();

        // Combined riwayat pembayaran (simpanan + bunga/tebus gadai)
        $riwayat = collect();

        $statusLabels = ['pending' => 'Menunggu Konfirmasi', 'confirmed' => 'Dikonfirmasi', 'rejected' => 'Ditolak'];

        $user->simpanan()->latest()->get()->each(function ($s) use (&$riwayat, $statusLabels) {
            $riwayat->push([
                'type'     => 'simpanan',
                'title'    => $s->type_label,
                'sub'      => $s->period_label,
                'amount'   => $s->amount,
                'status'   => $s->status,
                'color'    => $s->status_color,
                'date_fmt' => $s->submitted_at->format('d M Y'),
                'ts'       => $s->confirmed_at?->timestamp ?? $s->submitted_at->timestamp,
                'detail'   => [
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
                ],
            ]);
        });

        PembayaranGadai::whereHas('transaksi', fn($q) => $q->where('anggota_id', $user->id))
            ->with('transaksi.jenisBarang')
            ->latest('submitted_at')
            ->get()
            ->each(function ($p) use (&$riwayat, $statusLabels) {
                $months = $p->paid_months ?? [];
                $riwayat->push([
                    'type'     => 'pembayaran',
                    'title'    => 'Bayar ' . $p->payment_type_label,
                    'sub'      => $p->transaksi?->reference_number ?? '-',
                    'amount'   => $p->amount,
                    'status'   => $p->status,
                    'color'    => $p->status_color,
                    'date_fmt' => $p->submitted_at?->format('d M Y') ?? '-',
                    'ts'       => $p->confirmed_at?->timestamp ?? $p->submitted_at?->timestamp ?? 0,
                    'detail'   => [
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
                    ],
                ]);
            });

        $riwayat = $riwayat->sortByDesc('ts')->values();

        $riwayatProses    = $riwayat->where('status', 'pending')->count();
        $riwayatVerified  = $riwayat->whereIn('status', ['confirmed', 'rejected'])->count();

        return view('anggota.simpanan.index', compact(
            'summary', 'gadaiAktif', 'tagihanBunga', 'info',
            'riwayat', 'riwayatProses', 'riwayatVerified'
        ));
    }

    public function bayarForm()
    {
        $info = KoperasiInfo::getInstance();
        return view('anggota.simpanan.bayar', compact('info'));
    }

    public function bayarStore(Request $request)
    {
        $request->validate([
            'type'           => 'required|in:pokok,wajib',
            'period_month'   => 'required_if:type,pokok|nullable|integer|min:1|max:12',
            'period_year'    => 'required_if:type,pokok|nullable|integer|min:2020|max:2099',
            'amount'         => ['required', 'numeric', $request->type === 'pokok' ? 'min:50000' : 'min:10000'],
            'transfer_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $proofPath = $request->file('transfer_proof')->store('bukti-simpanan', 'public');

        $simpanan = Simpanan::create([
            'anggota_id'          => auth()->id(),
            'type'                => $request->type,
            'period_month'        => $request->period_month,
            'period_year'         => $request->period_year,
            'amount'              => $request->amount,
            'transfer_proof_path' => $proofPath,
            'status'              => 'pending',
            'submitted_at'        => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success'  => true,
                'title'    => 'Pembayaran Terkirim!',
                'subtitle' => 'Bukti simpanan menunggu konfirmasi pengurus',
                'rows'     => array_values(array_filter([
                    ['Tipe Simpanan', $simpanan->type_label],
                    $simpanan->type === 'pokok' ? ['Periode', $simpanan->period_label] : null,
                    ['Jumlah Transfer', 'Rp ' . number_format($simpanan->amount, 0, ',', '.')],
                    ['Tanggal Kirim', $simpanan->submitted_at->format('d M Y, H:i')],
                ])),
            ]);
        }

        return redirect()->route('anggota.simpanan.index')->with('success', 'Bukti simpanan berhasil dikirim. Menunggu konfirmasi.');
    }
}