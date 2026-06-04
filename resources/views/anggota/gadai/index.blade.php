@extends('layouts.anggota')
@php
$title = 'Gadai Saya';
@endphp
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="page-title">Gadai Saya</h1>
    <a href="{{ route('anggota.gadai.pengajuan.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajukan Gadai
    </a>
</div>

<div class="flex gap-1 mb-4 bg-gray-100 p-1 rounded-xl w-fit">
    @foreach(['aktif'=>'Aktif','pending'=>'Pending','selesai'=>'Selesai','ditolak'=>'Ditolak'] as $key => $label)
        <a href="{{ route('anggota.gadai.index', ['tab' => $key]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $tab === $key ? 'bg-white shadow text-mony-text' : 'text-mony-muted hover:text-mony-text' }}">
            {{ $label }}
            @if($counts[$key] > 0) <span class="ml-1 badge-{{ $key === 'aktif' ? 'success' : ($key === 'pending' ? 'warning' : 'gray') }} text-xs">{{ $counts[$key] }}</span> @endif
        </a>
    @endforeach
</div>

@if(in_array($tab, ['aktif','selesai']))
    @if($transaksi->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($transaksi as $t)
            <div class="card p-5 {{ $t->isOverdue() ? 'border-l-4 border-red-400' : '' }}">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="font-semibold text-sm">{{ $t->jenisBarang?->name }}</p>
                        <p class="text-xs text-mony-muted font-mono">{{ $t->reference_number }}</p>
                    </div>
                    <span class="badge-{{ $t->status_color }}">{{ $t->status_label }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                    <div><span class="text-mony-muted">Pinjaman</span><p class="font-semibold text-sm">Rp {{ number_format($t->loan_amount, 0, ',', '.') }}</p></div>
                    <div><span class="text-mony-muted">Bunga/Bln</span><p class="font-semibold text-sm">Rp {{ number_format($t->monthlyInterest(), 0, ',', '.') }}</p></div>
                    <div><span class="text-mony-muted">Gadai</span><p>{{ $t->pawn_date->format('d/m/Y') }}</p></div>
                    <div>
                        <span class="text-mony-muted {{ $t->isOverdue() ? 'text-red-500' : '' }}">Jatuh Tempo</span>
                        <p class="{{ $t->isOverdue() ? 'text-red-600 font-medium' : '' }}">
                            {{ $t->due_date->format('d/m/Y') }}
                            @if(!$t->isOverdue() && $t->status === 'aktif')
                                <span class="text-mony-muted">({{ $t->daysUntilDue() }}h)</span>
                            @elseif($t->isOverdue())
                                <span class="text-red-500">(!)</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('anggota.gadai.detail', $t) }}" class="btn-outline btn-sm flex-1 text-center">Detail</a>
                    @if($t->status === 'aktif')
                        <a href="{{ route('anggota.gadai.bayar', $t) }}" class="btn-primary btn-sm flex-1 text-center">Bayar</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @if($transaksi->hasPages()) <div class="mt-4">{{ $transaksi->links() }}</div> @endif
    @else
        <div class="card p-12 text-center text-mony-muted">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <p class="mb-3">Tidak ada gadai {{ $tab }}</p>
            @if($tab === 'aktif') <a href="{{ route('anggota.gadai.pengajuan.create') }}" class="btn-primary btn-sm">Ajukan Sekarang</a> @endif
        </div>
    @endif
@else
    @if($pengajuan->count())
        <div class="space-y-3">
            @foreach($pengajuan as $p)
            <div class="card p-4 flex items-center justify-between">
                <div>
                    <p class="font-medium text-sm">{{ $p->jenisBarang?->name }}</p>
                    <p class="text-xs text-mony-muted">{{ $p->submitted_at->format('d/m/Y') }}</p>
                    @if($p->reason_if_rejected) <p class="text-xs text-red-600 mt-1">Ditolak: {{ $p->reason_if_rejected }}</p> @endif
                </div>
                <div class="text-right">
                    <p class="font-medium text-sm">Rp {{ number_format($p->loan_request_amount, 0, ',', '.') }}</p>
                    <span class="badge-{{ $p->status_color }} text-xs">{{ $p->status_label }}</span>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="card p-12 text-center text-mony-muted">
            <p>Tidak ada pengajuan {{ $tab }}</p>
        </div>
    @endif
@endif
@endsection
