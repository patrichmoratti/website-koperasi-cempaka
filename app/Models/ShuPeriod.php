<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShuPeriod extends Model
{
    protected $table = 'shu_period';

    protected $fillable = [
        'year', 'status',
        'total_income', 'total_expenses', 'total_shu',
        'pct_dana_cadangan', 'pct_jasa_modal', 'pct_jasa_usaha',
        'pct_dana_pengurus', 'pct_dana_pendidikan', 'pct_dana_sosial',
        'alloc_dana_cadangan', 'alloc_jasa_modal', 'alloc_jasa_usaha',
        'alloc_dana_pengurus', 'alloc_dana_pendidikan', 'alloc_dana_sosial',
    ];

    protected $casts = [
        'total_income'       => 'float',
        'total_expenses'     => 'float',
        'total_shu'          => 'float',
        'alloc_dana_cadangan'=> 'float',
        'alloc_jasa_modal'   => 'float',
        'alloc_jasa_usaha'   => 'float',
        'alloc_dana_pengurus'=> 'float',
        'alloc_dana_pendidikan'=> 'float',
        'alloc_dana_sosial'  => 'float',
    ];

    public function distributions() { return $this->hasMany(ShuDistribution::class, 'shu_period_id'); }

    public function isPublished(): bool { return $this->status === 'published'; }
    public function isClosed(): bool    { return in_array($this->status, ['closed', 'published']); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open'      => 'Terbuka',
            'closed'    => 'Ditutup',
            'published' => 'Dipublikasikan',
            default     => $this->status,
        };
    }
}
