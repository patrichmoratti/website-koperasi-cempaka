<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    protected $fillable = [
        'transaction_type', 'reference_id', 'reference_type',
        'anggota_id', 'pengurus_id', 'amount', 'status',
        'description', 'metadata', 'ip_address',
    ];

    protected $casts = [
        'metadata' => 'array',
        'amount'   => 'float',
    ];

    public function anggota() { return $this->belongsTo(User::class, 'anggota_id'); }
    public function pengurus() { return $this->belongsTo(User::class, 'pengurus_id'); }

    public function getTransactionTypeLabelAttribute(): string
    {
        return match($this->transaction_type) {
            'pengajuan_gadai'     => 'Pengajuan Gadai',
            'approval_gadai'      => 'Approval Gadai',
            'pembayaran_gadai'    => 'Pembayaran Gadai',
            'pelunasan_gadai'     => 'Pelunasan Gadai',
            'pembayaran_simpanan' => 'Pembayaran Simpanan',
            'approval_simpanan'   => 'Approval Simpanan',
            'registrasi'          => 'Registrasi Anggota',
            default               => $this->transaction_type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'committed' ? 'Berhasil' : 'Dibatalkan';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status === 'committed' ? 'success' : 'danger';
    }
}