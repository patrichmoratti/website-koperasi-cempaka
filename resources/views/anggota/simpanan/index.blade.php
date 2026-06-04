@extends('layouts.anggota')
@php
$title = 'Simpanan';
@endphp
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="page-title">Simpanan Saya</h1>
    <a href="{{ route('anggota.simpanan.bayar') }}" class="btn-primary btn-sm">Bayar Simpanan</a>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="stat-card">
        <span class="stat-label">Simpanan Pokok</span>
        <span class="stat-value text-xl">Rp {{ number_format($summary['pokok'], 0, ',', '.') }}</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Simpanan Wajib</span>
        <span class="stat-value text-xl">Rp {{ number_format($summary['wajib'], 0, ',', '.') }}</span>
    </div>
    <div class="stat-card bg-primary/5">
        <span class="stat-label">Total</span>
        <span class="stat-value text-xl text-primary">Rp {{ number_format($summary['total'], 0, ',', '.') }}</span>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="p-4 border-b border-gray-100">
        <h3 class="section-title">Riwayat Simpanan</h3>
    </div>
    <table class="table-base">
        <thead><tr><th>Tipe</th><th>Periode</th><th>Jumlah</th><th>Status</th><th>Tanggal</th></tr></thead>
        <tbody>
            @forelse($simpanan as $s)
            <tr>
                <td><span class="badge-{{ $s->type === 'pokok' ? 'info' : 'primary' }}">{{ $s->type_label }}</span></td>
                <td class="text-sm">{{ $s->period_label }}</td>
                <td class="font-medium">Rp {{ number_format($s->amount, 0, ',', '.') }}</td>
                <td><span class="badge-{{ $s->status_color }}">{{ ucfirst($s->status) }}</span></td>
                <td class="text-xs text-mony-muted">{{ $s->submitted_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-8 text-mony-muted">Belum ada simpanan</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($simpanan->hasPages()) <div class="px-4 py-3 border-t">{{ $simpanan->links() }}</div> @endif
</div>
@endsection
