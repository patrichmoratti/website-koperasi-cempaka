@extends('layouts.admin')
@php
$title = 'Manajemen Gadai';
@endphp
@section('content')

{{-- Page header --}}
<div x-data="{ createOpen: false }">
<div class="flex items-start justify-between mb-5">
    <div>
        <span class="badge-primary text-xs mb-1 inline-block">Admin</span>
        <h1 class="text-2xl font-bold text-mony-text tracking-tight">Manajemen Gadai</h1>
        <p class="text-sm mt-0.5 text-mony-muted">Monitoring transaksi gadai aktif dan riwayat selesai.</p>
    </div>
    <button type="button" @click="createOpen = true"
            class="btn-primary btn-sm flex-shrink-0 mt-1">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Gadai Manual
    </button>
</div>
@include('pengurus.gadai._popup_create_manual', ['formAction' => route('admin.gadai.store-manual')])
</div>{{-- /createOpen x-data --}}

{{-- Tab navigation --}}
<div class="flex gap-1 p-1 rounded-2xl mb-5" style="background: rgba(255,255,255,0.1);">
    @foreach(['aktif'=>'Transaksi Aktif','selesai'=>'Selesai'] as $key => $label)
        <a href="{{ route('admin.gadai.index', ['tab' => $key]) }}"
           class="flex-1 text-center py-2 rounded-xl text-xs font-semibold transition-all"
           style="{{ $tab === $key ? 'background:white; color:var(--green); box-shadow:0 1px 6px rgba(0,0,0,0.15);' : 'color:rgba(255,255,255,0.55);' }}"
           onmouseover="{{ $tab !== $key ? "this.style.color='rgba(255,255,255,0.9)'" : '' }}"
           onmouseout="{{ $tab !== $key ? "this.style.color='rgba(255,255,255,0.55)'" : '' }}">
            {{ $label }}
            @if($counts[$key] > 0)
                <span class="ml-1" style="{{ $tab === $key ? 'color:var(--green2);' : 'color:rgba(255,255,255,0.7);' }}">({{ $counts[$key] }})</span>
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
        @if($search)
            <a href="{{ route('admin.gadai.index', ['tab' => $tab]) }}" class="btn-outline">Reset</a>
        @endif
    </form>
</div>

{{-- ══ TAB: AKTIF ══ --}}
@if($tab === 'aktif')
<div x-data="{ openModal: null }" @keydown.escape.window="if(!$store.lb?.show){ openModal = null }">
    <div class="card overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm font-semibold text-mony-text">Transaksi Gadai Aktif</p>
            @if($counts['aktif'] > 0)
                <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:var(--green-light); color:var(--green);">{{ $counts['aktif'] }} aktif</span>
            @endif
        </div>
        <table class="table-base">
            <thead><tr>
                <th>No. Ref</th><th>Anggota</th><th>Barang</th>
                <th>Pinjaman</th><th>Jatuh Tempo</th><th>Status</th><th></th>
            </tr></thead>
            <tbody>
                @forelse($transaksi as $t)
                <tr @click="openModal = 'transaksi-{{ $t->id }}'" class="cursor-pointer">
                    <td class="font-mono text-xs">{{ $t->reference_number }}</td>
                    <td class="font-medium">{{ $t->anggota?->name }}</td>
                    <td>{{ $t->jenisBarang?->name }}</td>
                    <td class="font-medium">Rp {{ number_format($t->loan_amount, 0, ',', '.') }}</td>
                    <td class="{{ $t->isOverdue() ? 'text-red-600 font-medium' : 'text-mony-muted text-xs' }}">
                        {{ $t->due_date->format('d/m/Y') }}
                        @if($t->isOverdue())<span class="text-xs"> (Lewat!)</span>@endif
                    </td>
                    <td><span class="badge-{{ $t->status_color }}">{{ $t->status_label }}</span></td>
                    <td @click.stop>
                        <div class="flex gap-1">
                            <button type="button" @click.stop="openModal = 'transaksi-{{ $t->id }}'"
                                    class="btn-primary btn-sm">Detail</button>
                            <a href="{{ route('admin.gadai.transaksi', $t) }}" @click.stop class="btn-outline btn-sm">Struk</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-mony-muted">Tidak ada transaksi aktif</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($transaksi->hasPages())
            <div class="px-4 py-3 border-t">{{ $transaksi->links() }}</div>
        @endif
    </div>

    @foreach($transaksi as $t)
        @include('pengurus.gadai._popup_transaksi', ['t' => $t])
    @endforeach
</div>

{{-- ══ TAB: SELESAI ══ --}}
@else
<div x-data="{ openModal: null }" @keydown.escape.window="if(!$store.lb?.show){ openModal = null }">
    <div class="card overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm font-semibold text-mony-text">Transaksi Selesai</p>
        </div>
        <table class="table-base">
            <thead><tr>
                <th>No. Ref</th><th>Anggota</th><th>Barang</th>
                <th>Pinjaman</th><th>Tanggal</th><th>Status</th><th></th>
            </tr></thead>
            <tbody>
                @forelse($transaksi as $t)
                <tr @click="openModal = 'transaksi-{{ $t->id }}'" class="cursor-pointer">
                    <td class="font-mono text-xs">{{ $t->reference_number }}</td>
                    <td class="font-medium">{{ $t->anggota?->name }}</td>
                    <td>{{ $t->jenisBarang?->name }}</td>
                    <td>Rp {{ number_format($t->loan_amount, 0, ',', '.') }}</td>
                    <td class="text-xs text-mony-muted">{{ $t->pawn_date->format('d/m/Y') }}</td>
                    <td><span class="badge-{{ $t->status_color }}">{{ $t->status_label }}</span></td>
                    <td @click.stop>
                        <div class="flex gap-1">
                            <button type="button" @click.stop="openModal = 'transaksi-{{ $t->id }}'"
                                    class="btn-primary btn-sm">Detail</button>
                            <a href="{{ route('admin.gadai.transaksi', $t) }}" @click.stop class="btn-outline btn-sm">Struk</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-mony-muted">Tidak ada transaksi selesai</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($transaksi->hasPages())
            <div class="px-4 py-3 border-t">{{ $transaksi->links() }}</div>
        @endif
    </div>

    @foreach($transaksi as $t)
        @include('pengurus.gadai._popup_transaksi', ['t' => $t])
    @endforeach
</div>
@endif

@endsection