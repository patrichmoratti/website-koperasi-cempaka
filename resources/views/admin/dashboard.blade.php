@extends('layouts.admin')
@php
$title = 'Dashboard Admin';
@endphp
@section('content')

{{-- Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-mony-text tracking-tight">Dashboard Admin</h1>
    <p class="text-sm text-mony-muted mt-1">Selamat datang, {{ auth()->user()->name }}. Ini ringkasan hari ini.</p>
</div>

{{-- KPI Strip --}}
<div class="card p-0 mb-5 overflow-hidden">
    <div class="grid grid-cols-2 lg:grid-cols-4">
        @php
        $kpis = [
            [
                'label' => 'Anggota Aktif',
                'value' => number_format($stats['total_anggota_aktif']),
                'sub'   => 'anggota terdaftar',
                'bg'    => '#e7f0fb',
                'icon'  => 'color:#3B82F6',
                'path'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                'href'  => route('admin.anggota.index'),
            ],
            [
                'label' => 'Gadai Aktif',
                'value' => number_format($stats['total_gadai_aktif']),
                'sub'   => $stats['gadai_overdue'] > 0 ? $stats['gadai_overdue'].' lewat jatuh tempo' : 'semua dalam batas',
                'bg'    => 'var(--green-light)',
                'icon'  => 'color:var(--green)',
                'path'  => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'href'  => route('admin.gadai.index', ['tab' => 'aktif']),
                'alert' => $stats['gadai_overdue'] > 0,
            ],
            [
                'label' => 'Pinjaman Beredar',
                'value' => 'Rp '.number_format($stats['total_pinjaman'] / 1000000, 1).' jt',
                'sub'   => number_format($stats['total_pinjaman'], 0, ',', '.'),
                'bg'    => '#fdf1de',
                'icon'  => 'color:#D97706',
                'path'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'href'  => route('admin.gadai.index', ['tab' => 'aktif']),
            ],
            [
                'label' => 'Pendapatan Bulan Ini',
                'value' => 'Rp '.number_format($stats['pendapatan_bulan_ini'] / 1000000, 1).' jt',
                'sub'   => now()->isoFormat('MMMM YYYY'),
                'bg'    => '#ede9fe',
                'icon'  => 'color:#7C3AED',
                'path'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                'href'  => route('admin.laporan.keuangan'),
            ],
        ];
        @endphp
        @foreach($kpis as $i => $kpi)
        @php
            $border = '';
            if ($i % 2 === 0) $border .= 'border-r ';
            if ($i < 2) $border .= 'border-b lg:border-b-0 ';
            if ($i < 3) $border .= 'lg:border-r ';
        @endphp
        <a href="{{ $kpi['href'] }}"
           class="flex items-center gap-3 p-5 hover:bg-gray-50/60 transition-colors {{ $border }}"
           style="border-color:#eef3ea">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:{{ $kpi['bg'] }}">
                <svg class="w-5 h-5" style="{{ $kpi['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['path'] }}"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-mony-muted">{{ $kpi['label'] }}</p>
                <p class="text-xl font-bold text-mony-text leading-tight">{{ $kpi['value'] }}</p>
                <p class="text-xs {{ ($kpi['alert'] ?? false) ? 'text-red-500 font-medium' : 'text-mony-muted' }}">
                    {{ $kpi['sub'] }}
                </p>
            </div>
        </a>
        @endforeach
    </div>
</div>

{{-- Simpanan strip --}}
<div class="card p-0 mb-5 overflow-hidden">
    <div class="grid grid-cols-1 sm:grid-cols-3">
        @php
        $simpStrip = [
            ['label' => 'Total Simpanan', 'value' => 'Rp '.number_format($stats['total_simpanan'], 0, ',', '.'), 'sub' => 'seluruh jenis simpanan', 'style' => 'color:var(--green)', 'bg' => 'var(--green-light)'],
            ['label' => 'Gadai Selesai', 'value' => number_format(\App\Models\TransaksiGadai::whereIn('status',['ditebus','dilelang','selesai'])->count()), 'sub' => 'transaksi selesai', 'style' => 'color:#3B82F6', 'bg' => '#e7f0fb'],
            ['label' => 'Total Anggota', 'value' => number_format(\App\Models\User::where('role','anggota')->count()), 'sub' => number_format(\App\Models\User::where('role','anggota')->where('account_status','pending')->count()).' pending verifikasi', 'style' => 'color:#7C3AED', 'bg' => '#ede9fe'],
        ];
        @endphp
        @foreach($simpStrip as $i => $s)
        <div class="flex items-center gap-3 p-4 {{ $i < 2 ? 'border-b sm:border-b-0 sm:border-r' : '' }}"
             style="border-color:#eef3ea">
            <div class="w-2 h-10 rounded-full flex-shrink-0" style="background:{{ $s['bg'] }}; border:2px solid; border-color:{{ $s['style'] }};"></div>
            <div>
                <p class="text-xs text-mony-muted">{{ $s['label'] }}</p>
                <p class="text-base font-bold" style="{{ $s['style'] }}">{{ $s['value'] }}</p>
                <p class="text-xs text-mony-muted">{{ $s['sub'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">
    <div class="card p-5 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-mony-text">Tren Pendapatan Bunga — 12 Bulan</h3>
            <span class="text-xs text-mony-muted">{{ now()->isoFormat('YYYY') }}</span>
        </div>
        <div style="height:200px">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-mony-text mb-4">Distribusi Jenis Barang Gadai</h3>
        <div style="height:180px" class="flex items-center justify-center">
            <canvas id="jenisChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-mony-text mb-4">Pengajuan Gadai per Bulan</h3>
        <div style="height:180px">
            <canvas id="gadaiChart"></canvas>
        </div>
    </div>
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-mony-text mb-4">Simpanan Pokok vs Wajib — 6 Bulan</h3>
        <div style="height:180px">
            <canvas id="simpananChart"></canvas>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const GREEN  = getComputedStyle(document.documentElement).getPropertyValue('--green').trim() || '#1A3D2E';
    const AMBER  = '#D97706';
    const BLUE   = '#3B82F6';
    const PURPLE = '#7C3AED';

    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
    };

    // Income trend
    new Chart(document.getElementById('incomeChart'), {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                data: @json($incomeByMonth),
                borderColor: GREEN,
                backgroundColor: GREEN + '18',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: GREEN,
                pointRadius: 3,
                pointHoverRadius: 5,
            }]
        },
        options: {
            ...chartDefaults,
            scales: {
                y: { beginAtZero: true, grid: { color: '#f0f7ee' }, ticks: { color: '#888', callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'jt' } },
                x: { grid: { display: false }, ticks: { color: '#888' } }
            }
        }
    });

    // Jenis barang doughnut
    const jenisData = @json($jenisDistrib);
    const jenisColors = [GREEN, AMBER, BLUE, PURPLE, '#EF4444', '#10B981', '#F59E0B', '#EC4899'];
    new Chart(document.getElementById('jenisChart'), {
        type: 'doughnut',
        data: {
            labels: jenisData.map(d => d.label),
            datasets: [{ data: jenisData.map(d => d.count), backgroundColor: jenisColors, borderWidth: 2, borderColor: '#fff' }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } } }
    });

    // Gadai per bulan
    new Chart(document.getElementById('gadaiChart'), {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [{ data: @json($gadaiByMonth), backgroundColor: GREEN + 'CC', borderRadius: 6 }]
        },
        options: {
            ...chartDefaults,
            scales: {
                y: { beginAtZero: true, grid: { color: '#f0f7ee' }, ticks: { color: '#888', precision: 0 } },
                x: { grid: { display: false }, ticks: { color: '#888' } }
            }
        }
    });

    // Simpanan pokok vs wajib
    const simpLabels = @json(collect(range(5,0))->map(fn($i) => now()->subMonths($i)->isoFormat('MMM YY')));
    new Chart(document.getElementById('simpananChart'), {
        type: 'bar',
        data: {
            labels: simpLabels,
            datasets: [
                { label: 'Pokok', data: @json($simpananPokok), backgroundColor: BLUE + 'CC', borderRadius: 4 },
                { label: 'Wajib', data: @json($simpananWajib), backgroundColor: PURPLE + 'CC', borderRadius: 4 },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { boxWidth: 10, font: { size: 11 } } } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f0f7ee' }, ticks: { color: '#888', callback: v => 'Rp ' + (v/1000).toFixed(0) + 'rb' } },
                x: { grid: { display: false }, ticks: { color: '#888' } }
            }
        }
    });
});
</script>
@endsection