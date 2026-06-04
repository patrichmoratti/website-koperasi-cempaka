@extends('layouts.admin')
@php
$title = 'Detail Pengajuan';
@endphp
@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('admin.gadai.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">Detail Pengajuan Gadai</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
        {{-- Info Barang --}}
        <div class="card p-5">
            <h3 class="section-title mb-4">Informasi Barang</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-mony-muted">Jenis Barang</p>
                    <p class="font-medium">{{ $pengajuan->jenisBarang?->name }}</p>
                </div>
                <div>
                    <p class="text-mony-muted">Kondisi</p>
                    <p class="font-medium">{{ $pengajuan->condition }}</p>
                </div>
                <div>
                    <p class="text-mony-muted">Berat / Jumlah</p>
                    <p class="font-medium">{{ $pengajuan->weight_or_quantity ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-mony-muted">Tanggal Pengajuan</p>
                    <p class="font-medium">{{ $pengajuan->submitted_at->format('d M Y H:i') }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-mony-muted">Deskripsi</p>
                    <p class="font-medium">{{ $pengajuan->description }}</p>
                </div>
                <div>
                    <p class="text-mony-muted">Estimasi Nilai Anggota</p>
                    <p class="font-semibold text-lg">Rp {{ number_format($pengajuan->estimated_value, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-mony-muted">Pinjaman Diminta</p>
                    <p class="font-semibold text-lg text-primary">Rp {{ number_format($pengajuan->loan_request_amount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Foto Barang --}}
        @if($pengajuan->item_photo_paths)
        <div class="card p-5">
            <h3 class="section-title mb-3">Foto Barang</h3>
            <div class="grid grid-cols-3 gap-3">
                @foreach($pengajuan->item_photo_paths as $photo)
                    <img src="{{ asset('storage/' . $photo) }}" alt="Foto barang"
                         class="w-full h-32 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-90"
                         onclick="window.open('{{ asset('storage/' . $photo) }}')">
                @endforeach
            </div>
        </div>
        @endif

        {{-- Action --}}
        @if($pengajuan->status === 'proses')
        <div class="card p-5" x-data="{ showReject: false }">
            <h3 class="section-title mb-4">Proses Pengajuan</h3>

            <form method="POST" action="{{ route('admin.gadai.pengajuan.approve', $pengajuan) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nilai Taksir (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="appraisal_value"
                               value="{{ old('appraisal_value', $pengajuan->estimated_value) }}"
                               class="form-input @error('appraisal_value') border-red-400 @enderror"
                               min="1" required>
                        @error('appraisal_value') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Jumlah Pinjaman Disetujui (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="loan_amount"
                               value="{{ old('loan_amount', $pengajuan->loan_request_amount) }}"
                               class="form-input @error('loan_amount') border-red-400 @enderror"
                               min="1" required>
                        @error('loan_amount') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Lokasi Penyimpanan</label>
                        <input type="text" name="warehouse_location" class="form-input" placeholder="Contoh: Rak A-01">
                    </div>
                </div>
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
                    <strong>Info:</strong> Dengan menyetujui, sistem akan membuat transaksi gadai otomatis. Bunga 8%/bulan, jatuh tempo 4 bulan.
                </div>
                <button type="submit" class="btn-success" onclick="return confirm('Setujui pengajuan ini?')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui Pengajuan
                </button>
            </form>

            <div class="mt-4 pt-4 border-t border-gray-100">
                <button @click="showReject = !showReject" class="btn-danger">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Tolak Pengajuan
                </button>
                <div x-show="showReject" class="mt-4">
                    <form method="POST" action="{{ route('admin.gadai.pengajuan.reject', $pengajuan) }}">
                        @csrf
                        <label class="form-label">Alasan Penolakan</label>
                        <textarea name="reason" class="form-input mb-3" rows="3" placeholder="Jelaskan alasan penolakan..." required></textarea>
                        <button type="submit" class="btn-danger btn-sm">Konfirmasi Tolak</button>
                    </form>
                </div>
            </div>
        </div>
        @else
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <span class="badge-{{ $pengajuan->status_color }} text-sm px-3 py-1">{{ $pengajuan->status_label }}</span>
                @if($pengajuan->processed_at)
                    <p class="text-sm text-mony-muted">Diproses {{ $pengajuan->processed_at->diffForHumans() }} oleh {{ $pengajuan->processor?->name }}</p>
                @endif
            </div>
            @if($pengajuan->reason_if_rejected)
                <p class="text-sm text-red-600 mt-2">Alasan: {{ $pengajuan->reason_if_rejected }}</p>
            @endif
            @if($pengajuan->transaksi)
                <div class="mt-3">
                    <a href="{{ route('admin.gadai.transaksi', $pengajuan->transaksi) }}" class="btn-primary btn-sm">
                        Lihat Transaksi {{ $pengajuan->transaksi->reference_number }}
                    </a>
                </div>
            @endif
        </div>
        @endif
    </div>

    {{-- Anggota Info --}}
    <div class="space-y-4">
        <div class="card p-5">
            <h3 class="section-title mb-3">Informasi Anggota</h3>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                    {{ strtoupper(substr($pengajuan->anggota?->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <p class="font-medium">{{ $pengajuan->anggota?->name }}</p>
                    <p class="text-xs text-mony-muted">{{ $pengajuan->anggota?->email }}</p>
                </div>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-mony-muted">NIK</span>
                    <span class="font-mono text-xs">{{ $pengajuan->anggota?->nik ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-mony-muted">HP</span>
                    <span>{{ $pengajuan->anggota?->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-mony-muted">Gadai Aktif</span>
                    <span>{{ $pengajuan->anggota?->transaksiGadai()->where('status','aktif')->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-mony-muted">Total Simpanan</span>
                    <span>Rp {{ number_format($pengajuan->anggota?->totalSimpanan() ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('admin.anggota.show', $pengajuan->anggota_id) }}" class="btn-outline btn-sm w-full">Lihat Profil Anggota</a>
            </div>
        </div>

        {{-- Jenis Barang Info --}}
        <div class="card p-5">
            <h3 class="section-title mb-3">Info Katalog Barang</h3>
            <div class="text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-mony-muted">Kategori</span>
                    <span>{{ $pengajuan->jenisBarang?->category }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-mony-muted">Max Pinjaman</span>
                    <span class="text-primary font-medium">{{ $pengajuan->jenisBarang?->max_loan_percentage }}%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-mony-muted">Max Pinjaman (Calc)</span>
                    <span class="font-medium">Rp {{ number_format($pengajuan->jenisBarang?->maxLoanAmount($pengajuan->estimated_value) ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
