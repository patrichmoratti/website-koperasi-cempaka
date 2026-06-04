<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1A1A2E; margin: 15px; }
    .header { text-align: center; border-bottom: 2px solid #00BFA5; padding-bottom: 8px; margin-bottom: 12px; }
    .header h1 { color: #00BFA5; font-size: 14px; margin: 0; }
    table { width: 100%; border-collapse: collapse; margin: 8px 0; }
    th { background: #f0fdfb; padding: 5px 6px; text-align: left; font-size: 8px; color: #6B7280; border: 1px solid #e5e7eb; }
    td { padding: 4px 6px; border: 1px solid #f3f4f6; font-size: 8px; }
    .total-row { background: #f0fdfb; font-weight: bold; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ $info->name }}</h1>
    <p>LAPORAN DISTRIBUSI SHU TAHUN {{ $period?->year }}</p>
    <p style="font-size:8px; color:#6B7280;">Dicetak: {{ now()->isoFormat('D MMMM YYYY') }}</p>
</div>

<table>
    <tr><td style="color:#6B7280; width:30%;">Total Pendapatan</td><td>Rp {{ number_format($period?->total_income, 0, ',', '.') }}</td>
        <td style="color:#6B7280; width:30%;">Total Biaya</td><td>Rp {{ number_format($period?->total_expenses, 0, ',', '.') }}</td></tr>
    <tr><td style="color:#6B7280;">Total SHU</td><td><strong>Rp {{ number_format($period?->total_shu, 0, ',', '.') }}</strong></td>
        <td style="color:#6B7280;">Jasa Modal ({{ $period?->pct_jasa_modal }}%)</td><td>Rp {{ number_format($period?->alloc_jasa_modal, 0, ',', '.') }}</td></tr>
    <tr><td style="color:#6B7280;">Jasa Usaha ({{ $period?->pct_jasa_usaha }}%)</td><td>Rp {{ number_format($period?->alloc_jasa_usaha, 0, ',', '.') }}</td>
        <td style="color:#6B7280;">Jumlah Anggota</td><td>{{ $distributions->count() }}</td></tr>
</table>

<p style="font-size:10px; font-weight:bold; margin-top:10px;">Detail Distribusi per Anggota</p>
<table>
    <thead><tr>
        <th>No</th><th>Nama Anggota</th>
        <th>Total Simpanan</th><th>Proporsi Simpanan</th><th>Jasa Modal</th>
        <th>Total Bunga Bayar</th><th>Proporsi Bunga</th><th>Jasa Usaha</th>
        <th>Total SHU</th><th>Status</th>
    </tr></thead>
    <tbody>
        @php $totalShu = 0; @endphp
        @foreach($distributions as $i => $d)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $d->anggota?->name }}</td>
            <td>Rp {{ number_format($d->total_savings, 0, ',', '.') }}</td>
            <td>{{ number_format($d->member_savings_proportion * 100, 2) }}%</td>
            <td>Rp {{ number_format($d->member_jasa_modal, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($d->total_interest_paid, 0, ',', '.') }}</td>
            <td>{{ number_format($d->member_interest_proportion * 100, 2) }}%</td>
            <td>Rp {{ number_format($d->member_jasa_usaha, 0, ',', '.') }}</td>
            <td><strong>Rp {{ number_format($d->total_shu_received, 0, ',', '.') }}</strong></td>
            <td>{{ $d->withdrawal_status === 'withdrawn' ? 'Dicairkan' : 'Pending' }}</td>
        </tr>
        @php $totalShu += $d->total_shu_received; @endphp
        @endforeach
        <tr class="total-row">
            <td colspan="8" style="text-align:right;">TOTAL SHU DIDISTRIBUSIKAN</td>
            <td>Rp {{ number_format($totalShu, 0, ',', '.') }}</td>
            <td></td>
        </tr>
    </tbody>
</table>
</body>
</html>
