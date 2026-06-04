<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranGadai extends Model
{
    protected $table = 'pembayaran_gadai';

    protected $fillable = [
        'transaksi_gadai_id', 'payment_type', 'month_covered',
        'paid_months', 'amount', 'transfer_proof_path',
        'status', 'submitted_at', 'confirmed_by', 'confirmed_at', 'rejection_reason',
    ];

    protected $casts = [
        'paid_months'  => 'array',
        'submitted_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'amount'       => 'float',
    ];

    public function transaksi()    { return $this->belongsTo(TransaksiGadai::class, 'transaksi_gadai_id'); }
    public function confirmedBy()  { return $this->belongsTo(User::class, 'confirmed_by'); }

    public function scopePending($q)    { return $q->where('status', 'pending'); }
    public function scopeConfirmed($q)  { return $q->where('status', 'confirmed'); }

    public function getPaymentTypeLabelAttribute(): string
    {
        return match($this->payment_type) {
            'bunga' => 'Bunga',
            'tebus' => 'Tebus',
            default => $this->payment_type,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'warning',
            'confirmed' => 'success',
            'rejected'  => 'danger',
            default     => 'gray',
        };
    }
}
