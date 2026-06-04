<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KoperasiInfo extends Model
{
    protected $table = 'koperasi_info';

    protected $fillable = [
        'name', 'vision', 'mission', 'address', 'phone', 'email',
        'bank_name', 'bank_account_number', 'bank_account_name',
        'terms_and_conditions', 'logo_path',
    ];

    public static function getInstance(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'name'               => 'Koperasi Simpan Pinjam Cempaka',
            'bank_name'          => 'BCA',
            'bank_account_number'=> '1234567890',
            'bank_account_name'  => 'KSP Cempaka',
        ]);
    }
}
