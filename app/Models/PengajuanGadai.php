<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanGadai extends Model
{
    protected $table = 'pengajuan_gadai';

    protected $fillable = [
        'anggota_id', 'jenis_barang_id', 'brand_name', 'description',
        'weight_or_quantity', 'condition',
        'estimated_value', 'loan_request_amount',
        'item_photo_paths', 'item_video_path', 'supporting_doc_paths',
        'status', 'reason_if_rejected',
        'processed_by', 'processed_at', 'submitted_at',
    ];

    protected $casts = [
        'item_photo_paths'    => 'array',
        'supporting_doc_paths'=> 'array',
        'processed_at'        => 'datetime',
        'submitted_at'        => 'datetime',
        'estimated_value'     => 'float',
        'loan_request_amount' => 'float',
    ];

    public function anggota()      { return $this->belongsTo(User::class, 'anggota_id'); }
    public function jenisBarang()  { return $this->belongsTo(JenisBarangGadai::class, 'jenis_barang_id'); }
    public function processor()    { return $this->belongsTo(User::class, 'processed_by'); }
    public function transaksi()    { return $this->hasOne(TransaksiGadai::class, 'pengajuan_id'); }
    public function riwayat()      { return $this->morphMany(RiwayatStatus::class, 'entity', 'entity_type', 'entity_id'); }

    public function scopeProses($q)   { return $q->where('status', 'proses'); }
    public function scopeDiterima($q) { return $q->where('status', 'diterima'); }
    public function scopeDitolak($q)  { return $q->where('status', 'ditolak'); }

    public function getRefNumberAttribute(): string
    {
        $name   = $this->jenisBarang->name ?? 'ITM';
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 3));
        $date   = ($this->submitted_at ?? $this->created_at)->format('dmy');
        return $prefix . '-' . $date . '-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'proses'     => 'Menunggu Review',
            'diterima'   => 'Diterima',
            'ditolak'    => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
            default      => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'proses'     => 'warning',
            'diterima'   => 'success',
            'ditolak'    => 'danger',
            'dibatalkan' => 'gray',
            default      => 'gray',
        };
    }
}
