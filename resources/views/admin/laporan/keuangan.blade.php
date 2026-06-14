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

{{-- Summary Strip --}}
<div class="card p-0 mb-6 overflow-hidden">
    <div class="grid grid-cols-1 sm:grid-cols-3">
        @php
        $summaryStrip = [
            ['label' => 'Total Pemasukan Gadai', 'value' => 'Rp '.number_format($totalIncome, 0, ',', '.'), 'sub' => 'bunga & tebus', 'style' => 'color:var(--green)', 'bg' => 'var(--green-light)'],
            ['label' => 'Total Simpanan Masuk', 'value' => 'Rp '.number_format($totalSimpanan, 0, ',', '.'), 'sub' => 'pokok & wajib', 'style' => 'color:#3B82F6', 'bg' => '#e7f0fb'],
            ['label' => 'Pengajuan Gadai', 'value' => number_format($pengajuan->count()), 'sub' => 'Rp '.number_format($totalPengajuan, 0, ',', '.').' diajukan', 'style' => 'color:#D97706', 'bg' => '#fdf1de'],
        ];
        @endphp
        @foreach($summaryStrip as $i => $s)
        <div class="flex items-center gap-3 p-4 {{ $i < 2 ? 'border-b sm:border-b-0 sm:border-r' : '' }}" style="border-color:#eef3ea">
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

{{-- Chart --}}
<div class="card p-5 mb-6">
    <h3 class="section-title mb-4">Pemasukan Gadai vs Simpanan Masuk per Bulan ({{ $year }})</h3>
    <div class="h-64">
        <canvas id="keuanganChart"></canvas>
    </div>
</div>

{{-- Tables --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="card overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="section-title">Pendapatan Bunga & Tebus</h3>
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
            <h3 class="section-title">Pengajuan Gadai</h3>
        </div>
        <div class="max-h-64 overflow-y-auto">
            <table class="table-base">
                <thead><tr><th>Anggota</th><th>Barang</th><th>Nilai Diajukan</th><th>Status</th><th>Tanggal</th></tr></thead>
                <tbody>
                    @foreach($pengajuan->take(20) as $p)
                    <tr>
                        <td class="text-xs">{{ $p->anggota?->name }}</td>
                        <td class="text-xs text-mony-muted">{{ $p->jenisBarang?->name }}</td>
                        <td class="font-medium text-sm">Rp {{ number_format($p->loan_request_amount, 0, ',', '.') }}</td>
                        <td><span class="badge-{{ $p->status_color }} text-xs">{{ $p->status_label }}</span></td>
                        <td class="text-xs text-mony-muted">{{ $p->submitted_at?->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                    @if($pengajuan->isEmpty())
                        <tr><td colspan="5" class="text-center py-4 text-mony-muted text-sm">Tidak ada data</td></tr>
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
                { label: 'Pemasukan Gadai', data: @json($monthlyIncome), backgroundColor: '#00BFA580', borderColor: '#00BFA5', borderWidth: 1, borderRadius: 4 },
                { label: 'Simpanan Masuk', data: @json($monthlySimpanan), backgroundColor: '#3B82F680', borderColor: '#3B82F6', borderWidth: 1, borderRadius: 4 },
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
