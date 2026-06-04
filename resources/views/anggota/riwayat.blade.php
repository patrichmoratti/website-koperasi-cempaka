@extends('layouts.anggota')
@php
$title = 'Riwayat Transaksi';
@endphp
@section('content')
<h1 class="page-title mb-6">Riwayat Transaksi</h1>

<div class="card p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Filter</label>
            <select name="filter" class="form-input">
                @foreach(['semua'=>'Semua','gadai'=>'Gadai','pembayaran'=>'Pembayaran','simpanan'=>'Simpanan'] as $k => $v)
                    <option value="{{ $k }}" @selected($filter === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Dari</label>
            <input type="date" name="from" value="{{ request('from') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Sampai</label>
            <input type="date" name="to" value="{{ request('to') }}" class="form-input">
        </div>
        <button type="submit" class="btn-primary">Filter</button>
    </form>
</div>

@if($riwayat->count())
    <div class="space-y-3">
        @foreach($riwayat as $item)
        <div class="card p-4 flex items-center gap-4 {{ $item->url ? 'hover:shadow-card-hover transition-shadow' : '' }}">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                {{ ['gadai'=>'bg-green-100','pembayaran'=>'bg-yellow-100','simpanan'=>'bg-blue-100'][$item->type] ?? 'bg-gray-100' }}">
                @if($item->type === 'gadai')
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                @elseif($item->type === 'pembayaran')
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @else
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-sm">{{ $item->title }}</p>
                <p class="text-xs text-mony-muted">{{ $item->description }} · {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="font-semibold text-sm">Rp {{ number_format($item->amount, 0, ',', '.') }}</p>
                <span class="badge-{{ $item->color ?? 'gray' }} text-xs">{{ $item->status }}</span>
            </div>
            @if($item->url)
                <a href="{{ $item->url }}" class="btn-ghost btn-sm btn-icon flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @endif
        </div>
        @endforeach
    </div>
@else
    <div class="card p-12 text-center text-mony-muted">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p>Belum ada riwayat transaksi</p>
    </div>
@endif
@endsection
