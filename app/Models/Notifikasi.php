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

    /**
     * Contextual icon key derived from the notification's subject —
     * used by the UI to pick a fitting icon (no dedicated column for this).
     */
    public function getIconKeyAttribute(): string
    {
        if (str_contains($this->title, 'Simpanan'))     return 'simpanan';
        if (str_contains($this->title, 'Pendaftaran'))  return 'registrasi';
        if (str_contains($this->title, 'Jatuh Tempo'))  return 'jatuh_tempo';
        if (str_contains($this->title, 'Pembayaran'))   return 'pembayaran';
        if ($this->related_entity_type === 'pengajuan_gadai') return 'pengajuan';
        if ($this->related_entity_type === 'transaksi_gadai') return 'gadai';

        return 'default';
    }
}
