@extends('layouts.admin')
@php
$title = 'Simpanan';
@endphp
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="page-title">Data Simpanan</h1>
    <a href="{{ route('admin.simpanan.export') }}" class="btn-outline btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export Excel
    </a>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="stat-card">
        <span class="stat-label">Total Simpanan Pokok</span>
        <span class="stat-value text-xl">Rp {{ number_format($summary['total_pokok'], 0, ',', '.') }}</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Total Simpanan Wajib</span>
        <span class="stat-value text-xl">Rp {{ number_format($summary['total_wajib'], 0, ',', '.') }}</span>
    </div>
    <div class="stat-card bg-primary/5">
        <span class="stat-label">Total Keseluruhan</span>
        <span class="stat-value text-xl text-primary">Rp {{ number_format($summary['total_pokok'] + $summary['total_wajib'], 0, ',', '.') }}</span>
    </div>
</div>

<div class="card p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-40">
            <label class="form-label">Cari Anggota</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Nama anggota...">
        </div>
        <div>
            <label class="form-label">Tipe</label>
            <select name="type" class="form-input">
                <option value="">Semua</option>
                <option value="pokok" @selected(request('type') === 'pokok')>Pokok</option>
                <option value="wajib" @selected(request('type') === 'wajib')>Wajib</option>
            </select>
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">Semua</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="confirmed" @selected(request('status') === 'confirmed')>Dikonfirmasi</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Ditolak</option>
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['search','type','status','month','year']))
            <a href="{{ route('admin.simpanan.index') }}" class="btn-outline">Reset</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <table class="table-base">
        <thead><tr>
            <th>Anggota</th><th>Tipe</th><th>Periode</th>
            <th>Jumlah</th><th>Status</th><th>Tanggal</th>
        </tr></thead>
        <tbody>
            @forelse($simpanan as $s)
            <tr>
                <td class="font-medium">{{ $s->anggota?->name }}</td>
                <td><span class="badge-{{ $s->type === 'pokok' ? 'info' : 'primary' }}">{{ $s->type_label }}</span></td>
                <td class="text-xs">{{ $s->period_label }}</td>
                <td class="font-medium">Rp {{ number_format($s->amount, 0, ',', '.') }}</td>
                <td><span class="badge-{{ $s->status_color }}">{{ ucfirst($s->status) }}</span></td>
                <td class="text-xs text-mony-muted">{{ $s->submitted_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-8 text-mony-muted">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($simpanan->hasPages())
        <div class="px-4 py-3 border-t">{{ $simpanan->links() }}</div>
    @endif
</div>
@endsection
