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
    public function __construct(private NotifikasiService $notif, private TransactionLogService $transactionLog) {}

    /**
     * Step 1 — Pengurus/admin menerima pengajuan (belum input nilai pinjaman).
     * Anggota diminta membawa barang ke koperasi untuk dinilai ulang.
     */
    public function terimaPengajuan(PengajuanGadai $pengajuan): void
    {
        $logData = [
            'transaction_type' => 'approval_gadai',
            'reference_id'     => $pengajuan->id,
            'reference_type'   => 'pengajuan_gadai',
            'anggota_id'       => $pengajuan->anggota_id,
            'amount'           => null,
            'description'      => "Pengajuan gadai {$pengajuan->ref_number} diterima untuk dinilai",
        ];

        try {
            DB::transaction(function () use ($pengajuan, $logData) {
                $pengajuan->update([
                    'status'       => 'diterima',
                    'processed_by' => auth()->id(),
                    'processed_at' => now(),
                ]);
                RiwayatStatus::record('pengajuan_gadai', $pengajuan->id, 'proses', 'diterima');
                $this->notif->pengajuanDiterima($pengajuan->anggota_id, $pengajuan->ref_number);
                $this->transactionLog->log($logData);
            });
        } catch (\Throwable $e) {
            $this->transactionLog->logFailure($logData, $e);
            throw $e;
        }
    }

    /**
     * Step 2 — Pengurus/admin menilai barang yang sudah dibawa ke koperasi,
     * lalu membuat TransaksiGadai aktif.
     */
    public function nilaiPengajuan(PengajuanGadai $pengajuan, array $data): TransaksiGadai
    {
        $logData = [
            'transaction_type' => 'approval_gadai',
            'reference_id'     => $pengajuan->id,
            'reference_type'   => 'pengajuan_gadai',
            'anggota_id'       => $pengajuan->anggota_id,
            'amount'           => $data['loan_amount'],
            'description'      => "Pengajuan gadai {$pengajuan->ref_number} dinilai dan transaksi gadai dibuat",
        ];

        try {
            return DB::transaction(function () use ($pengajuan, $data, $logData) {
                $pawnDate = Carbon::today();
                // Awal jatuh tempo = 4 bulan sejak gadai, akan diperpanjang otomatis setiap bunga dibayar
                $dueDate  = $pawnDate->copy()->addMonths(4);

                $transaksi = TransaksiGadai::create([
                    'anggota_id'         => $pengajuan->anggota_id,
                    'pengajuan_id'       => $pengajuan->id,
                    'jenis_barang_id'    => $pengajuan->jenis_barang_id,
                    'item_description'   => $pengajuan->description,
                    'item_photo_paths'   => $pengajuan->item_photo_paths,
                    'appraisal_value'    => $pengajuan->estimated_value, // tetap = estimasi nilai merk dari pengajuan
                    'loan_amount'        => $data['loan_amount'],
                    'interest_rate'      => 8.00,
                    'pawn_date'          => $pawnDate,
                    'due_date'           => $dueDate,
                    'status'             => 'aktif',
                    'warehouse_location' => $data['warehouse_location'] ?? null,
                    'reference_number'   => TransaksiGadai::generateReference(),
                ]);

                RiwayatStatus::record('transaksi_gadai', $transaksi->id, null, 'aktif');
                $this->notif->transaksiDibuat($pengajuan->anggota_id, $transaksi->reference_number);

                $this->transactionLog->log(array_merge($logData, [
                    'reference_id'   => $transaksi->id,
                    'reference_type' => 'transaksi_gadai',
                    'description'    => "Transaksi gadai {$transaksi->reference_number} dibuat dengan pinjaman Rp " . number_format($transaksi->loan_amount, 0, ',', '.'),
                ]));

                return $transaksi;
            });
        } catch (\Throwable $e) {
            $this->transactionLog->logFailure($logData, $e);
            throw $e;
        }
    }

    public function rejectPengajuan(PengajuanGadai $pengajuan, string $reason): void
    {
        $logData = [
            'transaction_type' => 'approval_gadai',
            'reference_id'     => $pengajuan->id,
            'reference_type'   => 'pengajuan_gadai',
            'anggota_id'       => $pengajuan->anggota_id,
            'amount'           => null,
            'description'      => "Pengajuan gadai {$pengajuan->ref_number} ditolak: {$reason}",
        ];

        try {
            DB::transaction(function () use ($pengajuan, $reason, $logData) {
                $pengajuan->update([
                    'status'            => 'ditolak',
                    'reason_if_rejected'=> $reason,
                    'processed_by'      => auth()->id(),
                    'processed_at'      => now(),
                ]);
                RiwayatStatus::record('pengajuan_gadai', $pengajuan->id, 'proses', 'ditolak', null, $reason);
                $this->notif->pengajuanDitolak($pengajuan->anggota_id, $reason);
                $this->transactionLog->log($logData);
            });
        } catch (\Throwable $e) {
            $this->transactionLog->logFailure($logData, $e);
            throw $e;
        }
    }

    public function confirmPembayaran(PembayaranGadai $pembayaran): void
    {
        $transaksi = $pembayaran->transaksi;
        $logData = [
            'transaction_type' => $pembayaran->payment_type === 'tebus' ? 'pelunasan_gadai' : 'pembayaran_gadai',
            'reference_id'     => $pembayaran->id,
            'reference_type'   => 'pembayaran_gadai',
            'anggota_id'       => $transaksi->anggota_id,
            'amount'           => $pembayaran->amount,
            'description'      => "Pembayaran {$pembayaran->payment_type_label} untuk gadai {$transaksi->reference_number} dikonfirmasi",
        ];

        try {
            DB::transaction(function () use ($pembayaran, $logData) {
                $now = now();
                $pembayaran->update([
                    'status'       => 'confirmed',
                    'confirmed_by' => auth()->id(),
                    'confirmed_at' => $now,
                ]);

                $transaksi = $pembayaran->transaksi;

                if ($pembayaran->payment_type === 'tebus') {
                    $transaksi->update(['status' => 'ditebus']);
                    RiwayatStatus::record('transaksi_gadai', $transaksi->id, 'aktif', 'ditebus');
                } elseif ($pembayaran->payment_type === 'bunga') {
                    // Perpanjang jatuh tempo: 4 bulan sejak bunga terakhir dikonfirmasi.
                    // Jika tidak membayar bunga selama 4 bulan berturut setelah ini, barang dilelang.
                    $transaksi->update(['due_date' => $now->copy()->addMonths(4)]);
                }

                $this->notif->pembayaranDikonfirmasi(
                    $transaksi->anggota_id,
                    $pembayaran->payment_type,
                    $transaksi->reference_number
                );

                $this->transactionLog->log($logData);
            });
        } catch (\Throwable $e) {
            $this->transactionLog->logFailure($logData, $e);
            throw $e;
        }
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
        $logData = [
            'transaction_type' => 'approval_gadai',
            'reference_id'     => $transaksi->id,
            'reference_type'   => 'transaksi_gadai',
            'anggota_id'       => $transaksi->anggota_id,
            'amount'           => $data['auction_value'] ?? null,
            'description'      => "Barang gadai {$transaksi->reference_number} diproses untuk lelang",
        ];

        try {
            return DB::transaction(function () use ($transaksi, $data, $logData) {
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

                $this->transactionLog->log($logData);

                return $lelang;
            });
        } catch (\Throwable $e) {
            $this->transactionLog->logFailure($logData, $e);
            throw $e;
        }
    }

    public function markMenungguLelang(TransaksiGadai $transaksi): void
    {
        $logData = [
            'transaction_type' => 'approval_gadai',
            'reference_id'     => $transaksi->id,
            'reference_type'   => 'transaksi_gadai',
            'anggota_id'       => $transaksi->anggota_id,
            'amount'           => null,
            'description'      => "Gadai {$transaksi->reference_number} memasuki status menunggu lelang",
        ];

        try {
            DB::transaction(function () use ($transaksi, $logData) {
                $transaksi->update(['status' => 'menunggu_lelang']);
                RiwayatStatus::record('transaksi_gadai', $transaksi->id, 'aktif', 'menunggu_lelang');
                $this->notif->send(
                    $transaksi->anggota_id, 'mendesak',
                    'Gadai Memasuki Status Lelang',
                    "Gadai {$transaksi->reference_number} telah melewati jatuh tempo dan akan dilelang jika tidak segera ditebus.",
                    'transaksi_gadai', $transaksi->id
                );
                $this->transactionLog->log($logData);
            });
        } catch (\Throwable $e) {
            $this->transactionLog->logFailure($logData, $e);
            throw $e;
        }
    }
}