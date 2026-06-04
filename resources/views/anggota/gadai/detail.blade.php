@extends('layouts.anggota')
@php
$title = 'Detail Gadai';
@endphp
@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('anggota.gadai.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <div>
        <h1 class="page-title">{{ $transaksi->reference_number }}</h1>
        <span class="badge-{{ $transaksi->status_color }}">{{ $transaksi->status_label }}</span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 max-w-4xl">
    <div class="space-y-4">
        {{-- Info --}}
        <div class="card p-5">
            <h3 class="section-title mb-4">Informasi Transaksi</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-mony-muted">Barang</span><span class="font-medium">{{ $transaksi->jenisBarang?->name }}</span></div>
                <div class="flex justify-between"><span class="text-mony-muted">Nilai Taksir</span><span class="font-medium">Rp {{ number_format($transaksi->appraisal_value, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-mony-muted">Pinjaman</span><span class="font-semibold text-primary">Rp {{ number_format($transaksi->loan_amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-mony-muted">Bunga per Bulan</span><span>{{ $transaksi->interest_rate }}% = Rp {{ number_format($transaksi->monthlyInterest(), 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-mony-muted">Tanggal Gadai</span><span>{{ $transaksi->pawn_date->format('d M Y') }}</span></div>
                <div class="flex justify-between">
                    <span class="text-mony-muted {{ $transaksi->isOverdue() ? 'text-red-600' : '' }}">Jatuh Tempo</span>
                    <span class="{{ $transaksi->isOverdue() ? 'text-red-600 font-medium' : '' }}">
                        {{ $transaksi->due_date->format('d M Y') }}
                        @if($transaksi->isOverdue())
                            (Lewat {{ abs($transaksi->daysUntilDue()) }} hari!)
                        @else
                            ({{ $transaksi->daysUntilDue() }} hari lagi)
                        @endif
                    </span>
                </div>
                <div class="flex justify-between"><span class="text-mony-muted">Lokasi Penyimpanan</span><span>{{ $transaksi->warehouse_location ?? '-' }}</span></div>
                <div class="border-t pt-3 flex justify-between">
                    <span class="font-medium">Total Tebus Sekarang</span>
                    <span class="font-bold text-secondary">Rp {{ number_format($transaksi->totalRedemption(), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if($transaksi->status === 'aktif')
        <a href="{{ route('anggota.gadai.bayar', $transaksi) }}" class="btn-primary w-full text-center block">
            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Bayar Bunga / Tebus
        </a>
        @endif
    </div>

    {{-- Payment History --}}
    <div class="card p-5">
        <h3 class="section-title mb-4">Riwayat Pembayaran</h3>
        @if($transaksi->pembayaran->count())
            <div class="space-y-3">
                @foreach($transaksi->pembayaran->sortByDesc('submitted_at') as $p)
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex items-center justify-between mb-1">
                        <span class="badge-{{ $p->payment_type === 'tebus' ? 'info' : 'success' }} text-xs">{{ $p->payment_type_label }}</span>
                        <span class="badge-{{ $p->status_color }} text-xs">{{ ucfirst($p->status) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-mony-muted">{{ $p->submitted_at->format('d M Y') }}</span>
                        <span class="font-medium">Rp {{ number_format($p->amount, 0, ',', '.') }}</span>
                    </div>
                    @if($p->rejection_reason)
                        <p class="text-xs text-red-600 mt-1">Ditolak: {{ $p->rejection_reason }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6 text-mony-muted">
                <p class="text-sm">Belum ada riwayat pembayaran</p>
            </div>
        @endif
    </div>
</div>
@endsection
