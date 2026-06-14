@extends('layouts.admin')
@php
$title = 'Log Transaksi ACID';
@endphp
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-mony-text tracking-tight">Log Transaksi ACID</h1>
        <p class="text-sm mt-1 text-mony-muted">Audit trail seluruh transaksi sistem &mdash; tercatat di dalam blok transaksi database (commit/rollback)</p>
    </div>
</div>

{{-- Stats strip --}}
<div class="card p-0 mb-4 overflow-hidden">
    <div class="grid grid-cols-1 sm:grid-cols-2">
        <div class="flex items-center gap-3 p-4 border-b sm:border-b-0 sm:border-r" style="border-color:#eef3ea">
            <div class="w-2 h-10 rounded-full flex-shrink-0" style="background:var(--green-light); border:2px solid; border-color:var(--green);"></div>
            <div>
                <p class="text-xs text-mony-muted">Transaksi Berhasil (Committed)</p>
                <p class="text-base font-bold" style="color:var(--green)">{{ number_format($counts['committed']) }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 p-4">
            <div class="w-2 h-10 rounded-full flex-shrink-0" style="background:#fee2e2; border:2px solid; border-color:#DC2626;"></div>
            <div>
                <p class="text-xs text-mony-muted">Transaksi Dibatalkan (Rolled Back)</p>
                <p class="text-base font-bold" style="color:#DC2626">{{ number_format($counts['rolled_back']) }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="w-56">
            <label class="form-label">Jenis Transaksi</label>
            <select name="transaction_type" class="form-input">
                <option value="">Semua</option>
                @foreach($transactionTypes as $key => $label)
                    <option value="{{ $key }}" @selected(request('transaction_type') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-40">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">Semua</option>
                <option value="committed" @selected(request('status') === 'committed')>Committed</option>
                <option value="rolled_back" @selected(request('status') === 'rolled_back')>Rolled Back</option>
            </select>
        </div>
        <div class="w-36">
            <label class="form-label">Bulan</label>
            <select name="month" class="form-input">
                <option value="">Semua</option>
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" @selected(request('month') == $m)>{{ \Carbon\Carbon::create(null,$m)->isoFormat('MMMM') }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-28">
            <label class="form-label">Tahun</label>
            <select name="year" class="form-input">
                <option value="">Semua</option>
                @foreach(range(now()->year, 2020) as $y)
                    <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['transaction_type','status','month','year']))
            <a href="{{ route('admin.transaction-logs.index') }}" class="btn-outline">Reset</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table-base">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis Transaksi</th>
                    <th>Anggota</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Diproses Oleh</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-xs text-mony-muted whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td><span class="badge-info text-xs">{{ $log->transaction_type_label }}</span></td>
                    <td class="text-sm">{{ $log->anggota?->name ?? '-' }}</td>
                    <td class="font-medium text-sm">{{ $log->amount !== null ? 'Rp ' . number_format($log->amount, 0, ',', '.') : '-' }}</td>
                    <td><span class="badge-{{ $log->status_color }} text-xs">{{ $log->status_label }}</span></td>
                    <td class="text-xs">{{ $log->pengurus?->name ?? '-' }}</td>
                    <td class="text-xs text-mony-muted max-w-xs">{{ $log->description }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-mony-muted">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.414l3.586 3.586A1 1 0 0116 7.414V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm">Belum ada log transaksi</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
    @endif
</div>

@endsection