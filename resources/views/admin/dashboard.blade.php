@extends('layouts.admin')
@php
$title = 'Dashboard Admin';
@endphp
@section('content')
    <div class="mb-6">
        <h1 class="page-title">Dashboard Admin</h1>
        <p class="text-sm text-mony-muted mt-1">Selamat datang, {{ auth()->user()->name }}. Ini ringkasan hari ini.</p>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="stat-card col-span-1">
            <div class="flex items-center justify-between">
                <span class="stat-label">Anggota Aktif</span>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <span class="stat-value">{{ number_format($stats['total_anggota_aktif']) }}</span>
            <span class="stat-change text-blue-600">Anggota</span>
        </div>

        <div class="stat-card col-span-1">
            <div class="flex items-center justify-between">
                <span class="stat-label">Gadai Aktif</span>
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <span class="stat-value">{{ number_format($stats['total_gadai_aktif']) }}</span>
            <span class="stat-change text-green-600">Transaksi</span>
        </div>

        <div class="stat-card col-span-1">
            <div class="flex items-center justify-between">
                <span class="stat-label">Total Pinjaman</span>
                <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <span class="stat-value text-lg">{{ 'Rp ' . number_format($stats['total_pinjaman'], 0, ',', '.') }}</span>
            <span class="stat-change text-primary">Beredar</span>
        </div>

        <div class="stat-card col-span-1">
            <div class="flex items-center justify-between">
                <span class="stat-label">Total Simpanan</span>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
            </div>
            <span class="stat-value text-lg">{{ 'Rp ' . number_format($stats['total_simpanan'], 0, ',', '.') }}</span>
            <span class="stat-change text-purple-600">Terkumpul</span>
        </div>

        <div class="stat-card col-span-1">
            <div class="flex items-center justify-between">
                <span class="stat-label">Pendapatan Bln Ini</span>
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <span class="stat-value text-lg">{{ 'Rp ' . number_format($stats['pendapatan_bulan_ini'], 0, ',', '.') }}</span>
            <span class="stat-change text-yellow-600">Bulan ini</span>
        </div>

        <div class="stat-card col-span-1">
            <div class="flex items-center justify-between">
                <span class="stat-label">Pending Hari Ini</span>
                <div class="w-8 h-8 {{ $stats['pending_hari_ini'] > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 {{ $stats['pending_hari_ini'] > 0 ? 'text-red-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <span class="stat-value {{ $stats['pending_hari_ini'] > 0 ? 'text-red-600' : '' }}">{{ $stats['pending_hari_ini'] }}</span>
            <span class="stat-change {{ $stats['pending_hari_ini'] > 0 ? 'text-red-500' : 'text-mony-muted' }}">Pengajuan baru</span>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        {{-- Income Trend --}}
        <div class="card p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="section-title">Tren Pendapatan Bunga (12 Bulan)</h3>
            </div>
            <div class="h-56">
                <canvas id="incomeChart"></canvas>
            </div>
        </div>

        {{-- Jenis Barang Doughnut --}}
        <div class="card p-5">
            <h3 class="section-title mb-4">Distribusi Jenis Barang Gadai</h3>
            <div class="h-48 flex items-center justify-center">
                <canvas id="jenisChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        {{-- Gadai per bulan --}}
        <div class="card p-5">
            <h3 class="section-title mb-4">Pengajuan Gadai per Bulan</h3>
            <div class="h-48">
                <canvas id="gadaiChart"></canvas>
            </div>
        </div>

        {{-- Simpanan pokok vs wajib --}}
        <div class="card p-5">
            <h3 class="section-title mb-4">Simpanan Pokok vs Wajib (6 Bulan)</h3>
            <div class="h-48">
                <canvas id="simpananChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <a href="{{ route('admin.konfirmasi.index') }}" class="card p-4 flex flex-col items-center gap-2 hover:shadow-card-hover transition-shadow text-center">
            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-mony-text">Konfirmasi</span>
        </a>
        <a href="{{ route('admin.gadai.index') }}" class="card p-4 flex flex-col items-center gap-2 hover:shadow-card-hover transition-shadow text-center">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-mony-text">Gadai</span>
        </a>
        <a href="{{ route('admin.laporan.keuangan') }}" class="card p-4 flex flex-col items-center gap-2 hover:shadow-card-hover transition-shadow text-center">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-mony-text">Laporan</span>
        </a>
        <a href="{{ route('admin.shu.index') }}" class="card p-4 flex flex-col items-center gap-2 hover:shadow-card-hover transition-shadow text-center">
            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-mony-text">SHU</span>
        </a>
    </div>

    {{-- Recent Activity --}}
    <div class="card p-5">
        <h3 class="section-title mb-4">Aktivitas Terbaru</h3>
        @if($recentActivity->count())
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Anggota</th>
                            <th>Jenis Barang</th>
                            <th>Nilai Estimasi</th>
                            <th>Pinjaman Diminta</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentActivity as $item)
                        <tr>
                            <td class="font-medium">{{ $item->anggota?->name }}</td>
                            <td>{{ $item->jenisBarang?->name }}</td>
                            <td>Rp {{ number_format($item->estimated_value, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->loan_request_amount, 0, ',', '.') }}</td>
                            <td><span class="badge-{{ $item->status_color }}">{{ $item->status_label }}</span></td>
                            <td class="text-mony-muted text-xs">{{ $item->submitted_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ route('admin.gadai.pengajuan', $item) }}" class="btn-ghost btn-sm btn-icon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-mony-muted">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm">Belum ada aktivitas terbaru</p>
            </div>
        @endif
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const TEAL = '#00BFA5';
        const AMBER = '#FFB300';
        const BLUE = '#3B82F6';
        const PURPLE = '#8B5CF6';

        // Income trend
        new Chart(document.getElementById('incomeChart'), {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Pendapatan Bunga',
                    data: @json($incomeByMonth),
                    borderColor: TEAL,
                    backgroundColor: TEAL + '20',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: TEAL,
                    pointRadius: 4,
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'jt' } } } }
        });

        // Jenis barang doughnut
        const jenisData = @json($jenisDistrib);
        const jenisColors = ['#00BFA5','#FFB300','#3B82F6','#8B5CF6','#EF4444','#10B981','#F59E0B','#EC4899'];
        new Chart(document.getElementById('jenisChart'), {
            type: 'doughnut',
            data: {
                labels: jenisData.map(d => d.label),
                datasets: [{
                    data: jenisData.map(d => d.count),
                    backgroundColor: jenisColors,
                    borderWidth: 0,
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });

        // Gadai per bulan
        new Chart(document.getElementById('gadaiChart'), {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Pengajuan',
                    data: @json($gadaiByMonth),
                    backgroundColor: TEAL + 'CC',
                    borderRadius: 6,
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });

        // Simpanan
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
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + (v/1000).toFixed(0) + 'rb' } } } }
        });
    });
    </script>
@endsection
