@extends('layouts.admin')
@php
$title = 'Manajemen Gadai';
@endphp
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="page-title">Manajemen Gadai</h1>
</div>

{{-- Tabs --}}
<div class="flex gap-1 mb-4 bg-gray-100 p-1 rounded-xl w-fit">
    @foreach(['pengajuan'=>'Pengajuan Baru','aktif'=>'Aktif','selesai'=>'Selesai','diterima'=>'Diterima','ditolak'=>'Ditolak','semua'=>'Semua'] as $key => $label)
        <a href="{{ route('admin.gadai.index', ['tab' => $key]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $tab === $key ? 'bg-white shadow text-mony-text' : 'text-mony-muted hover:text-mony-text' }}">
            {{ $label }}
            @if(isset($counts[$key]) && $counts[$key] > 0)
                <span class="ml-1 badge-{{ $key === 'pengajuan' ? 'danger' : 'warning' }}">{{ $counts[$key] }}</span>
            @endif
        </a>
    @endforeach
</div>

{{-- Search --}}
<div class="card p-4 mb-4">
    <form method="GET" class="flex gap-3 items-end">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="flex-1">
            <input type="text" name="search" value="{{ $search }}" class="form-input" placeholder="Cari nama anggota...">
        </div>
        <button type="submit" class="btn-primary">Cari</button>
    </form>
</div>

@if(in_array($tab, ['pengajuan','diterima','ditolak']))
    {{-- Pengajuan table --}}
    <div class="card overflow-hidden">
        <table class="table-base">
            <thead><tr>
                <th>Anggota</th><th>Jenis Barang</th><th>Estimasi Nilai</th>
                <th>Pinjaman Diminta</th><th>Status</th><th>Tanggal</th><th></th>
            </tr></thead>
            <tbody>
                @forelse($pengajuan as $p)
                <tr>
                    <td class="font-medium">{{ $p->anggota?->name }}</td>
                    <td>{{ $p->jenisBarang?->name }}</td>
                    <td>Rp {{ number_format($p->estimated_value, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($p->loan_request_amount, 0, ',', '.') }}</td>
                    <td><span class="badge-{{ $p->status_color }}">{{ $p->status_label }}</span></td>
                    <td class="text-xs text-mony-muted">{{ $p->submitted_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.gadai.pengajuan', $p) }}" class="btn-primary btn-sm">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-mony-muted">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($pengajuan->hasPages())
            <div class="px-4 py-3 border-t">{{ $pengajuan->links() }}</div>
        @endif
    </div>
@else
    {{-- Transaksi table --}}
    <div class="card overflow-hidden">
        <table class="table-base">
            <thead><tr>
                <th>No. Ref</th><th>Anggota</th><th>Barang</th>
                <th>Pinjaman</th><th>Jatuh Tempo</th><th>Status</th><th></th>
            </tr></thead>
            <tbody>
                @forelse($transaksi as $t)
                <tr>
                    <td class="font-mono text-xs">{{ $t->reference_number }}</td>
                    <td class="font-medium">{{ $t->anggota?->name }}</td>
                    <td>{{ $t->jenisBarang?->name }}</td>
                    <td>Rp {{ number_format($t->loan_amount, 0, ',', '.') }}</td>
                    <td class="{{ $t->isOverdue() ? 'text-red-600 font-medium' : 'text-mony-muted text-xs' }}">
                        {{ $t->due_date->format('d/m/Y') }}
                        @if($t->isOverdue()) <span class="text-xs">(Lewat!)</span> @endif
                    </td>
                    <td><span class="badge-{{ $t->status_color }}">{{ $t->status_label }}</span></td>
                    <td><a href="{{ route('admin.gadai.transaksi', $t) }}" class="btn-primary btn-sm">Detail</a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-mony-muted">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($transaksi->hasPages())
            <div class="px-4 py-3 border-t">{{ $transaksi->links() }}</div>
        @endif
    </div>
@endif
@endsection
