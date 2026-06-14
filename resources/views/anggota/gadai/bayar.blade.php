@extends('layouts.anggota')
@php
$title = 'Bayar Gadai';
@endphp
@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('anggota.gadai.detail', $transaksi) }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">Bayar Gadai</h1>
</div>

<div class="max-w-lg"
     x-data="{
        type: 'bunga',
        months: [],
        get monthlyInterest() { return {{ $transaksi->monthlyInterest() }}; },
        get totalAmount() {
            if(this.type === 'tebus') return {{ $transaksi->totalRedemption() }};
            return this.months.length * this.monthlyInterest;
        }
     }">

    {{-- Gadai info --}}
    <div class="card p-5 mb-4">
        <div class="flex items-center justify-between mb-3">
            <p class="font-semibold">{{ $transaksi->reference_number }}</p>
            <span class="badge-success">Aktif</span>
        </div>
        <div class="grid grid-cols-2 gap-2 text-sm">
            <div><span class="text-mony-muted">Pinjaman</span><p class="font-semibold">Rp {{ number_format($transaksi->loan_amount, 0, ',', '.') }}</p></div>
            <div><span class="text-mony-muted">Bunga/Bulan</span><p class="font-semibold text-yellow-600">Rp {{ number_format($transaksi->monthlyInterest(), 0, ',', '.') }}</p></div>
            <div><span class="text-mony-muted {{ $transaksi->isOverdue() ? 'text-red-600' : '' }}">Jatuh Tempo</span>
                <p class="{{ $transaksi->isOverdue() ? 'text-red-600 font-semibold' : '' }}">{{ $transaksi->due_date->format('d M Y') }}</p></div>
            <div><span class="text-mony-muted">Total Tebus</span><p class="font-semibold text-secondary">Rp {{ number_format($transaksi->totalRedemption(), 0, ',', '.') }}</p></div>
        </div>
    </div>

    <form method="POST" action="{{ route('anggota.gadai.bayar.store', $transaksi) }}" enctype="multipart/form-data"
          class="card p-6 space-y-5">
        @csrf

        {{-- Payment type --}}
        <div>
            <label class="form-label">Tipe Pembayaran <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" name="payment_type" value="bunga" x-model="type" class="hidden peer">
                    <div class="peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 border border-gray-200 rounded-xl p-4 text-center hover:border-primary transition-all">
                        <p class="font-medium text-sm">Bayar Bunga</p>
                        <p class="text-xs text-mony-muted">Rp {{ number_format($transaksi->monthlyInterest(), 0, ',', '.') }}/bln</p>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="payment_type" value="tebus" x-model="type" class="hidden peer">
                    <div class="peer-checked:ring-2 peer-checked:ring-secondary peer-checked:bg-secondary/5 border border-gray-200 rounded-xl p-4 text-center hover:border-secondary transition-all">
                        <p class="font-medium text-sm">Tebus Barang</p>
                        <p class="text-xs text-mony-muted">Rp {{ number_format($transaksi->totalRedemption(), 0, ',', '.') }}</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Month selection (bunga only) --}}
        <div x-show="type === 'bunga'" class="space-y-3">
            <div class="flex items-center justify-between">
                <label class="form-label mb-0">Pilih Bulan yang Dibayar</label>
                <span class="text-xs text-mony-muted">JT saat ini: <strong>{{ $transaksi->due_date->format('d M Y') }}</strong></span>
            </div>
            <div class="p-3 rounded-xl text-xs flex items-start gap-2" style="background:#fff7ed; border:1px solid #fed7aa; color:#9a3412;">
                <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Setiap pembayaran bunga dikonfirmasi, jatuh tempo diperpanjang otomatis <strong>4 bulan ke depan</strong>. Jika tidak membayar bunga selama <strong>4 bulan berturut-turut</strong>, barang akan diproses untuk dilelang.</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                @php
                    $paidMonths = $transaksi->pembayaran()
                        ->where('payment_type','bunga')
                        ->where('status','confirmed')
                        ->get()
                        ->flatMap(fn($p) => $p->paid_months ?? [])
                        ->unique()->values()->toArray();
                    $cursor = $transaksi->pawn_date->copy()->addMonth()->startOfMonth();
                    $endMonth = $transaksi->due_date->copy()->startOfMonth();
                @endphp
                @while($cursor->lte($endMonth))
                    @php $month = $cursor->format('Y-m') @endphp
                    <label class="cursor-pointer">
                        <input type="checkbox" name="paid_months[]" value="{{ $month }}"
                               x-model="months"
                               {{ in_array($month, $paidMonths) ? 'disabled' : '' }}
                               class="hidden peer">
                        <div class="peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5
                                    {{ in_array($month, $paidMonths) ? 'bg-green-50 border-green-200 cursor-not-allowed' : 'border-gray-200 hover:border-primary' }}
                                    border rounded-xl p-3 text-center transition-all">
                            <p class="text-xs font-medium">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->isoFormat('MMM YY') }}
                            </p>
                            @if(in_array($month, $paidMonths))
                                <p class="text-xs text-green-600">✓ Lunas</p>
                            @else
                                <p class="text-xs text-mony-muted">Rp {{ number_format($transaksi->monthlyInterest(), 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </label>
                    @php $cursor->addMonth() @endphp
                @endwhile
            </div>
        </div>

        {{-- Amount --}}
        <div>
            <label class="form-label">Jumlah Transfer (Rp) <span class="text-red-500">*</span></label>
            <input type="number" name="amount"
                   :value="totalAmount"
                   class="form-input @error('amount') border-red-400 @enderror"
                   required readonly>
            <p class="form-hint">Jumlah otomatis dihitung berdasarkan pilihan</p>
            @error('amount') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        {{-- Bank info --}}
        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
            <p class="text-sm font-medium mb-2">Transfer ke Rekening Koperasi:</p>
            <div class="space-y-1 text-sm">
                <p><span class="text-mony-muted">Bank:</span> <strong>{{ $info->bank_name }}</strong></p>
                <p><span class="text-mony-muted">No. Rekening:</span> <strong>{{ $info->bank_account_number }}</strong></p>
                <p><span class="text-mony-muted">Atas Nama:</span> <strong>{{ $info->bank_account_name }}</strong></p>
            </div>
        </div>

        {{-- Upload proof --}}
        <div>
            <label class="form-label">Bukti Transfer <span class="text-red-500">*</span></label>
            <input type="file" name="transfer_proof" accept="image/*,application/pdf"
                   class="form-input @error('transfer_proof') border-red-400 @enderror" required>
            <p class="form-hint">JPG/PNG/PDF, maks. 2MB</p>
            @error('transfer_proof') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primary w-full py-3"
                onclick="return confirmAction(event, 'Kirim bukti pembayaran ini?', 'Ya, Kirim')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Kirim Bukti Pembayaran
        </button>
    </form>
</div>
@endsection
