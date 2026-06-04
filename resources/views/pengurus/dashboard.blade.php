@extends('layouts.pengurus')
@php
$title = 'Dashboard Pengurus';
@endphp
@section('content')
<div class="mb-6">
    <h1 class="page-title">Dashboard Pengurus</h1>
    <p class="text-sm text-mony-muted mt-1">Selamat datang, {{ auth()->user()->name }}</p>
</div>

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['Pengajuan Gadai', $stats['pending_pengajuan'], 'warning', 'admin.gadai.index?tab=pengajuan'],
        ['Pembayaran', $stats['pending_pembayaran'], 'danger', 'admin.konfirmasi.index?tab=pembayaran_gadai'],
        ['Simpanan', $stats['pending_simpanan'], 'info', 'admin.konfirmasi.index?tab=simpanan'],
        ['Registrasi', $stats['pending_registrasi'], 'primary', 'admin.konfirmasi.index?tab=registrasi'],
    ] as [$label, $count, $color, $route])
    <a href="{{ url($route) }}" class="stat-card hover:shadow-card-hover transition-shadow">
        <span class="stat-label">{{ $label }}</span>
        <span class="stat-value text-3xl {{ $count > 0 ? 'text-' . $color . '-600' : '' }}">{{ $count }}</span>
        <span class="badge-{{ $color }} w-fit">Pending</span>
    </a>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="card p-5">
        <h3 class="section-title mb-4">Pengajuan Gadai Terbaru</h3>
        @if($recentPengajuan->count())
            <div class="space-y-3">
                @foreach($recentPengajuan as $p)
                <div class="flex items-center justify-between p-3 bg-yellow-50 border border-yellow-100 rounded-xl">
                    <div>
                        <p class="font-medium text-sm">{{ $p->anggota?->name }}</p>
                        <p class="text-xs text-mony-muted">{{ $p->jenisBarang?->name }} · {{ $p->submitted_at->diffForHumans() }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">Rp {{ number_format($p->loan_request_amount, 0, ',', '.') }}</p>
                        <a href="{{ route('pengurus.konfirmasi.index', ['tab' => 'pengajuan']) }}" class="text-xs text-primary hover:underline">Proses →</a>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-mony-muted">Tidak ada pengajuan pending</p>
        @endif
    </div>

    <div class="card p-5">
        <h3 class="section-title mb-4">Pembayaran Terbaru</h3>
        @if($recentPembayaran->count())
            <div class="space-y-3">
                @foreach($recentPembayaran as $p)
                <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-100 rounded-xl">
                    <div>
                        <p class="font-medium text-sm">{{ $p->transaksi?->anggota?->name }}</p>
                        <p class="text-xs text-mony-muted">{{ $p->payment_type_label }} · {{ $p->submitted_at->diffForHumans() }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">Rp {{ number_format($p->amount, 0, ',', '.') }}</p>
                        <a href="{{ route('pengurus.konfirmasi.index', ['tab' => 'pembayaran_gadai']) }}" class="text-xs text-primary hover:underline">Konfirmasi →</a>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-mony-muted">Tidak ada pembayaran pending</p>
        @endif
    </div>
</div>
@endsection
