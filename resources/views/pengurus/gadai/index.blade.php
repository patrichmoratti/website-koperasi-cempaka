@extends('layouts.pengurus')
@php
$title = 'Gadai';
@endphp
@section('content')
<h1 class="page-title mb-6">Manajemen Gadai</h1>

<div class="flex gap-1 mb-4 bg-gray-100 p-1 rounded-xl w-fit">
    @foreach(['pengajuan'=>'Pengajuan','aktif'=>'Transaksi Aktif'] as $key => $label)
        <a href="{{ route('pengurus.gadai.index', ['tab' => $key]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $tab === $key ? 'bg-white shadow text-mony-text' : 'text-mony-muted hover:text-mony-text' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

<div class="card overflow-hidden">
    <table class="table-base">
        <thead><tr>
            @if($tab === 'pengajuan')
                <th>Anggota</th><th>Barang</th><th>Pinjaman</th><th>Status</th><th>Tanggal</th><th></th>
            @else
                <th>No. Ref</th><th>Anggota</th><th>Barang</th><th>Pinjaman</th><th>Jatuh Tempo</th><th>Status</th><th></th>
            @endif
        </tr></thead>
        <tbody>
            @if($tab === 'pengajuan')
                @forelse($pengajuan as $p)
                <tr>
                    <td class="font-medium">{{ $p->anggota?->name }}</td>
                    <td>{{ $p->jenisBarang?->name }}</td>
                    <td>Rp {{ number_format($p->loan_request_amount, 0, ',', '.') }}</td>
                    <td><span class="badge-{{ $p->status_color }}">{{ $p->status_label }}</span></td>
                    <td class="text-xs text-mony-muted">{{ $p->submitted_at->format('d/m/Y') }}</td>
                    <td><a href="{{ route('pengurus.gadai.pengajuan', $p) }}" class="btn-primary btn-sm">Detail</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-mony-muted">Tidak ada data</td></tr>
                @endforelse
            @else
                @forelse($transaksi as $t)
                <tr>
                    <td class="font-mono text-xs">{{ $t->reference_number }}</td>
                    <td class="font-medium">{{ $t->anggota?->name }}</td>
                    <td>{{ $t->jenisBarang?->name }}</td>
                    <td>Rp {{ number_format($t->loan_amount, 0, ',', '.') }}</td>
                    <td class="{{ $t->isOverdue() ? 'text-red-600 font-medium' : 'text-mony-muted text-xs' }}">{{ $t->due_date->format('d/m/Y') }}</td>
                    <td><span class="badge-{{ $t->status_color }}">{{ $t->status_label }}</span></td>
                    <td><a href="{{ route('pengurus.gadai.transaksi', $t) }}" class="btn-primary btn-sm">Detail</a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-mony-muted">Tidak ada data</td></tr>
                @endforelse
            @endif
        </tbody>
    </table>
</div>
@endsection
