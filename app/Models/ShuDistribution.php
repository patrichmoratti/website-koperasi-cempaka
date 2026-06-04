<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShuDistribution extends Model
{
    protected $table = 'shu_distribution';

    protected $fillable = [
        'shu_period_id', 'anggota_id',
        'total_savings', 'member_savings_proportion', 'member_jasa_modal',
        'total_interest_paid', 'member_interest_proportion', 'member_jasa_usaha',
        'total_shu_received', 'withdrawal_status', 'withdrawal_date',
    ];

    protected $casts = [
        'total_savings'             => 'float',
        'member_savings_proportion' => 'float',
        'member_jasa_modal'         => 'float',
        'total_interest_paid'       => 'float',
        'member_interest_proportion'=> 'float',
        'member_jasa_usaha'         => 'float',
        'total_shu_received'        => 'float',
        'withdrawal_date'           => 'datetime',
    ];

    public function period()  { return $this->belongsTo(ShuPeriod::class, 'shu_period_id'); }
    public function anggota() { return $this->belongsTo(User::class, 'anggota_id'); }
}
