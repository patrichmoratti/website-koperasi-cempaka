@extends('layouts.admin')
@php
$title = 'Laporan Keuangan';
@endphp
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="page-title">Laporan Keuangan</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.laporan.keuangan.pdf', request()->all()) }}" target="_blank" class="btn-outline btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export PDF
        </a>
    </div>
</div>

<div class="card p-4 mb-6">
    <form method="GET" class="flex gap-3 items-end">
        <div>
            <label class="form-label">Tahun</label>
            <select name="year" class="form-input">
                @foreach(range(now()->year, 2020) as $y)
                    <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Bulan (opsional)</label>
            <select name="month" class="form-input">
                <option value="">Semua Bulan</option>
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" @selected($month == $m)>{{ \Carbon\Carbon::create(null,$m)->isoFormat('MMMM') }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="stat-card bg-green-50 border-green-100">
        <span class="stat-label text-green-700">Total Pemasukan</span>
        <span class="stat-value text-xl text-green-700">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
    </div>
    <div class="stat-card bg-red-50 border-red-100">
        <span class="stat-label text-red-700">Total Pengeluaran</span>
        <span class="stat-value text-xl text-red-700">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
    </div>
    <div class="stat-card {{ $laba >= 0 ? 'bg-primary/5' : 'bg-red-50' }}">
        <span class="stat-label">Laba Bersih</span>
        <span class="stat-value text-xl {{ $laba >= 0 ? 'text-primary' : 'text-red-600' }}">
            Rp {{ number_format(abs($laba), 0, ',', '.') }}
            {{ $laba < 0 ? '(Rugi)' : '' }}
        </span>
    </div>
</div>

{{-- Chart --}}
<div class="card p-5 mb-6">
    <h3 class="section-title mb-4">Pemasukan vs Pengeluaran per Bulan ({{ $year }})</h3>
    <div class="h-64">
        <canvas id="keuanganChart"></canvas>
    </div>
</div>

{{-- Tables --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="card overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="section-title">Pendapatan Bunga</h3>
        </div>
        <div class="max-h-64 overflow-y-auto">
            <table class="table-base">
                <thead><tr><th>Anggota</th><th>Tipe</th><th>Jumlah</th><th>Tanggal</th></tr></thead>
                <tbody>
                    @foreach($pendapatan->take(20) as $p)
                    <tr>
                        <td class="text-xs">{{ $p->transaksi?->anggota?->name }}</td>
                        <td><span class="badge-{{ $p->payment_type === 'tebus' ? 'info' : 'success' }} text-xs">{{ $p->payment_type_label }}</span></td>
                        <td class="font-medium text-sm">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                        <td class="text-xs text-mony-muted">{{ $p->confirmed_at?->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                    @if($pendapatan->isEmpty())
                        <tr><td colspan="4" class="text-center py-4 text-mony-muted text-sm">Tidak ada data</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="section-title">Biaya Operasional</h3>
        </div>
        <div class="max-h-64 overflow-y-auto">
            <table class="table-base">
                <thead><tr><th>Kategori</th><th>Keterangan</th><th>Jumlah</th><th>Tanggal</th></tr></thead>
                <tbody>
                    @foreach($biaya->take(20) as $b)
                    <tr>
                        <td class="text-xs font-medium">{{ $b->category }}</td>
                        <td class="text-xs text-mony-muted">{{ Str::limit($b->description, 40) }}</td>
                        <td class="font-medium text-sm">Rp {{ number_format($b->amount, 0, ',', '.') }}</td>
                        <td class="text-xs text-mony-muted">{{ $b->date->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                    @if($biaya->isEmpty())
                        <tr><td colspan="4" class="text-center py-4 text-mony-muted text-sm">Tidak ada data</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('keuanganChart'), {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [
                { label: 'Pemasukan', data: @json($monthlyIncome), backgroundColor: '#00BFA580', borderColor: '#00BFA5', borderWidth: 1, borderRadius: 4 },
                { label: 'Pengeluaran', data: @json($monthlyExpense), backgroundColor: '#EF444480', borderColor: '#EF4444', borderWidth: 1, borderRadius: 4 },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'jt' } } }
        }
    });
});
</script>
@endsection
