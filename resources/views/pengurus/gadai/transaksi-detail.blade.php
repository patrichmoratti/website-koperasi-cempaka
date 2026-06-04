@extends('layouts.pengurus')
@php
$title = 'Detail Transaksi';
@endphp
@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('pengurus.gadai.index', ['tab' => 'aktif']) }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <div>
        <h1 class="page-title">{{ $transaksi->reference_number }}</h1>
        <span class="badge-{{ $transaksi->status_color }}">{{ $transaksi->status_label }}</span>
    </div>
</div>

<div class="card p-6 max-w-2xl">
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div><p class="text-mony-muted">Anggota</p><p class="font-medium">{{ $transaksi->anggota?->name }}</p></div>
        <div><p class="text-mony-muted">Barang</p><p class="font-medium">{{ $transaksi->jenisBarang?->name }}</p></div>
        <div><p class="text-mony-muted">Nilai Taksir</p><p class="font-semibold">Rp {{ number_format($transaksi->appraisal_value, 0, ',', '.') }}</p></div>
        <div><p class="text-mony-muted">Pinjaman</p><p class="font-semibold text-primary">Rp {{ number_format($transaksi->loan_amount, 0, ',', '.') }}</p></div>
        <div><p class="text-mony-muted">Tanggal Gadai</p><p class="font-medium">{{ $transaksi->pawn_date->format('d M Y') }}</p></div>
        <div><p class="text-mony-muted {{ $transaksi->isOverdue() ? 'text-red-600' : '' }}">Jatuh Tempo</p>
            <p class="font-medium {{ $transaksi->isOverdue() ? 'text-red-600' : '' }}">{{ $transaksi->due_date->format('d M Y') }}</p></div>
        <div><p class="text-mony-muted">Bunga/Bulan</p><p class="font-medium">Rp {{ number_format($transaksi->monthlyInterest(), 0, ',', '.') }}</p></div>
        <div><p class="text-mony-muted">Total Tebus</p><p class="font-medium text-secondary">Rp {{ number_format($transaksi->totalRedemption(), 0, ',', '.') }}</p></div>
        <div class="col-span-2"><p class="text-mony-muted">Deskripsi</p><p class="font-medium">{{ $transaksi->item_description }}</p></div>
    </div>

    <div class="mt-6">
        <h4 class="font-medium text-sm mb-3">Riwayat Pembayaran</h4>
        @if($transaksi->pembayaran->count())
            <div class="space-y-2">
                @foreach($transaksi->pembayaran as $p)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl text-sm">
                        <div>
                            <span class="badge-{{ $p->payment_type === 'tebus' ? 'info' : 'success' }}">{{ $p->payment_type_label }}</span>
                            <span class="text-xs text-mony-muted ml-2">{{ $p->submitted_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">Rp {{ number_format($p->amount, 0, ',', '.') }}</p>
                            <span class="badge-{{ $p->status_color }} text-xs">{{ ucfirst($p->status) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-mony-muted">Belum ada pembayaran</p>
        @endif
    </div>
</div>
@endsection
