@extends('layouts.anggota')
@php
$title = 'Detail Gadai';
@endphp
@section('content')
<div x-data="{
    showBayarModal: false,
    type: 'bunga',
    months: [],
    successOpen: false,
    successData: { title: '', subtitle: '', rows: [] },
    submitting: false,
    get monthlyInterest() { return {{ $transaksi->monthlyInterest() }}; },
    get totalAmount() {
        if (this.type === 'tebus') return {{ $transaksi->totalRedemption() }};
        return this.months.length * this.monthlyInterest;
    },
    submitPayment(event) {
        const form = event.target;
        const self = this;
        this.submitting = true;
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            self.submitting = false;
            if (data.success) {
                self.showBayarModal = false;
                self.successData = { title: data.title, subtitle: data.subtitle, rows: data.rows };
                self.successOpen = true;
                form.reset();
            } else {
                alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
            }
        })
        .catch(() => { self.submitting = false; alert('Gagal mengirim. Periksa koneksi kamu.'); });
    }
}">

<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('anggota.gadai.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <div>
        <h1 class="page-title">{{ $transaksi->reference_number }}</h1>
        <span class="badge-{{ $transaksi->status_color }}">{{ $transaksi->status_label }}</span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 max-w-4xl">
    <div class="space-y-4">
        {{-- Info --}}
        <div class="card p-5">
            <h3 class="section-title mb-4">Informasi Transaksi</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-mony-muted">Barang</span><span class="font-medium">{{ $transaksi->jenisBarang?->name }}</span></div>
                <div class="flex justify-between"><span class="text-mony-muted">Nilai Taksir</span><span class="font-medium">Rp {{ number_format($transaksi->appraisal_value, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-mony-muted">Pinjaman</span><span class="font-semibold text-primary">Rp {{ number_format($transaksi->loan_amount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-mony-muted">Bunga per Bulan</span><span>{{ $transaksi->interest_rate }}% = Rp {{ number_format($transaksi->monthlyInterest(), 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-mony-muted">Tanggal Gadai</span><span>{{ $transaksi->pawn_date->format('d M Y') }}</span></div>
                <div class="flex justify-between">
                    <span class="text-mony-muted {{ $transaksi->isOverdue() ? 'text-red-600' : '' }}">Jatuh Tempo</span>
                    <span class="{{ $transaksi->isOverdue() ? 'text-red-600 font-medium' : '' }}">
                        {{ $transaksi->due_date->format('d M Y') }}
                        @if($transaksi->isOverdue())
                            (Lewat {{ abs($transaksi->daysUntilDue()) }} hari!)
                        @else
                            ({{ $transaksi->daysUntilDue() }} hari lagi)
                        @endif
                    </span>
                </div>
                <div class="flex justify-between"><span class="text-mony-muted">Lokasi Penyimpanan</span><span>{{ $transaksi->warehouse_location ?? '-' }}</span></div>
                <div class="border-t pt-3 flex justify-between">
                    <span class="font-medium">Total Tebus Sekarang</span>
                    <span class="font-bold text-secondary">Rp {{ number_format($transaksi->totalRedemption(), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if($transaksi->status === 'aktif')
        <button type="button" @click="showBayarModal = true" class="btn-primary w-full text-center block">
            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Bayar Bunga / Tebus
        </button>
        @endif
    </div>

    {{-- Payment History --}}
    <div class="card p-5">
        <h3 class="section-title mb-4">Riwayat Pembayaran</h3>
        @if($transaksi->pembayaran->count())
            <div class="space-y-3">
                @foreach($transaksi->pembayaran->sortByDesc('submitted_at') as $p)
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex items-center justify-between mb-1">
                        <span class="badge-{{ $p->payment_type === 'tebus' ? 'info' : 'success' }} text-xs">{{ $p->payment_type_label }}</span>
                        <span class="badge-{{ $p->status_color }} text-xs">{{ ucfirst($p->status) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-mony-muted">{{ $p->submitted_at->format('d M Y') }}</span>
                        <span class="font-medium">Rp {{ number_format($p->amount, 0, ',', '.') }}</span>
                    </div>
                    @if($p->rejection_reason)
                        <p class="text-xs text-red-600 mt-1">Ditolak: {{ $p->rejection_reason }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6 text-mony-muted">
                <p class="text-sm">Belum ada riwayat pembayaran</p>
            </div>
        @endif
    </div>
</div>

@if($transaksi->status === 'aktif')
@php
    $paidMonthsList = $transaksi->pembayaran
        ->where('payment_type','bunga')->where('status','confirmed')
        ->flatMap(fn($p) => $p->paid_months ?? [])->unique()->values()->toArray();
    $endMonthM = $transaksi->due_date->copy()->startOfMonth();
@endphp

{{-- Popup: Bayar Bunga / Tebus --}}
<div x-show="showBayarModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @click.self="showBayarModal = false"
     @keydown.escape.window="if(!$store.lb?.show){ showBayarModal = false }"
     style="background:rgba(8,20,12,0.72);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);"
     x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="popup-sheet bg-white w-full shadow-2xl scrollbar-hide"
         style="max-width:560px;max-height:90vh;overflow-y:auto;border-radius:24px;"
         @click.stop
         x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
        {{-- Header --}}
        <div class="overflow-hidden rounded-t-3xl px-6 py-5" style="background:linear-gradient(135deg,var(--green) 0%,var(--green2) 100%);">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:rgba(255,255,255,0.15)">
                        <img src="{{ asset('images/logo-mony.png') }}" alt="MONY" class="w-7 h-7 object-contain">
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white leading-tight">Bayar Gadai</h2>
                        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6)">{{ $transaksi->reference_number }} — {{ $transaksi->jenisBarang?->name }}</p>
                    </div>
                </div>
                <button type="button" @click="showBayarModal = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full flex-shrink-0"
                        style="background:rgba(255,255,255,0.15)"
                        onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        {{-- Info table --}}
        <div class="mx-5 mt-5 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5">
            <div class="px-4 py-2.5" style="background:#f5faf3;border-bottom:1px solid #ddebd5">
                <p class="text-xs font-semibold text-mony-text">Ringkasan Gadai</p>
            </div>
            @foreach([
                ['Jenis Barang', $transaksi->jenisBarang?->name ?? '-'],
                ['Pinjaman',     'Rp ' . number_format($transaksi->loan_amount, 0, ',', '.')],
                ['Bunga/Bulan',  'Rp ' . number_format($transaksi->monthlyInterest(), 0, ',', '.')],
                ['Jatuh Tempo',  $transaksi->due_date->format('d M Y')],
                ['Total Tebus',  'Rp ' . number_format($transaksi->totalRedemption(), 0, ',', '.')],
            ] as $ri => [$rl, $rv])
            <div class="flex justify-between items-center px-4 py-2.5 text-xs {{ $ri < 4 ? 'border-b' : '' }}" style="{{ $ri < 4 ? 'border-color:#f0f7ee' : '' }}">
                <span class="text-mony-muted">{{ $rl }}</span>
                <span class="font-semibold text-mony-text">{{ $rv }}</span>
            </div>
            @endforeach
        </div>
        {{-- Form --}}
        <form method="POST" action="{{ route('anggota.gadai.bayar.store', $transaksi) }}" enctype="multipart/form-data"
              @submit.prevent="submitPayment($event)"
              class="px-5 pb-6 mt-5 space-y-5">
            @csrf
            <div>
                <label class="form-label">Tipe Pembayaran</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_type" value="bunga" x-model="type" class="hidden peer">
                        <div class="peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 border border-gray-200 rounded-xl p-3.5 text-center hover:border-primary transition-all">
                            <p class="font-semibold text-sm text-mony-text">Bayar Bunga</p>
                            <p class="text-xs text-mony-muted">Rp {{ number_format($transaksi->monthlyInterest(), 0, ',', '.') }}/bln</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_type" value="tebus" x-model="type" class="hidden peer">
                        <div class="peer-checked:ring-2 peer-checked:ring-secondary peer-checked:bg-secondary/5 border border-gray-200 rounded-xl p-3.5 text-center hover:border-secondary transition-all">
                            <p class="font-semibold text-sm text-mony-text">Tebus Barang</p>
                            <p class="text-xs text-mony-muted">Rp {{ number_format($transaksi->totalRedemption(), 0, ',', '.') }}</p>
                        </div>
                    </label>
                </div>
            </div>
            <div x-show="type === 'bunga'" class="space-y-2">
                <label class="form-label mb-0">Pilih Bulan yang Dibayar</label>
                <div class="grid grid-cols-3 gap-2">
                    @php $cursorM = $transaksi->pawn_date->copy()->addMonth()->startOfMonth(); @endphp
                    @while($cursorM->lte($endMonthM))
                        @php $monthKey = $cursorM->format('Y-m') @endphp
                        <label class="{{ in_array($monthKey, $paidMonthsList) ? '' : 'cursor-pointer' }}">
                            <input type="checkbox" name="paid_months[]" value="{{ $monthKey }}"
                                   x-model="months" {{ in_array($monthKey, $paidMonthsList) ? 'disabled' : '' }}
                                   class="hidden peer">
                            <div class="peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5
                                        {{ in_array($monthKey, $paidMonthsList) ? 'bg-green-50 border-green-200 opacity-60 cursor-not-allowed' : 'border-gray-200 hover:border-primary' }}
                                        border rounded-xl p-2.5 text-center transition-all">
                                <p class="text-xs font-semibold text-mony-text">{{ \Carbon\Carbon::createFromFormat('Y-m',$monthKey)->isoFormat('MMM YY') }}</p>
                                @if(in_array($monthKey, $paidMonthsList))
                                    <p class="text-[10px] text-green-600 font-semibold">✓ Lunas</p>
                                @else
                                    <p class="text-[10px] text-mony-muted">Rp {{ number_format($transaksi->monthlyInterest(),0,',','.') }}</p>
                                @endif
                            </div>
                        </label>
                        @php $cursorM->addMonth() @endphp
                    @endwhile
                </div>
            </div>
            <div>
                <label class="form-label">Jumlah Transfer (Rp)</label>
                <input type="number" name="amount" :value="totalAmount" class="form-input bg-gray-50" required readonly>
                <p class="form-hint">Otomatis dihitung berdasarkan pilihan</p>
            </div>
            <div class="p-4 rounded-2xl" style="background:#f5faf3;border:1px solid #ddebd5">
                <p class="text-xs font-semibold text-mony-text mb-2.5">Transfer ke Rekening Koperasi</p>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between"><span class="text-mony-muted">Bank</span><strong class="text-mony-text">{{ $info->bank_name }}</strong></div>
                    <div class="flex justify-between"><span class="text-mony-muted">No. Rekening</span><strong class="text-mony-text font-mono">{{ $info->bank_account_number }}</strong></div>
                    <div class="flex justify-between"><span class="text-mony-muted">Atas Nama</span><strong class="text-mony-text">{{ $info->bank_account_name }}</strong></div>
                </div>
            </div>
            <div>
                <label class="form-label">Bukti Transfer <span class="text-red-500">*</span></label>
                <input type="file" name="transfer_proof" accept="image/*,application/pdf" class="form-input" required>
                <p class="form-hint">JPG/PNG/PDF, maks. 2MB</p>
            </div>
            <button type="submit" class="btn-primary w-full py-3 text-sm" :disabled="submitting"
                    onclick="return confirmAction(event,'Kirim bukti pembayaran gadai ini?','Ya, Kirim')">
                <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <svg x-show="submitting" x-cloak class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v2m0 12v2m8-8h-2M6 12H4m13.66-5.66l-1.42 1.42M7.76 16.24l-1.42 1.42m12.02 0l-1.42-1.42M7.76 7.76L6.34 6.34"/></svg>
                <span x-text="submitting ? 'Mengirim...' : 'Kirim Bukti Pembayaran'"></span>
            </button>
        </form>
    </div>
</div>
@endif

@include('partials.payment-success-modal')

</div>{{-- end x-data wrapper --}}
@endsection
