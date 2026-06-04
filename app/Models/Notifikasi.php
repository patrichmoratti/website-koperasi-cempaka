<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id', 'category', 'title', 'message',
        'related_entity_type', 'related_entity_id', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function scopeUnread($q)   { return $q->where('is_read', false); }
    public function scopeByUser($q, int $userId) { return $q->where('user_id', $userId); }

    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'mendesak'  => 'danger',
            'pengingat' => 'warning',
            'update'    => 'info',
            default     => 'gray',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'mendesak'  => 'Mendesak',
            'pengingat' => 'Pengingat',
            'update'    => 'Update',
            default     => $this->category,
        };
    }
}
