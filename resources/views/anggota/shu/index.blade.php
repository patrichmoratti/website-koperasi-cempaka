@extends('layouts.anggota')
@php
$title = 'SHU Saya';
@endphp
@section('content')
<h1 class="page-title mb-6">Sisa Hasil Usaha (SHU)</h1>

@if($distributions->isEmpty())
    <div class="card p-12 text-center text-mony-muted">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
        </svg>
        <p>Belum ada data SHU yang dipublikasikan</p>
    </div>
@else
    <div class="card p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="section-title">Total SHU Diterima</h3>
        </div>
        <p class="text-4xl font-bold text-primary">Rp {{ number_format($totalShu, 0, ',', '.') }}</p>
        <p class="text-sm text-mony-muted mt-1">Dari {{ $distributions->count() }} periode</p>
    </div>

    <div class="space-y-4">
        @foreach($distributions as $d)
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="section-title">SHU Tahun {{ $d->period?->year }}</h3>
                <span class="badge-{{ $d->withdrawal_status === 'withdrawn' ? 'success' : 'warning' }}">
                    {{ $d->withdrawal_status === 'withdrawn' ? 'Sudah Dicairkan' : 'Belum Dicairkan' }}
                </span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-gray-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-mony-muted">Total Simpanan</p>
                    <p class="font-semibold text-sm">Rp {{ number_format($d->total_savings, 0, ',', '.') }}</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-blue-700">Jasa Modal</p>
                    <p class="font-semibold text-sm text-blue-800">Rp {{ number_format($d->member_jasa_modal, 0, ',', '.') }}</p>
                    <p class="text-xs text-blue-600">{{ number_format($d->member_savings_proportion * 100, 2) }}% dari total</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-purple-700">Jasa Usaha</p>
                    <p class="font-semibold text-sm text-purple-800">Rp {{ number_format($d->member_jasa_usaha, 0, ',', '.') }}</p>
                    <p class="text-xs text-purple-600">{{ number_format($d->member_interest_proportion * 100, 2) }}% dari total</p>
                </div>
                <div class="bg-primary/5 rounded-xl p-3 text-center">
                    <p class="text-xs text-primary">Total SHU</p>
                    <p class="font-bold text-lg text-primary">Rp {{ number_format($d->total_shu_received, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
