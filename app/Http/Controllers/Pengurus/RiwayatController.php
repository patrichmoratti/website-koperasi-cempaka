<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\PembayaranGadai;
use App\Models\PengajuanGadai;
use App\Models\User;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'semua');

        $registrasi = User::where('role', 'anggota')
            ->whereIn('account_status', ['active', 'rejected'])
            ->latest('updated_at')
            ->get()
            ->map(fn ($u) => (object) [
                'type'   => 'registrasi',
                'date'   => $u->updated_at,
                'name'   => $u->name,
                'sub'    => $u->email,
                'badge'  => $u->account_status === 'active' ? 'success' : 'danger',
                'label'  => $u->account_status === 'active' ? 'Disetujui' : 'Ditolak',
                'detail' => [
                    'title'       => 'Registrasi Anggota',
                    'subtitle'    => $u->name,
                    'status'      => $u->account_status === 'active' ? 'confirmed' : 'rejected',
                    'statusLabel' => $u->account_status === 'active' ? 'Registrasi Disetujui' : 'Registrasi Ditolak',
                    'rows'        => [
                        ['Nama Lengkap',  $u->name],
                        ['Email',         $u->email],
                        ['NIK',           $u->nik ?? '-'],
                        ['No. HP',        $u->phone ?? '-'],
                        ['Tgl. Daftar',   $u->created_at->format('d M Y')],
                        ['Tgl. Diproses', $u->updated_at->format('d M Y, H:i')],
                    ],
                    'note' => $u->rejection_reason ?? null,
                    'link' => null,
                ],
            ]);

        $pembayaran = PembayaranGadai::with(['transaksi.anggota'])
            ->whereIn('status', ['confirmed', 'rejected'])
            ->latest('confirmed_at')
            ->get()
            ->map(fn ($p) => (object) [
                'type'   => 'pembayaran',
                'date'   => $p->confirmed_at ?? $p->submitted_at,
                'name'   => $p->transaksi?->anggota?->name ?? '-',
                'sub'    => $p->payment_type_label,
                'badge'  => $p->status === 'confirmed' ? 'success' : 'danger',
                'label'  => $p->status === 'confirmed' ? 'Dikonfirmasi' : 'Ditolak',
                'detail' => [
                    'title'       => $p->payment_type_label . ' Gadai',
                    'subtitle'    => ($p->transaksi?->anggota?->name ?? '-') . ' · ' . ($p->transaksi?->reference_number ?? '-'),
                    'status'      => $p->status,
                    'statusLabel' => $p->status === 'confirmed' ? 'Pembayaran Dikonfirmasi' : 'Pembayaran Ditolak',
                    'rows'        => [
                        ['Anggota',         $p->transaksi?->anggota?->name ?? '-'],
                        ['No. Referensi',   $p->transaksi?->reference_number ?? '-'],
                        ['Tipe Pembayaran', $p->payment_type_label],
                        ['Jumlah',          'Rp ' . number_format($p->amount, 0, ',', '.')],
                        ['Tgl. Diajukan',   $p->submitted_at?->format('d M Y, H:i') ?? '-'],
                        ['Tgl. Diproses',   $p->confirmed_at?->format('d M Y, H:i') ?? '-'],
                    ],
                    'note' => $p->rejection_reason ?? null,
                    'link' => null,
                ],
            ]);

        $pengajuan = PengajuanGadai::with(['anggota', 'jenisBarang'])
            ->whereIn('status', ['diterima', 'ditolak'])
            ->latest('processed_at')
            ->get()
            ->map(fn ($p) => (object) [
                'type'   => 'pengajuan',
                'date'   => $p->processed_at ?? $p->submitted_at,
                'name'   => $p->anggota?->name ?? '-',
                'sub'    => $p->jenisBarang?->name ?? '-',
                'badge'  => $p->status === 'diterima' ? 'success' : 'danger',
                'label'  => $p->status_label,
                'detail' => [
                    'title'       => 'Pengajuan Gadai',
                    'subtitle'    => ($p->anggota?->name ?? '-') . ' · ' . ($p->jenisBarang?->name ?? '-'),
                    'status'      => $p->status === 'diterima' ? 'confirmed' : 'rejected',
                    'statusLabel' => $p->status === 'diterima' ? 'Pengajuan Diterima' : 'Pengajuan Ditolak',
                    'rows'        => [
                        ['Anggota',           $p->anggota?->name ?? '-'],
                        ['Jenis Barang',      $p->jenisBarang?->name ?? '-'],
                        ['Brand/Merek',       $p->brand_name ?? '-'],
                        ['Kondisi',           $p->condition ?? '-'],
                        ['Pinjaman Diajukan', 'Rp ' . number_format($p->loan_request_amount, 0, ',', '.')],
                        ['Tgl. Pengajuan',    $p->submitted_at?->format('d M Y') ?? '-'],
                        ['Tgl. Diproses',     $p->processed_at?->format('d M Y, H:i') ?? '-'],
                    ],
                    'note' => $p->reason_if_rejected ?? null,
                    'link' => null,
                ],
            ]);

        $semua = $registrasi->merge($pembayaran)->merge($pengajuan)
            ->sortByDesc('date')
            ->values();

        return view('pengurus.riwayat.index', compact('tab', 'registrasi', 'pembayaran', 'pengajuan', 'semua'));
    }
}