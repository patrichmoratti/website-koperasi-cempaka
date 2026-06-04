<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1A1A2E; margin: 20px; }
    .header { text-align: center; border-bottom: 2px solid #00BFA5; padding-bottom: 10px; margin-bottom: 15px; }
    .header h1 { color: #00BFA5; font-size: 16px; margin: 0; }
    .header p { color: #6B7280; margin: 3px 0; font-size: 9px; }
    .report-title { font-size: 14px; font-weight: bold; margin: 10px 0; }
    .summary { display: flex; gap: 15px; margin: 10px 0; }
    .summary-box { flex: 1; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px; text-align: center; }
    .summary-box .label { font-size: 8px; color: #6B7280; }
    .summary-box .value { font-size: 12px; font-weight: bold; margin-top: 3px; }
    .green { color: #10B981; } .red { color: #EF4444; } .teal { color: #00BFA5; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th { background: #f9fafb; padding: 6px 8px; text-align: left; font-size: 9px; color: #6B7280; border-bottom: 1px solid #e5e7eb; }
    td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; font-size: 9px; }
    .signature { margin-top: 30px; display: flex; justify-content: space-around; }
    .sig-box { text-align: center; }
    .sig-box .line { border-top: 1px solid #1A1A2E; width: 120px; margin: 30px auto 3px; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ $info->name }}</h1>
    <p>{{ $info->address }}</p>
    <p>Telp: {{ $info->phone }} | Email: {{ $info->email }}</p>
</div>

<p class="report-title">LAPORAN KEUANGAN {{ $month ? \Carbon\Carbon::create($year, $month)->isoFormat('MMMM') . ' ' : '' }}TAHUN {{ $year }}</p>
<p style="font-size:9px; color:#6B7280;">Dicetak: {{ now()->isoFormat('D MMMM YYYY, HH:mm') }}</p>

<div class="summary" style="display:table; width:100%;">
    <div style="display:table-cell; width:33%; border:1px solid #e5e7eb; border-radius:4px; padding:8px; text-align:center;">
        <div style="font-size:8px; color:#6B7280;">TOTAL PEMASUKAN</div>
        <div style="font-size:13px; font-weight:bold; color:#10B981;">Rp {{ number_format($totalIn, 0, ',', '.') }}</div>
    </div>
    <div style="display:table-cell; width:33%; padding-left:8px; border:1px solid #e5e7eb; border-radius:4px; padding:8px; text-align:center;">
        <div style="font-size:8px; color:#6B7280;">TOTAL PENGELUARAN</div>
        <div style="font-size:13px; font-weight:bold; color:#EF4444;">Rp {{ number_format($totalOut, 0, ',', '.') }}</div>
    </div>
    <div style="display:table-cell; width:33%; padding-left:8px; border:1px solid #00BFA5; border-radius:4px; padding:8px; text-align:center;">
        <div style="font-size:8px; color:#6B7280;">LABA BERSIH</div>
        <div style="font-size:13px; font-weight:bold; color:#00BFA5;">Rp {{ number_format($totalIn - $totalOut, 0, ',', '.') }}</div>
    </div>
</div>

<p style="font-size:11px; font-weight:bold; margin-top:15px;">Detail Pendapatan Bunga</p>
<table>
    <thead><tr><th>Anggota</th><th>Tipe</th><th>Jumlah</th><th>Tgl Konfirmasi</th></tr></thead>
    <tbody>
        @foreach($pendapatan as $p)
        <tr>
            <td>{{ $p->transaksi?->anggota?->name }}</td>
            <td>{{ $p->payment_type_label }}</td>
            <td>Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
            <td>{{ $p->confirmed_at?->format('d/m/Y') }}</td>
        </tr>
        @endforeach
        <tr style="font-weight:bold; background:#f9fafb;">
            <td colspan="2">TOTAL</td>
            <td>Rp {{ number_format($totalIn, 0, ',', '.') }}</td>
            <td></td>
        </tr>
    </tbody>
</table>

<p style="font-size:11px; font-weight:bold; margin-top:10px;">Detail Biaya Operasional</p>
<table>
    <thead><tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th>Jumlah</th></tr></thead>
    <tbody>
        @foreach($biaya as $b)
        <tr>
            <td>{{ $b->date->format('d/m/Y') }}</td>
            <td>{{ $b->category }}</td>
            <td>{{ $b->description }}</td>
            <td>Rp {{ number_format($b->amount, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr style="font-weight:bold; background:#f9fafb;">
            <td colspan="3">TOTAL</td>
            <td>Rp {{ number_format($totalOut, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

<div class="signature">
    <div class="sig-box">
        <p>Mengetahui,</p>
        <div class="line"></div>
        <p>Ketua Koperasi</p>
    </div>
    <div class="sig-box">
        <p>Dibuat oleh,</p>
        <div class="line"></div>
        <p>Pengurus</p>
    </div>
</div>
</body>
</html>
