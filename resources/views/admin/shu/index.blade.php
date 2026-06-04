@extends('layouts.admin')
@php
$title = 'Manajemen SHU';
@endphp
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="page-title">Sisa Hasil Usaha (SHU)</h1>
    <a href="{{ route('admin.shu.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Periode SHU
    </a>
</div>

@if($periods->isEmpty())
    <div class="card p-12 text-center text-mony-muted">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
        </svg>
        <p>Belum ada periode SHU. Buat periode baru untuk memulai.</p>
    </div>
@else
    <div class="grid grid-cols-1 gap-4">
        @foreach($periods as $p)
        <div class="card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold text-mony-text">SHU Tahun {{ $p->year }}</h3>
                    <span class="badge-{{ ['open'=>'warning','closed'=>'info','published'=>'success'][$p->status] ?? 'gray' }} mt-1">{{ $p->status_label }}</span>
                </div>
                <div class="flex gap-2">
                    @if(!$p->isClosed())
                        <form method="POST" action="{{ route('admin.shu.calculate', $p) }}"
                              onsubmit="return confirm('Hitung dan distribusi SHU tahun {{ $p->year }}? Proses ini akan menimpa distribusi yang ada.')">
                            @csrf
                            <button class="btn-primary btn-sm">Hitung & Distribusi</button>
                        </form>
                    @endif
                    @if($p->status === 'closed')
                        <form method="POST" action="{{ route('admin.shu.publish', $p) }}"
                              onsubmit="return confirm('Publikasikan SHU tahun {{ $p->year }} ke anggota?')">
                            @csrf
                            <button class="btn-success btn-sm">Publikasikan</button>
                        </form>
                    @endif
                    <a href="{{ route('admin.shu.show', $p) }}" class="btn-outline btn-sm">Detail</a>
                    @if($p->isClosed())
                        <a href="{{ route('admin.shu.excel', $p) }}" class="btn-ghost btn-sm">Excel</a>
                        <a href="{{ route('admin.shu.pdf', $p) }}" class="btn-ghost btn-sm" target="_blank">PDF</a>
                    @endif
                </div>
            </div>

            @if($p->isClosed())
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                <div class="bg-green-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-green-700">Total Pendapatan</p>
                    <p class="font-semibold text-green-800 text-sm">Rp {{ number_format($p->total_income, 0, ',', '.') }}</p>
                </div>
                <div class="bg-red-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-red-700">Total Biaya</p>
                    <p class="font-semibold text-red-800 text-sm">Rp {{ number_format($p->total_expenses, 0, ',', '.') }}</p>
                </div>
                <div class="bg-primary/5 rounded-xl p-3 text-center">
                    <p class="text-xs text-primary">Total SHU</p>
                    <p class="font-bold text-primary text-sm">Rp {{ number_format($p->total_shu, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-mony-muted">Distribusi</p>
                    <p class="font-semibold text-mony-text text-sm">{{ $p->distributions_count }} Anggota</p>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
@endif
@endsection
