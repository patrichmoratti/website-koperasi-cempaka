<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    protected $table = 'simpanan';

    protected $fillable = [
        'anggota_id', 'type', 'amount', 'period_month', 'period_year',
        'transfer_proof_path', 'status', 'submitted_at',
        'confirmed_by', 'confirmed_at', 'rejection_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'amount'       => 'float',
    ];

    public function anggota()     { return $this->belongsTo(User::class, 'anggota_id'); }
    public function confirmedBy() { return $this->belongsTo(User::class, 'confirmed_by'); }

    public function scopePending($q)   { return $q->where('status', 'pending'); }
    public function scopeConfirmed($q) { return $q->where('status', 'confirmed'); }
    public function scopePokok($q)     { return $q->where('type', 'pokok'); }
    public function scopeWajib($q)     { return $q->where('type', 'wajib'); }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'pokok' => 'Simpanan Pokok',
            'wajib' => 'Simpanan Wajib',
            default => $this->type,
        };
    }

    public function getPeriodLabelAttribute(): string
    {
        if (!$this->period_month || !$this->period_year) return '-';
        return \Carbon\Carbon::create($this->period_year, $this->period_month)->isoFormat('MMMM YYYY');
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
