<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LelangBarang extends Model
{
    protected $table = 'lelang_barang';

    protected $fillable = [
        'transaksi_gadai_id', 'auction_date', 'auction_value', 'notes', 'processed_by',
    ];

    protected $casts = [
        'auction_date'  => 'date',
        'auction_value' => 'float',
    ];

    public function transaksi()   { return $this->belongsTo(TransaksiGadai::class, 'transaksi_gadai_id'); }
    public function processor()   { return $this->belongsTo(User::class, 'processed_by'); }
}
