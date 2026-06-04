<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisBarangGadai extends Model
{
    protected $table = 'jenis_barang_gadai';

    protected $fillable = [
        'name', 'category', 'description',
        'base_value_per_unit', 'unit', 'max_loan_percentage',
        'image_path', 'conditions', 'brands', 'requirements', 'is_active',
    ];

    protected $casts = [
        'conditions'          => 'array',
        'brands'              => 'array',
        'requirements'        => 'array',
        'is_active'           => 'boolean',
        'base_value_per_unit' => 'float',
        'max_loan_percentage' => 'float',
    ];

    public function pengajuan()   { return $this->hasMany(PengajuanGadai::class, 'jenis_barang_id'); }
    public function transaksi()   { return $this->hasMany(TransaksiGadai::class, 'jenis_barang_id'); }

    public function scopeActive($query) { return $query->where('is_active', true); }

    public function maxLoanAmount(float $estimatedValue): float
    {
        return $estimatedValue * ($this->max_loan_percentage / 100);
    }
}
