<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1A1A2E; margin: 20px; }
    .header { text-align: center; border-bottom: 2px solid #00BFA5; padding-bottom: 10px; margin-bottom: 15px; }
    .header h1 { color: #00BFA5; font-size: 16px; margin: 0; }
    .header p { color: #6B7280; font-size: 9px; margin: 3px 0; }
    .title { font-size: 14px; font-weight: bold; margin: 10px 0; }
    .ref { background: #00BFA5; color: white; display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th { background: #f9fafb; padding: 6px 8px; text-align: left; font-size: 9px; color: #6B7280; border-bottom: 1px solid #e5e7eb; }
    td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; font-size: 9px; }
    .highlight { background: #f0fdfb; border: 1px solid #00BFA5; border-radius: 6px; padding: 10px; margin: 10px 0; }
    .signature { margin-top: 30px; display: table; width: 100%; }
    .sig-box { display: table-cell; text-align: center; }
    .sig-line { border-top: 1px solid #1A1A2E; width: 120px; margin: 30px auto 3px; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ $info->name }}</h1>
    <p>{{ $info->address }} | {{ $info->phone }} | {{ $info->email }}</p>
</div>

<p class="title">SURAT KETERANGAN GADAI</p>
<div class="ref">{{ $transaksi->reference_number }}</div>

<table>
    <tr><td style="width:40%; color:#6B7280;">Anggota</td><td><strong>{{ $transaksi->anggota?->name }}</strong></td></tr>
    <tr><td style="color:#6B7280;">NIK</td><td>{{ $transaksi->anggota?->nik }}</td></tr>
    <tr><td style="color:#6B7280;">Barang Digadai</td><td>{{ $transaksi->jenisBarang?->name }}</td></tr>
    <tr><td style="color:#6B7280;">Deskripsi</td><td>{{ $transaksi->item_description }}</td></tr>
    <tr><td style="color:#6B7280;">Nilai Taksir</td><td><strong>Rp {{ number_format($transaksi->appraisal_value, 0, ',', '.') }}</strong></td></tr>
    <tr><td style="color:#6B7280;">Lokasi Penyimpanan</td><td>{{ $transaksi->warehouse_location ?? '-' }}</td></tr>
</table>

<div class="highlight">
    <table style="margin:0;">
        <tr>
            <td style="width:33%; text-align:center;"><div style="color:#6B7280; font-size:8px;">JUMLAH PINJAMAN</div><div style="font-size:13px; font-weight:bold; color:#00BFA5;">Rp {{ number_format($transaksi->loan_amount, 0, ',', '.') }}</div></td>
            <td style="width:33%; text-align:center;"><div style="color:#6B7280; font-size:8px;">BUNGA/BULAN (8%)</div><div style="font-size:13px; font-weight:bold; color:#F59E0B;">Rp {{ number_format($transaksi->monthlyInterest(), 0, ',', '.') }}</div></td>
            <td style="width:33%; text-align:center;"><div style="color:#6B7280; font-size:8px;">JATUH TEMPO</div><div style="font-size:13px; font-weight:bold; color:#EF4444;">{{ $transaksi->due_date->format('d M Y') }}</div></td>
        </tr>
    </table>
</div>

<table>
    <tr><td style="color:#6B7280;">Tanggal Gadai</td><td>{{ $transaksi->pawn_date->format('d M Y') }}</td></tr>
    <tr><td style="color:#6B7280;">Status</td><td>{{ $transaksi->status_label }}</td></tr>
    <tr><td style="color:#6B7280;">Total Tebus Sekarang</td><td><strong>Rp {{ number_format($transaksi->totalRedemption(), 0, ',', '.') }}</strong></td></tr>
</table>

<p style="font-size:9px; color:#6B7280; margin-top:15px;">
    Barang yang tidak ditebus dalam 5 bulan dari tanggal gadai akan diproses untuk dilelang sesuai syarat dan ketentuan koperasi.
</p>

<div class="signature">
    <div class="sig-box">
        <p>Mengetahui,</p>
        <div class="sig-line"></div>
        <p>Ketua Koperasi</p>
    </div>
    <div class="sig-box">
        <p>Anggota</p>
        <div class="sig-line"></div>
        <p>{{ $transaksi->anggota?->name }}</p>
    </div>
</div>
</body>
</html>
