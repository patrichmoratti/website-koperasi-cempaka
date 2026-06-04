<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TransaksiGadai extends Model
{
    protected $table = 'transaksi_gadai';

    protected $fillable = [
        'anggota_id', 'pengajuan_id', 'jenis_barang_id',
        'item_description', 'item_photo_paths',
        'appraisal_value', 'loan_amount', 'interest_rate',
        'pawn_date', 'due_date', 'status',
        'warehouse_location', 'reference_number',
    ];

    protected $casts = [
        'item_photo_paths' => 'array',
        'pawn_date'        => 'date',
        'due_date'         => 'date',
        'appraisal_value'  => 'float',
        'loan_amount'      => 'float',
        'interest_rate'    => 'float',
    ];

    public function anggota()      { return $this->belongsTo(User::class, 'anggota_id'); }
    public function pengajuan()    { return $this->belongsTo(PengajuanGadai::class, 'pengajuan_id'); }
    public function jenisBarang()  { return $this->belongsTo(JenisBarangGadai::class, 'jenis_barang_id'); }
    public function pembayaran()   { return $this->hasMany(PembayaranGadai::class, 'transaksi_gadai_id'); }
    public function lelang()       { return $this->hasOne(LelangBarang::class, 'transaksi_gadai_id'); }
    public function riwayat()      { return $this->morphMany(RiwayatStatus::class, 'entity', 'entity_type', 'entity_id'); }

    public function scopeAktif($q)           { return $q->where('status', 'aktif'); }
    public function scopeSelesai($q)         { return $q->whereIn('status', ['selesai', 'ditebus', 'dilelang']); }
    public function scopeMenungguLelang($q)  { return $q->where('status', 'menunggu_lelang'); }

    public function monthlyInterest(): float
    {
        return $this->loan_amount * ($this->interest_rate / 100);
    }

    public function totalRedemption(int $extraMonths = 0): float
    {
        $overdue = max(0, $this->overduePaidMonths());
        return $this->loan_amount + (($overdue + $extraMonths) * $this->monthlyInterest());
    }

    public function overduePaidMonths(): int
    {
        $paidMonths = $this->pembayaran()
            ->where('payment_type', 'bunga')
            ->where('status', 'confirmed')
            ->get()
            ->flatMap(fn($p) => $p->paid_months ?? [])
            ->unique()
            ->count();

        $monthsActive = (int) now()->startOfMonth()->diffInMonths($this->pawn_date->startOfMonth());
        return max(0, $monthsActive - $paidMonths);
    }

    public function totalInterestPaid(): float
    {
        return (float) $this->pembayaran()
            ->where('payment_type', 'bunga')
            ->where('status', 'confirmed')
            ->sum('amount');
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && $this->status === 'aktif';
    }

    public function daysUntilDue(): int
    {
        return (int) now()->diffInDays($this->due_date, false);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'diproses'       => 'Diproses',
            'verifikasi'     => 'Verifikasi',
            'aktif'          => 'Aktif',
            'selesai'        => 'Selesai',
            'menunggu_lelang'=> 'Menunggu Lelang',
            'dilelang'       => 'Dilelang',
            'ditebus'        => 'Ditebus',
            default          => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'aktif'          => 'success',
            'diproses','verifikasi' => 'warning',
            'menunggu_lelang'=> 'danger',
            'ditebus','selesai' => 'info',
            'dilelang'       => 'gray',
            default          => 'gray',
        };
    }

    public static function generateReference(): string
    {
        return 'TRX-' . strtoupper(now()->format('Ymd')) . '-' . strtoupper(substr(uniqid(), -6));
    }
}
