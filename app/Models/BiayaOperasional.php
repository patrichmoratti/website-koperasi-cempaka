<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaOperasional extends Model
{
    protected $table = 'biaya_operasional';

    protected $fillable = ['date', 'category', 'amount', 'description', 'recorded_by'];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'float',
    ];

    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }

    public static function categories(): array
    {
        return ['Gaji', 'Listrik', 'Air', 'Internet', 'Sewa', 'ATK', 'Operasional', 'Lain-lain'];
    }
}
