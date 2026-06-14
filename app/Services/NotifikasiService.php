<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;

class NotifikasiService
{
    public function send(int $userId, string $category, string $title, string $message, ?string $entityType = null, ?int $entityId = null): Notifikasi
    {
        return Notifikasi::create([
            'user_id'             => $userId,
            'category'            => $category,
            'title'               => $title,
            'message'             => $message,
            'related_entity_type' => $entityType,
            'related_entity_id'   => $entityId,
        ]);
    }

    public function sendToAll(string $category, string $title, string $message, string $role = 'anggota'): void
    {
        $users = User::where('role', $role)->where('account_status', 'active')->get();
        $data  = $users->map(fn($u) => [
            'user_id'    => $u->id,
            'category'   => $category,
            'title'      => $title,
            'message'    => $message,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();
        Notifikasi::insert($data);
    }

    public function pengajuanDiterima(int $anggotaId, string $refNumber): void
    {
        $this->send($anggotaId, 'update', 'Pengajuan Gadai Diterima',
            "Pengajuan gadai Anda ({$refNumber}) diterima. Silakan bawa barang ke koperasi untuk penilaian.",
            'pengajuan_gadai');
    }

    public function transaksiDibuat(int $anggotaId, string $refNumber): void
    {
        $this->send($anggotaId, 'update', 'Transaksi Gadai Aktif',
            "Barang Anda telah dinilai. Transaksi gadai {$refNumber} kini aktif.",
            'transaksi_gadai');
    }

    public function pengajuanDitolak(int $anggotaId, string $reason): void
    {
        $this->send($anggotaId, 'mendesak', 'Pengajuan Gadai Ditolak',
            "Maaf, pengajuan gadai Anda ditolak. Alasan: {$reason}");
    }

    public function pembayaranDikonfirmasi(int $anggotaId, string $type, string $refNumber): void
    {
        $typeLabel = $type === 'tebus' ? 'tebus' : 'bunga';
        $this->send($anggotaId, 'update', 'Pembayaran Dikonfirmasi',
            "Pembayaran {$typeLabel} untuk gadai {$refNumber} telah dikonfirmasi.",
            'transaksi_gadai');
    }

    public function simpananDikonfirmasi(int $anggotaId, string $typeLabel): void
    {
        $this->send($anggotaId, 'update', 'Simpanan Dikonfirmasi',
            "Pembayaran {$typeLabel} Anda telah dikonfirmasi.");
    }

    public function registrasiDisetujui(int $anggotaId): void
    {
        $this->send($anggotaId, 'update', 'Pendaftaran Disetujui',
            'Selamat! Akun Anda telah diverifikasi. Anda sekarang dapat menggunakan layanan MONY.');
    }

    public function registrasiDitolak(int $anggotaId, string $reason): void
    {
        $this->send($anggotaId, 'mendesak', 'Pendaftaran Ditolak',
            "Maaf, pendaftaran Anda ditolak. Alasan: {$reason}");
    }

    public function akunDiaktifkanKembali(int $anggotaId): void
    {
        $this->send($anggotaId, 'update', 'Akun Diaktifkan Kembali',
            'Akun Anda telah diaktifkan kembali oleh admin. Anda sekarang dapat login dan menggunakan layanan MONY.');
    }

    public function jatuhTempoMendekat(int $anggotaId, string $refNumber, int $days): void
    {
        $this->send($anggotaId, 'pengingat', 'Gadai Hampir Jatuh Tempo',
            "Gadai {$refNumber} akan jatuh tempo dalam {$days} hari. Segera bayar bunga atau tebus barang Anda.",
            'transaksi_gadai');
    }
}
