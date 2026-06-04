@extends('layouts.anggota')
@php
$title = 'Dashboard';
@endphp
@section('content')
<div class="mb-6">
    <h1 class="page-title">Halo, {{ explode(' ', auth()->user()->name)[0] }}!</h1>
    <p class="text-sm text-mony-muted mt-1">Selamat datang di MONY — layanan gadai koperasi Anda</p>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <span class="stat-label">Gadai Aktif</span>
        <span class="stat-value text-3xl">{{ $totalGadai }}</span>
        <span class="text-xs text-mony-muted">Rp {{ number_format($totalPinjaman, 0, ',', '.') }}</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Total Simpanan</span>
        <span class="stat-value text-xl">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">SHU Tahun Ini</span>
        <span class="stat-value text-xl {{ $shuTahunIni > 0 ? 'text-primary' : '' }}">
            Rp {{ number_format($shuTahunIni, 0, ',', '.') }}
        </span>
    </div>
    <div class="stat-card {{ $tagihan > 0 ? 'border-l-4 border-yellow-400' : '' }}">
        <span class="stat-label">Tagihan Bunga Bulan Ini</span>
        <span class="stat-value text-xl {{ $tagihan > 0 ? 'text-yellow-600' : '' }}">
            Rp {{ number_format($tagihan, 0, ',', '.') }}
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    {{-- Active Gadai — melebar 2/3 --}}
    <div class="lg:col-span-2 card p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="section-title">Gadai Aktif</h3>
            <a href="{{ route('anggota.gadai.index') }}" class="text-sm hover:underline" style="color: var(--green)">Lihat semua →</a>
        </div>

        @if($gadaiAktif->count())
            <div class="space-y-3">
                @foreach($gadaiAktif as $g)
                <div class="p-4 rounded-xl border" style="background: var(--cream); border-color: rgba(26,61,46,0.08)">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-medium text-sm" style="color: var(--text)">{{ $g->jenisBarang?->name }}</p>
                        <span class="badge-{{ $g->status_color }}">{{ $g->status_label }}</span>
                    </div>
                    <p class="text-xs font-mono mb-2" style="color: var(--text-muted)">{{ $g->reference_number }}</p>
                    <div class="flex items-center justify-between text-sm">
                        <span style="color: var(--text)">Pinjaman: <strong>Rp {{ number_format($g->loan_amount, 0, ',', '.') }}</strong></span>
                        <span class="{{ $g->isOverdue() ? 'text-red-600 font-medium' : '' }}" style="{{ $g->isOverdue() ? '' : 'color: var(--text-muted)' }}">
                            @if($g->isOverdue())
                                Lewat {{ abs($g->daysUntilDue()) }} hari!
                            @else
                                {{ $g->daysUntilDue() }} hari lagi
                            @endif
                        </span>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <a href="{{ route('anggota.gadai.detail', $g) }}" class="btn-outline btn-sm flex-1 text-center">Detail</a>
                        @if($g->status === 'aktif')
                            <a href="{{ route('anggota.gadai.bayar', $g) }}" class="btn-primary btn-sm flex-1 text-center">Bayar</a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10">
                <svg class="w-12 h-12 mx-auto mb-3" style="color: var(--green-light)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="text-sm mb-3" style="color: var(--text-muted)">Tidak ada gadai aktif</p>
                <a href="{{ route('anggota.gadai.pengajuan.create') }}" class="btn-primary btn-sm">Ajukan Gadai</a>
            </div>
        @endif
    </div>

    {{-- Notifikasi Terbaru — 1/3 --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="section-title">Notifikasi</h3>
            <a href="{{ route('anggota.notifikasi.index') }}" class="text-xs hover:underline" style="color: var(--green)">Lihat semua</a>
        </div>
        @if($recentNotif->count())
            <div class="space-y-2">
                @foreach($recentNotif as $notif)
                <div class="flex items-start gap-3 p-3 rounded-xl" style="{{ !$notif->is_read ? 'background: var(--green-light)' : 'background: var(--cream)' }}">
                    <div class="w-2 h-2 rounded-full mt-1 flex-shrink-0 {{ ['mendesak'=>'bg-red-500','pengingat'=>'bg-yellow-500','update'=>'bg-blue-500'][$notif->category] ?? 'bg-gray-400' }}"></div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium" style="color: var(--text)">{{ $notif->title }}</p>
                        <p class="text-xs mt-0.5" style="color: var(--text-muted)">{{ Str::limit($notif->message, 55) }}</p>
                        <p class="text-xs mt-1" style="color: var(--text-muted)">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-8 h-8 mx-auto mb-2" style="color: var(--green-light)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-xs" style="color: var(--text-muted)">Tidak ada notifikasi</p>
            </div>
        @endif
    </div>
</div>
@endsection
