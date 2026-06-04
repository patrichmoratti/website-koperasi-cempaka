@extends('layouts.admin')
@php
$title = 'Detail SHU ' . $shu->year;
@endphp
@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.shu.index') }}" class="btn-ghost btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="page-title">SHU Tahun {{ $shu->year }}</h1>
        <span class="badge-{{ ['open'=>'warning','closed'=>'info','published'=>'success'][$shu->status] }}">{{ $shu->status_label }}</span>
    </div>
    <div class="flex gap-2">
        @if($shu->isClosed())
            <a href="{{ route('admin.shu.excel', $shu) }}" class="btn-outline btn-sm">Excel</a>
            <a href="{{ route('admin.shu.pdf', $shu) }}" class="btn-outline btn-sm" target="_blank">PDF</a>
        @endif
    </div>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    @foreach([
        ['Pendapatan','text-green-700', $shu->total_income],
        ['Biaya','text-red-700', $shu->total_expenses],
        ['Total SHU','text-primary', $shu->total_shu],
        ['Jasa Modal','text-blue-700', $shu->alloc_jasa_modal],
        ['Jasa Usaha','text-purple-700', $shu->alloc_jasa_usaha],
        ['Dana Cadangan','text-gray-700', $shu->alloc_dana_cadangan],
    ] as [$label, $color, $val])
    <div class="stat-card">
        <span class="stat-label">{{ $label }}</span>
        <span class="font-bold text-sm {{ $color }}">Rp {{ number_format($val, 0, ',', '.') }}</span>
    </div>
    @endforeach
</div>

<div class="card overflow-hidden">
    <div class="p-4 border-b border-gray-100">
        <h3 class="section-title">Distribusi per Anggota ({{ $shu->distributions->count() }})</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="table-base">
            <thead><tr>
                <th>Anggota</th><th>Total Simpanan</th><th>Jasa Modal</th>
                <th>Total Bunga Bayar</th><th>Jasa Usaha</th><th>Total SHU Diterima</th><th>Status</th>
            </tr></thead>
            <tbody>
                @forelse($shu->distributions as $d)
                <tr>
                    <td class="font-medium">{{ $d->anggota?->name }}</td>
                    <td>Rp {{ number_format($d->total_savings, 0, ',', '.') }}</td>
                    <td class="text-blue-700">Rp {{ number_format($d->member_jasa_modal, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($d->total_interest_paid, 0, ',', '.') }}</td>
                    <td class="text-purple-700">Rp {{ number_format($d->member_jasa_usaha, 0, ',', '.') }}</td>
                    <td class="font-semibold text-primary">Rp {{ number_format($d->total_shu_received, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge-{{ $d->withdrawal_status === 'withdrawn' ? 'success' : 'warning' }}">
                            {{ $d->withdrawal_status === 'withdrawn' ? 'Dicairkan' : 'Pending' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-mony-muted">Belum ada distribusi. Klik "Hitung & Distribusi" pada halaman utama SHU.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
