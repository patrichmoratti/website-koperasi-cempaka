<?php

namespace App\Services;

use App\Models\PengajuanGadai;
use App\Models\TransaksiGadai;
use App\Models\PembayaranGadai;
use App\Models\LelangBarang;
use App\Models\RiwayatStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GadaiService
{
    public function __construct(private NotifikasiService $notif) {}

    public function approvePengajuan(PengajuanGadai $pengajuan, array $data): TransaksiGadai
    {
        return DB::transaction(function () use ($pengajuan, $data) {
            $pengajuan->update([
                'status'       => 'diterima',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            RiwayatStatus::record('pengajuan_gadai', $pengajuan->id, 'proses', 'diterima');

            $pawnDate = Carbon::today();
            $dueDate  = $pawnDate->copy()->addMonths(4);

            $transaksi = TransaksiGadai::create([
                'anggota_id'       => $pengajuan->anggota_id,
                'pengajuan_id'     => $pengajuan->id,
                'jenis_barang_id'  => $pengajuan->jenis_barang_id,
                'item_description' => $pengajuan->description,
                'item_photo_paths' => $pengajuan->item_photo_paths,
                'appraisal_value'  => $data['appraisal_value'],
                'loan_amount'      => $data['loan_amount'],
                'interest_rate'    => 8.00,
                'pawn_date'        => $pawnDate,
                'due_date'         => $dueDate,
                'status'           => 'aktif',
                'warehouse_location' => $data['warehouse_location'] ?? null,
                'reference_number' => TransaksiGadai::generateReference(),
            ]);

            RiwayatStatus::record('transaksi_gadai', $transaksi->id, null, 'aktif');
            $this->notif->pengajuanDiterima($pengajuan->anggota_id, $transaksi->reference_number);

            return $transaksi;
        });
    }

    public function rejectPengajuan(PengajuanGadai $pengajuan, string $reason): void
    {
        DB::transaction(function () use ($pengajuan, $reason) {
            $pengajuan->update([
                'status'            => 'ditolak',
                'reason_if_rejected'=> $reason,
                'processed_by'      => auth()->id(),
                'processed_at'      => now(),
            ]);
            RiwayatStatus::record('pengajuan_gadai', $pengajuan->id, 'proses', 'ditolak', null, $reason);
            $this->notif->pengajuanDitolak($pengajuan->anggota_id, $reason);
        });
    }

    public function confirmPembayaran(PembayaranGadai $pembayaran): void
    {
        DB::transaction(function () use ($pembayaran) {
            $pembayaran->update([
                'status'       => 'confirmed',
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now(),
            ]);

            $transaksi = $pembayaran->transaksi;

            if ($pembayaran->payment_type === 'tebus') {
                $transaksi->update(['status' => 'ditebus']);
                RiwayatStatus::record('transaksi_gadai', $transaksi->id, 'aktif', 'ditebus');
            }

            $this->notif->pembayaranDikonfirmasi(
                $transaksi->anggota_id,
                $pembayaran->payment_type,
                $transaksi->reference_number
            );
        });
    }

    public function rejectPembayaran(PembayaranGadai $pembayaran, string $reason): void
    {
        $pembayaran->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'confirmed_by'     => auth()->id(),
            'confirmed_at'     => now(),
        ]);
    }

    public function markAsLelang(TransaksiGadai $transaksi, array $data): LelangBarang
    {
        return DB::transaction(function () use ($transaksi, $data) {
            $transaksi->update(['status' => 'dilelang']);
            RiwayatStatus::record('transaksi_gadai', $transaksi->id, 'menunggu_lelang', 'dilelang', null, $data['notes'] ?? null);

            $lelang = LelangBarang::create([
                'transaksi_gadai_id' => $transaksi->id,
                'auction_date'       => $data['auction_date'],
                'auction_value'      => $data['auction_value'] ?? null,
                'notes'              => $data['notes'] ?? null,
                'processed_by'       => auth()->id(),
            ]);

            $this->notif->send(
                $transaksi->anggota_id, 'mendesak',
                'Barang Gadai Dilelang',
                "Barang gadai {$transaksi->reference_number} telah diproses untuk lelang karena melewati batas waktu.",
                'transaksi_gadai', $transaksi->id
            );

            return $lelang;
        });
    }

    public function markMenungguLelang(TransaksiGadai $transaksi): void
    {
        DB::transaction(function () use ($transaksi) {
            $transaksi->update(['status' => 'menunggu_lelang']);
            RiwayatStatus::record('transaksi_gadai', $transaksi->id, 'aktif', 'menunggu_lelang');
            $this->notif->send(
                $transaksi->anggota_id, 'mendesak',
                'Gadai Memasuki Status Lelang',
                "Gadai {$transaksi->reference_number} telah melewati jatuh tempo dan akan dilelang jika tidak segera ditebus.",
                'transaksi_gadai', $transaksi->id
            );
        });
    }
}
