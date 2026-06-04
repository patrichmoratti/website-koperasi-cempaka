<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatStatus extends Model
{
    protected $table = 'riwayat_status';

    protected $fillable = [
        'entity_type', 'entity_id', 'from_status', 'to_status', 'changed_by', 'reason',
    ];

    public function changedBy() { return $this->belongsTo(User::class, 'changed_by'); }

    public static function record(string $entityType, int $entityId, ?string $from, string $to, ?int $changedBy = null, ?string $reason = null): self
    {
        return static::create([
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'from_status' => $from,
            'to_status'   => $to,
            'changed_by'  => $changedBy ?? auth()->id(),
            'reason'      => $reason,
        ]);
    }
}
