@extends('layouts.pengurus')
@php
$title = 'Detail Anggota';
@endphp
@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('pengurus.anggota.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">{{ $user->name }}</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 max-w-4xl">
    <div class="card p-5">
        <h3 class="section-title mb-4">Data Diri</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-mony-muted">Nama</span><span class="font-medium">{{ $user->name }}</span></div>
            <div class="flex justify-between"><span class="text-mony-muted">NIK</span><span class="font-mono text-xs">{{ $user->nik ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-mony-muted">Email</span><span>{{ $user->email }}</span></div>
            <div class="flex justify-between"><span class="text-mony-muted">HP</span><span>{{ $user->phone ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-mony-muted">Status</span>
                @php $c = ['pending'=>'warning','active'=>'success','rejected'=>'danger','suspended'=>'gray'] @endphp
                <span class="badge-{{ $c[$user->account_status] ?? 'gray' }}">{{ ucfirst($user->account_status) }}</span>
            </div>
        </div>
        <div class="mt-3">
            <p class="text-xs text-mony-muted">Alamat</p>
            <p class="text-sm">{{ $user->address ?? '-' }}</p>
        </div>
    </div>

    <div class="card p-5">
        <h3 class="section-title mb-4">Ringkasan Gadai & Simpanan</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-mony-muted">Gadai Aktif</span>
                <span class="font-medium">{{ $user->transaksiGadai->where('status','aktif')->count() }}</span></div>
            <div class="flex justify-between"><span class="text-mony-muted">Total Pinjaman Aktif</span>
                <span class="font-medium">Rp {{ number_format($user->transaksiGadai->where('status','aktif')->sum('loan_amount'), 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-mony-muted">Simpanan Pokok</span>
                <span>Rp {{ number_format($user->totalSimpananPokok(), 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-mony-muted">Simpanan Wajib</span>
                <span>Rp {{ number_format($user->totalSimpananWajib(), 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-mony-muted font-medium">Total Simpanan</span>
                <span class="font-semibold text-primary">Rp {{ number_format($user->totalSimpanan(), 0, ',', '.') }}</span></div>
        </div>
    </div>
</div>
@endsection
