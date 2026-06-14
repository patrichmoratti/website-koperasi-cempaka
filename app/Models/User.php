<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'nik', 'password',
        'role', 'account_status', 'address',
        'ktp_photo', 'selfie_photo', 'rejection_reason',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function isAdmin(): bool     { return $this->role === 'admin'; }
    public function isPengurus(): bool  { return $this->role === 'pengurus'; }
    public function isAnggota(): bool   { return $this->role === 'anggota'; }
    public function isActive(): bool    { return $this->account_status === 'active'; }
    public function isPending(): bool   { return $this->account_status === 'pending'; }

    public function pengajuanGadai()    { return $this->hasMany(PengajuanGadai::class, 'anggota_id'); }
    public function transaksiGadai()    { return $this->hasMany(TransaksiGadai::class, 'anggota_id'); }
    public function simpanan()          { return $this->hasMany(Simpanan::class, 'anggota_id'); }
    public function notifikasi()        { return $this->hasMany(Notifikasi::class, 'user_id'); }
    public function unreadNotifikasi()  { return $this->notifikasi()->where('is_read', false); }
    public function sentPesan()         { return $this->hasMany(Pesan::class, 'sender_id'); }
    public function receivedPesan()     { return $this->hasMany(Pesan::class, 'receiver_id'); }

    public function totalSimpanan(): float
    {
        return (float) $this->simpanan()->where('status', 'confirmed')->sum('amount');
    }

    public function totalSimpananPokok(): float
    {
        return (float) $this->simpanan()->where('type', 'pokok')->where('status', 'confirmed')->sum('amount');
    }

    public function totalSimpananWajib(): float
    {
        return (float) $this->simpanan()->where('type', 'wajib')->where('status', 'confirmed')->sum('amount');
    }
}
