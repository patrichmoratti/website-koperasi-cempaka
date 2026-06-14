@extends('layouts.pengurus')
@php
$title = 'Dashboard Pengurus';
@endphp
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-mony-text tracking-tight">Dashboard Pengurus</h1>
    <p class="text-sm text-mony-muted mt-1">Selamat datang, {{ auth()->user()->name }}</p>
</div>

{{-- KPI Strip --}}
<div class="card p-0 mb-6 overflow-hidden">
    <div class="grid grid-cols-2 lg:grid-cols-4">
        @php
        $kpis = [
            ['label' => 'Pengajuan Gadai', 'count' => $stats['pending_pengajuan'], 'color' => 'warning',  'bg' => '#fdf1de', 'icon' => 'text-amber-600',  'route' => 'admin.gadai.index?tab=pengajuan',
                'path' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'Pembayaran',     'count' => $stats['pending_pembayaran'], 'color' => 'danger', 'bg' => '#fde2e2', 'icon' => 'text-red-600',    'route' => 'admin.konfirmasi.index?tab=pembayaran_gadai',
                'path' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Simpanan',       'count' => $stats['pending_simpanan'], 'color' => 'info',    'bg' => '#e7f0fb', 'icon' => 'text-blue-600',   'route' => 'admin.konfirmasi.index?tab=simpanan',
                'path' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
            ['label' => 'Registrasi',     'count' => $stats['pending_registrasi'], 'color' => 'primary', 'bg' => 'var(--green-light)', 'icon' => '', 'iconStyle' => 'color:var(--green)', 'route' => 'admin.konfirmasi.index?tab=registrasi',
                'path' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ];
        @endphp
        @foreach($kpis as $i => $kpi)
        @php
            $borderClasses = ($i % 2 === 0 ? 'border-r ' : '')
                . ($i < 3 ? 'lg:border-r ' : '')
                . ($i >= 2 ? 'border-t lg:border-t-0' : '');
        @endphp
        <a href="{{ url($kpi['route']) }}"
           class="flex items-center gap-3 p-5 hover:bg-gray-50/60 transition-colors {{ $borderClasses }}"
           style="border-color:#eef3ea">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:{{ $kpi['bg'] }}">
                <svg class="w-5 h-5 {{ $kpi['icon'] }}" style="{{ $kpi['iconStyle'] ?? '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['path'] }}"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-mony-muted">{{ $kpi['label'] }}</p>
                <p class="text-xl font-bold text-mony-text leading-tight">{{ $kpi['count'] }}
                    @if($kpi['count'] > 0)<span class="badge-{{ $kpi['color'] }} text-xs ml-1 align-middle">Pending</span>@endif
                </p>
            </div>
        </a>
        @endforeach
    </div>
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
