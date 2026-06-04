@extends('layouts.admin')
@php
$title = 'Detail Transaksi ' . $transaksi->reference_number;
@endphp
@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.gadai.index', ['tab' => 'aktif']) }}" class="btn-ghost btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <div>
            <h1 class="page-title">{{ $transaksi->reference_number }}</h1>
            <span class="badge-{{ $transaksi->status_color }} mt-1">{{ $transaksi->status_label }}</span>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.gadai.transaksi.pdf', $transaksi) }}" class="btn-outline btn-sm" target="_blank">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            PDF
        </a>
        @if($transaksi->status === 'aktif' && $transaksi->isOverdue())
            <form method="POST" action="{{ route('admin.gadai.transaksi.lelang', $transaksi) }}"
                  onsubmit="return confirm('Tandai sebagai menunggu lelang?')">
                @csrf
                <button class="btn-danger btn-sm">Tandai Lelang</button>
            </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
        {{-- Transaksi Info --}}
        <div class="card p-5">
            <h3 class="section-title mb-4">Informasi Transaksi</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-mony-muted">Barang</p>
                    <p class="font-medium">{{ $transaksi->jenisBarang?->name }}</p>
                </div>
                <div>
                    <p class="text-mony-muted">Lokasi Penyimpanan</p>
                    <p class="font-medium">{{ $transaksi->warehouse_location ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-mony-muted">Nilai Taksir</p>
                    <p class="font-semibold">Rp {{ number_format($transaksi->appraisal_value, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-mony-muted">Jumlah Pinjaman</p>
                    <p class="font-semibold text-primary">Rp {{ number_format($transaksi->loan_amount, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-mony-muted">Bunga per Bulan</p>
                    <p class="font-medium">{{ $transaksi->interest_rate }}% = Rp {{ number_format($transaksi->monthlyInterest(), 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-mony-muted">Total Tebus Sekarang</p>
                    <p class="font-semibold text-secondary">Rp {{ number_format($transaksi->totalRedemption(), 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-mony-muted">Tanggal Gadai</p>
                    <p class="font-medium">{{ $transaksi->pawn_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-mony-muted {{ $transaksi->isOverdue() ? 'text-red-600' : '' }}">Jatuh Tempo</p>
                    <p class="font-medium {{ $transaksi->isOverdue() ? 'text-red-600' : '' }}">
                        {{ $transaksi->due_date->format('d M Y') }}
                        @if($transaksi->isOverdue())
                            <span class="text-xs">(Lewat {{ abs($transaksi->daysUntilDue()) }} hari!)</span>
                        @else
                            <span class="text-xs text-mony-muted">({{ $transaksi->daysUntilDue() }} hari lagi)</span>
                        @endif
                    </p>
                </div>
                <div class="col-span-2">
                    <p class="text-mony-muted">Deskripsi Barang</p>
                    <p class="font-medium">{{ $transaksi->item_description }}</p>
                </div>
            </div>
        </div>

        {{-- Pembayaran History --}}
        <div class="card p-5">
            <h3 class="section-title mb-4">Riwayat Pembayaran</h3>
            @if($transaksi->pembayaran->count())
                <div class="space-y-3">
                    @foreach($transaksi->pembayaran->sortByDesc('submitted_at') as $bayar)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full {{ $bayar->payment_type === 'tebus' ? 'bg-blue-100' : 'bg-green-100' }} flex items-center justify-center">
                                <svg class="w-5 h-5 {{ $bayar->payment_type === 'tebus' ? 'text-blue-600' : 'text-green-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium">{{ $bayar->payment_type_label }}</p>
                                <p class="text-xs text-mony-muted">{{ $bayar->submitted_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">Rp {{ number_format($bayar->amount, 0, ',', '.') }}</p>
                            <span class="badge-{{ $bayar->status_color }} text-xs">{{ ucfirst($bayar->status) }}</span>
                        </div>
                        @if($bayar->transfer_proof_path)
                            <a href="{{ asset('storage/' . $bayar->transfer_proof_path) }}" target="_blank"
                               class="ml-3 text-primary text-xs hover:underline">Lihat Bukti</a>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-mony-muted">Belum ada pembayaran</p>
            @endif
        </div>

        {{-- Lelang form if menunggu_lelang --}}
        @if($transaksi->status === 'menunggu_lelang')
        <div class="card p-5 border-l-4 border-red-500">
            <h3 class="section-title text-red-600 mb-4">Proses Lelang</h3>
            <form method="POST" action="{{ route('admin.gadai.transaksi.proses-lelang', $transaksi) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Tanggal Lelang</label>
                        <input type="date" name="auction_date" class="form-input" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div>
                        <label class="form-label">Nilai Lelang (Rp)</label>
                        <input type="number" name="auction_value" class="form-input" min="0">
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-input" rows="2"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn-danger" onclick="return confirm('Proses lelang barang ini?')">Proses Lelang</button>
            </form>
        </div>
        @endif
    </div>

    {{-- Side Panel --}}
    <div class="space-y-4">
        <div class="card p-5">
            <h3 class="section-title mb-3">Anggota</h3>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                    {{ strtoupper(substr($transaksi->anggota?->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <p class="font-medium text-sm">{{ $transaksi->anggota?->name }}</p>
                    <p class="text-xs text-mony-muted">{{ $transaksi->anggota?->phone }}</p>
                </div>
            </div>
            <a href="{{ route('admin.anggota.show', $transaksi->anggota_id) }}" class="btn-outline btn-sm w-full">Lihat Profil</a>
        </div>

        {{-- Progress --}}
        <div class="card p-5">
            <h3 class="section-title mb-3">Progress Pembayaran</h3>
            @php
                $totalBulanAktif = max(1, $transaksi->pawn_date->diffInMonths(now()));
                $totalBungaDibayar = $transaksi->totalInterestPaid();
                $totalBungaHarusDibayar = $transaksi->monthlyInterest() * min($totalBulanAktif, 4);
                $pct = $totalBungaHarusDibayar > 0 ? min(100, ($totalBungaDibayar / $totalBungaHarusDibayar) * 100) : 0;
            @endphp
            <div class="mb-3">
                <div class="flex justify-between text-xs text-mony-muted mb-1">
                    <span>Bunga dibayar</span>
                    <span>{{ number_format($pct, 0) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-primary h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            <div class="text-sm space-y-1">
                <div class="flex justify-between">
                    <span class="text-mony-muted">Total bunga dibayar</span>
                    <span>Rp {{ number_format($totalBungaDibayar, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-mony-muted">Sisa bunga</span>
                    <span class="{{ $totalBungaHarusDibayar - $totalBungaDibayar > 0 ? 'text-red-600' : 'text-green-600' }}">
                        Rp {{ number_format(max(0, $totalBungaHarusDibayar - $totalBungaDibayar), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
