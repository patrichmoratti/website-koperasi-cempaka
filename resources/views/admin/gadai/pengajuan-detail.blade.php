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
                    <div @click="$store.lb = { show: true, src: '{{ asset('storage/' . $photo) }}', type: 'image' }"
                         class="rounded-lg overflow-hidden border border-gray-200 cursor-pointer group" style="aspect-ratio:1;">
                        <img src="{{ asset('storage/' . $photo) }}" alt="Foto barang"
                             class="w-full h-full object-cover group-hover:opacity-85 transition-opacity">
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Action --}}
        @if($pengajuan->status === 'proses')
        <div class="card p-5">
            <h3 class="section-title mb-3">Proses Pengajuan</h3>
            <p class="text-sm text-mony-muted mb-4">Terima pengajuan untuk memberi tahu anggota agar membawa barang ke koperasi. Penilaian dilakukan setelah barang hadir secara fisik.</p>
            <div class="flex gap-3">
                <form method="POST" action="{{ route('admin.gadai.pengajuan.approve', $pengajuan) }}">
                    @csrf
                    <button type="submit" class="btn-success"
                            onclick="return confirmAction(event, 'Terima pengajuan ini? Anggota akan dihubungi untuk membawa barang ke koperasi.', 'Ya, Terima')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Terima Pengajuan
                    </button>
                </form>
                <button type="button" class="btn-danger"
                        onclick="openRejectModal('Alasan penolakan pengajuan gadai {{ addslashes($pengajuan->jenisBarang?->name) }} dari {{ addslashes($pengajuan->anggota?->name) }}', '{{ route('admin.gadai.pengajuan.reject', $pengajuan) }}')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Tolak Pengajuan
                </button>
            </div>
        </div>

        @elseif($pengajuan->status === 'diterima' && !$pengajuan->transaksi)
        @php
            $maxLoanCalc = $pengajuan->jenisBarang?->maxLoanAmount($pengajuan->estimated_value) ?? $pengajuan->estimated_value;
            $maxPctCalc  = $pengajuan->jenisBarang?->max_loan_percentage ?? 100;
        @endphp
        <div class="card p-5" id="nilai">
            <h3 class="section-title mb-1">Penilaian Barang</h3>
            <p class="text-sm text-mony-muted mb-4">Anggota telah membawa barang ke koperasi. Konfirmasi pinjaman yang disetujui untuk membuat transaksi gadai aktif.</p>
            <form method="POST" action="{{ route('admin.gadai.pengajuan.nilai', $pengajuan) }}" class="space-y-4"
                  x-data="{
                    loanRaw: {{ $pengajuan->loan_request_amount }},
                    fmt(n) { return n ? new Intl.NumberFormat('id-ID').format(n) : ''; },
                    handleLoan(e) { let d=e.target.value.replace(/\D/g,''); this.loanRaw=d?parseInt(d):0; e.target.value=this.fmt(this.loanRaw); }
                  }">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nilai Taksiran</label>
                        <div class="flex items-center rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                            <span class="px-3 py-2.5 text-sm font-semibold border-r border-gray-200" style="color:var(--green);">Rp</span>
                            <span class="flex-1 px-3 py-2.5 text-sm font-semibold text-gray-500">
                                {{ number_format($pengajuan->estimated_value, 0, ',', '.') }}
                            </span>
                            <svg class="w-4 h-4 mr-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <p class="text-xs text-mony-muted mt-1">Otomatis dari nilai merk barang</p>
                    </div>
                    <div>
                        <label class="form-label">Pinjaman Disetujui (Rp) <span class="text-red-500">*</span></label>
                        <div class="flex items-center rounded-lg overflow-hidden border border-gray-300">
                            <span class="px-3 py-2.5 text-sm font-semibold border-r border-gray-300 bg-gray-50" style="color:var(--green);">Rp</span>
                            <input type="text" x-init="$el.value = fmt(loanRaw)" x-on:input="handleLoan($event)"
                                   class="flex-1 px-3 py-2.5 text-sm outline-none border-0 bg-white font-semibold"
                                   style="color:var(--green2);" required>
                        </div>
                        <input type="hidden" name="loan_amount" :value="loanRaw">
                        <p class="text-xs text-mony-muted mt-1">Maksimal: Rp {{ number_format($maxLoanCalc, 0, ',', '.') }} ({{ $maxPctCalc }}% dari taksiran)</p>
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Lokasi Penyimpanan</label>
                        <input type="text" name="warehouse_location" class="form-input" placeholder="Contoh: Rak A-01">
                    </div>
                </div>
                <div class="p-3 rounded-lg text-sm flex items-start gap-2" style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534;">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Transaksi gadai aktif akan dibuat. Bunga <strong>8%/bulan</strong>. Jatuh tempo awal <strong>4 bulan</strong> — otomatis diperpanjang 4 bulan setiap kali anggota membayar bunga. Barang dilelang jika tidak membayar bunga <strong>4 bulan berturut-turut</strong>.</span>
                </div>
                <button type="submit" class="btn-success" onclick="return confirmAction(event, 'Konfirmasi penilaian dan buat transaksi gadai?', 'Ya, Buat Transaksi')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Konfirmasi & Buat Transaksi
                </button>
            </form>
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
