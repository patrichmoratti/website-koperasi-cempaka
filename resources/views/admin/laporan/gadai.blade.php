@extends('layouts.admin')
@php
$title = 'Laporan Gadai';
@endphp
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="page-title">Laporan Gadai</h1>
    <a href="{{ route('admin.laporan.gadai.excel') }}" class="btn-outline btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export Excel
    </a>
</div>

<div class="grid grid-cols-4 gap-4 mb-6">
    @foreach(['aktif'=>['success','Aktif'],'selesai'=>['info','Selesai'],'ditebus'=>['primary','Ditebus'],'dilelang'=>['danger','Dilelang']] as $s => [$color, $label])
    <div class="stat-card">
        <span class="stat-label">{{ $label }}</span>
        <span class="stat-value text-2xl">{{ $stats[$s] }}</span>
        <span class="badge-{{ $color }} w-fit">{{ $s }}</span>
    </div>
    @endforeach
</div>

<div class="card p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">Semua</option>
                @foreach(['aktif','selesai','ditebus','dilelang','menunggu_lelang'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
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
        @if(request()->hasAny(['status','from','to']))
            <a href="{{ route('admin.laporan.gadai') }}" class="btn-outline">Reset</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <table class="table-base">
        <thead><tr>
            <th>No. Ref</th><th>Anggota</th><th>Barang</th>
            <th>Pinjaman</th><th>Taksir</th><th>Gadai</th><th>Jatuh Tempo</th><th>Status</th>
        </tr></thead>
        <tbody>
            @forelse($transaksi as $t)
            <tr>
                <td class="font-mono text-xs">{{ $t->reference_number }}</td>
                <td class="font-medium text-sm">{{ $t->anggota?->name }}</td>
                <td class="text-sm">{{ $t->jenisBarang?->name }}</td>
                <td class="text-sm">Rp {{ number_format($t->loan_amount, 0, ',', '.') }}</td>
                <td class="text-sm">Rp {{ number_format($t->appraisal_value, 0, ',', '.') }}</td>
                <td class="text-xs text-mony-muted">{{ $t->pawn_date->format('d/m/Y') }}</td>
                <td class="text-xs {{ $t->isOverdue() ? 'text-red-600 font-medium' : 'text-mony-muted' }}">{{ $t->due_date->format('d/m/Y') }}</td>
                <td><span class="badge-{{ $t->status_color }} text-xs">{{ $t->status_label }}</span></td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-8 text-mony-muted">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($transaksi->hasPages())
        <div class="px-4 py-3 border-t">{{ $transaksi->links() }}</div>
    @endif
</div>
@endsection
