@extends('layouts.anggota')
@php $title = 'Keuangan'; @endphp
@section('content')

<div x-data="{
    showSimpananModal: false,
    tab: 'semua',
    prosesCount: {{ $riwayatProses }},
    verifiedCount: {{ $riwayatVerified }},
    successOpen: false,
    successData: { title: '', subtitle: '', rows: [] },
    detailOpen: false,
    detailData: { title: '', subtitle: '', status: 'pending', statusLabel: '', rows: [], note: null },
    openDetail(data) {
        this.detailData = data;
        this.detailOpen = true;
    },
    submitting: false,
    submitPayment(event, onClose) {
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
                onClose();
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

{{-- Page header --}}
<div class="mb-5">
    <span class="badge-primary text-xs mb-1 inline-block">Keuangan</span>
    <h1 class="text-2xl font-bold text-mony-text tracking-tight">Keuangan Saya</h1>
</div>

{{-- Stats row --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
    {{-- Total Simpanan --}}
    <div class="rounded-2xl p-5 flex flex-col gap-3" style="background:linear-gradient(135deg,var(--green) 0%,var(--green2) 100%); min-height:130px;">
        <span class="text-xs font-bold uppercase tracking-widest" style="color:rgba(255,255,255,0.8)">Total Simpanan</span>
        <p class="text-[1.65rem] font-bold text-white leading-none">Rp {{ number_format($summary['total'], 0, ',', '.') }}</p>
        <div class="grid grid-cols-2 gap-2 mt-auto">
            <div class="rounded-xl px-3 py-2" style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.12)">
                <p class="text-[10px] font-semibold uppercase tracking-wide mb-0.5" style="color:rgba(255,255,255,0.65)">Pokok</p>
                <p class="text-sm font-bold text-white">Rp {{ number_format($summary['pokok'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl px-3 py-2" style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.12)">
                <p class="text-[10px] font-semibold uppercase tracking-wide mb-0.5" style="color:rgba(255,255,255,0.65)">Wajib</p>
                <p class="text-sm font-bold text-white">Rp {{ number_format($summary['wajib'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Tagihan bunga --}}
    <div class="stat-card">
        <span class="stat-label">Tagihan Bunga / Bulan</span>
        <span class="stat-value text-xl {{ $tagihanBunga > 0 ? 'text-yellow-600' : '' }}">
            Rp {{ number_format($tagihanBunga, 0, ',', '.') }}
        </span>
        <span class="text-xs text-mony-muted mt-1">dari {{ $gadaiAktif->count() }} gadai aktif</span>
    </div>

    {{-- Gadai aktif --}}
    <div class="stat-card">
        <span class="stat-label">Gadai Aktif</span>
        <span class="stat-value text-3xl">{{ $gadaiAktif->count() }}</span>
        <span class="text-xs text-mony-muted mt-1">transaksi berjalan</span>
    </div>
</div>

{{-- Horizontal row: Tagihan Simpanan | Tagihan Bunga --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">

    {{-- Tagihan Simpanan --}}
    <div class="card p-5">
        <h3 class="section-title mb-1">Tagihan Simpanan</h3>
        <p class="text-xs text-mony-muted mb-4">Ketentuan simpanan pokok &amp; wajib anggota</p>

        <div class="space-y-3 mb-5">
            {{-- Simpanan Pokok info --}}
            <div class="rounded-2xl overflow-hidden border" style="border-color:rgba(37,99,235,0.15)">
                <div class="px-4 py-3 flex items-start justify-between gap-3" style="background:#eff6ff">
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-blue-100">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-mony-text">Simpanan Pokok</p>
                            <p class="text-xs text-mony-muted mt-0.5">Dibayar rutin <strong class="text-mony-text">setiap bulan</strong>, minimal <strong class="text-blue-600">Rp 50.000</strong></p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-2.5 flex items-center justify-between text-xs border-t" style="border-color:rgba(37,99,235,0.1)">
                    <span class="text-mony-muted">Sudah terkumpul</span>
                    <span class="font-bold text-mony-text">Rp {{ number_format($summary['pokok'], 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Simpanan Wajib info --}}
            <div class="rounded-2xl overflow-hidden border" style="border-color:rgba(26,61,46,0.12)">
                <div class="px-4 py-3 flex items-start justify-between gap-3" style="background:var(--green-light)">
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(26,61,46,0.12)">
                            <svg class="w-4 h-4" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-mony-text">Simpanan Wajib</p>
                            <p class="text-xs text-mony-muted mt-0.5">Dibayar <strong class="text-mony-text">sekali di awal</strong> sebesar <strong style="color:var(--green)">Rp 10.000</strong> untuk menjadi anggota resmi</p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-2.5 flex items-center justify-between text-xs border-t" style="border-color:rgba(26,61,46,0.1)">
                    <span class="text-mony-muted">Status</span>
                    @if($summary['wajib'] > 0)
                        <span class="badge-success text-xs">Sudah dibayar — Rp {{ number_format($summary['wajib'], 0, ',', '.') }}</span>
                    @else
                        <span class="badge-warning text-xs">Belum dibayar</span>
                    @endif
                </div>
            </div>
        </div>

        <button type="button" @click="showSimpananModal = true"
                class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-semibold text-white transition-all"
                style="background:var(--green)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Bayar Simpanan
        </button>
    </div>

    {{-- Tagihan Bunga Gadai --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="section-title">Tagihan Bunga Gadai</h3>
            @if($gadaiAktif->count())
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:#fff7ed;color:#c2410c;">
                {{ $gadaiAktif->count() }} aktif
            </span>
            @endif
        </div>

        @forelse($gadaiAktif as $t)
        @php
            $paidMonthsList = $t->pembayaran
                ->where('payment_type','bunga')->where('status','confirmed')
                ->flatMap(fn($p) => $p->paid_months ?? [])->unique()->values()->toArray();
            $endMonthM = $t->due_date->copy()->startOfMonth();
        @endphp
        <div x-data="{
            open: false,
            type: 'bunga',
            months: [],
            get monthlyInterest() { return {{ $t->monthlyInterest() }}; },
            get totalAmount() {
                if (this.type === 'tebus') return {{ $t->totalRedemption() }};
                return this.months.length * this.monthlyInterest;
            }
        }">
            {{-- Card item --}}
            <div class="rounded-2xl mb-3 last:mb-0 overflow-hidden border" style="border-color:rgba(26,61,46,0.1)">
                <div class="px-4 pt-4 pb-3" style="background:var(--cream)">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-mony-text leading-tight">{{ $t->jenisBarang?->name }}</p>
                            <p class="text-[11px] font-mono text-mony-muted mt-0.5">{{ $t->reference_number }}</p>
                        </div>
                        @if($t->isOverdue())
                        <span class="badge-danger text-xs flex-shrink-0">Lewat JT</span>
                        @else
                        <span class="text-xs font-semibold flex-shrink-0 px-2 py-0.5 rounded-full" style="background:var(--green-light);color:var(--green)">
                            {{ $t->daysUntilDue() }}h lagi
                        </span>
                        @endif
                    </div>
                </div>
                <div class="px-4 py-3 border-t" style="border-color:rgba(26,61,46,0.08);background:#fff">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-mony-muted">Bunga/bulan</span>
                        <span class="text-sm font-bold text-yellow-600">Rp {{ number_format($t->monthlyInterest(), 0, ',', '.') }}</span>
                    </div>
                    <span class="text-[11px] text-mony-muted">JT: {{ $t->due_date->format('d M Y') }}</span>
                </div>
                <button type="button" @click="open = true"
                        class="flex items-center justify-center gap-1.5 py-3 w-full text-sm font-semibold transition-all"
                        style="background:var(--green);color:white;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Bayar Bunga
                </button>
            </div>

            {{-- Popup Bayar Gadai --}}
            <div x-show="open" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4"
                 @click.self="open = false"
                 @keydown.escape.window="if(!$store.lb?.show){ open = false }"
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
                                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6)">{{ $t->reference_number }} — {{ $t->jenisBarang?->name }}</p>
                                </div>
                            </div>
                            <button type="button" @click="open = false"
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
                            ['Jenis Barang', $t->jenisBarang?->name ?? '-'],
                            ['Pinjaman',     'Rp ' . number_format($t->loan_amount, 0, ',', '.')],
                            ['Bunga/Bulan',  'Rp ' . number_format($t->monthlyInterest(), 0, ',', '.')],
                            ['Jatuh Tempo',  $t->due_date->format('d M Y')],
                            ['Total Tebus',  'Rp ' . number_format($t->totalRedemption(), 0, ',', '.')],
                        ] as $ri => [$rl, $rv])
                        <div class="flex justify-between items-center px-4 py-2.5 text-xs {{ $ri < 4 ? 'border-b' : '' }}" style="{{ $ri < 4 ? 'border-color:#f0f7ee' : '' }}">
                            <span class="text-mony-muted">{{ $rl }}</span>
                            <span class="font-semibold text-mony-text">{{ $rv }}</span>
                        </div>
                        @endforeach
                    </div>
                    {{-- Form --}}
                    <form method="POST" action="{{ route('anggota.gadai.bayar.store', $t) }}" enctype="multipart/form-data"
                          @submit.prevent="submitPayment($event, () => open = false)"
                          class="px-5 pb-6 mt-5 space-y-5">
                        @csrf
                        <div>
                            <label class="form-label">Tipe Pembayaran</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_type" value="bunga" x-model="type" class="hidden peer">
                                    <div class="peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 border border-gray-200 rounded-xl p-3.5 text-center hover:border-primary transition-all">
                                        <p class="font-semibold text-sm text-mony-text">Bayar Bunga</p>
                                        <p class="text-xs text-mony-muted">Rp {{ number_format($t->monthlyInterest(), 0, ',', '.') }}/bln</p>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_type" value="tebus" x-model="type" class="hidden peer">
                                    <div class="peer-checked:ring-2 peer-checked:ring-secondary peer-checked:bg-secondary/5 border border-gray-200 rounded-xl p-3.5 text-center hover:border-secondary transition-all">
                                        <p class="font-semibold text-sm text-mony-text">Tebus Barang</p>
                                        <p class="text-xs text-mony-muted">Rp {{ number_format($t->totalRedemption(), 0, ',', '.') }}</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div x-show="type === 'bunga'" class="space-y-2">
                            <label class="form-label mb-0">Pilih Bulan yang Dibayar</label>
                            <div class="grid grid-cols-3 gap-2">
                                @php $cursorM = $t->pawn_date->copy()->addMonth()->startOfMonth(); @endphp
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
                                                <p class="text-[10px] text-mony-muted">Rp {{ number_format($t->monthlyInterest(),0,',','.') }}</p>
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
        </div>
        @empty
        <div class="py-8 text-center">
            <svg class="w-8 h-8 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-xs text-mony-muted">Tidak ada tagihan bunga aktif</p>
        </div>
        @endforelse
    </div>

</div>{{-- end horizontal row --}}


{{-- Riwayat Pembayaran full-width --}}
<div class="card overflow-hidden">

    {{-- Tab header --}}
    <div class="px-5 pt-4 pb-0 border-b border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="section-title">Riwayat Pembayaran</h3>
                <p class="text-xs text-mony-muted mt-0.5">Simpanan & pembayaran gadai</p>
            </div>
        </div>
        <div class="flex gap-1">
            @php
                $tabs = [
                    'semua'       => ['label' => 'Semua',       'count' => count($riwayat)],
                    'proses'      => ['label' => 'Proses',       'count' => $riwayatProses],
                    'diverifikasi'=> ['label' => 'Diverifikasi', 'count' => $riwayatVerified],
                ];
            @endphp
            @foreach($tabs as $key => $t)
            <button type="button" @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}'
                        ? 'border-b-2 text-mony-text font-semibold'
                        : 'text-mony-muted hover:text-mony-text'"
                    class="flex items-center gap-1.5 px-3 pb-3 text-sm transition-colors"
                    :style="tab === '{{ $key }}' ? 'border-color:var(--green)' : ''">
                {{ $t['label'] }}
                @if($t['count'] > 0)
                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center"
                      :class="tab === '{{ $key }}' ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-mony-muted'"
                      style="{{ $key === 'proses' ? 'background:#fff7ed!important;color:#c2410c!important' : '' }}">
                    {{ $t['count'] }}
                </span>
                @endif
            </button>
            @endforeach
        </div>
    </div>

    {{-- Content --}}
    @if(count($riwayat) > 0)
    <div class="divide-y divide-gray-50">
        @foreach($riwayat as $item)
        <div x-show="
            tab === 'semua' ||
            (tab === 'proses' && '{{ $item['status'] }}' === 'pending') ||
            (tab === 'diverifikasi' && ('{{ $item['status'] }}' === 'confirmed' || '{{ $item['status'] }}' === 'rejected'))"
             @click="openDetail(@js($item['detail']))"
             class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50/50 transition-colors cursor-pointer">

            {{-- Icon --}}
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                {{ $item['type'] === 'simpanan' ? 'bg-blue-50' : 'bg-amber-50' }}">
                @if($item['type'] === 'simpanan')
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                @else
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif
            </div>

            {{-- Detail --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-mony-text">{{ $item['title'] }}</p>
                <p class="text-xs text-mony-muted truncate">{{ $item['sub'] }} · {{ $item['date_fmt'] }}</p>
            </div>

            {{-- Amount + status --}}
            <div class="text-right flex-shrink-0">
                <p class="text-sm font-bold text-mony-text">Rp {{ number_format($item['amount'], 0, ',', '.') }}</p>
                <span class="badge-{{ $item['color'] }} text-xs mt-0.5 inline-block">
                    {{ match($item['status']) { 'pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'rejected' => 'Ditolak', default => ucfirst($item['status']) } }}
                </span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Empty per tab --}}
    <div x-show="tab === 'proses' && prosesCount === 0" class="py-12 text-center">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm text-mony-muted">Tidak ada pembayaran dalam proses</p>
    </div>
    <div x-show="tab === 'diverifikasi' && verifiedCount === 0" class="py-12 text-center">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm text-mony-muted">Belum ada pembayaran yang dikonfirmasi atau ditolak</p>
    </div>

    @else
    <div class="py-14 text-center">
        <div class="w-12 h-12 rounded-2xl mx-auto mb-3 flex items-center justify-center" style="background:var(--green-light)">
            <svg class="w-6 h-6" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <p class="text-sm text-mony-muted mb-3">Belum ada riwayat pembayaran</p>
        <button type="button" @click="showSimpananModal = true" class="btn-primary btn-sm">Bayar Simpanan Sekarang</button>
    </div>
    @endif

</div>{{-- end riwayat card --}}


{{-- ═══════════════════════════════════════════════════════ --}}
{{-- POPUP: BAYAR SIMPANAN                                   --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div x-show="showSimpananModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @click.self="showSimpananModal = false"
     @keydown.escape.window="if(!$store.lb?.show){ showSimpananModal = false }"
     style="background:rgba(8,20,12,0.72);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);"
     x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    <div class="popup-sheet bg-white w-full shadow-2xl scrollbar-hide"
         style="max-width:540px;max-height:90vh;overflow-y:auto;border-radius:24px;"
         @click.stop
         x-data="{
             type: 'wajib',
             amount: 10000,
             fmt(n) { return n ? new Intl.NumberFormat('id-ID').format(n) : '0'; },
             updateAmount() { this.amount = this.type === 'pokok' ? 50000 : 10000; }
         }"
         x-init="$watch('type', () => updateAmount())"
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
                        <h2 class="text-base font-bold text-white leading-tight">Bayar Simpanan</h2>
                        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6)">KSP Cempaka — {{ auth()->user()->name }}</p>
                    </div>
                </div>
                <button type="button" @click="showSimpananModal = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full flex-shrink-0"
                        style="background:rgba(255,255,255,0.15)"
                        onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="rounded-xl px-3 py-2.5" style="background:rgba(255,255,255,0.1)">
                    <p class="text-[10px] font-semibold uppercase tracking-wide" style="color:rgba(255,255,255,0.6)">Total Simpanan Saat Ini</p>
                    <p class="text-sm font-bold text-white mt-0.5">Rp {{ number_format($summary['total'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl px-3 py-2.5" style="background:rgba(255,255,255,0.1)">
                    <p class="text-[10px] font-semibold uppercase tracking-wide" style="color:rgba(255,255,255,0.6)">Akan Dibayarkan</p>
                    <p class="text-sm font-bold text-white mt-0.5" x-text="'Rp ' + fmt(amount)"></p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('anggota.simpanan.bayar.store') }}" enctype="multipart/form-data"
              @submit.prevent="submitPayment($event, () => showSimpananModal = false)"
              class="px-6 pb-6 mt-5 space-y-5">
            @csrf

            {{-- Tipe --}}
            <div>
                <label class="form-label">Tipe Simpanan</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="wajib" x-model="type" class="hidden peer">
                        <div class="peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 border border-gray-200 rounded-2xl p-4 text-center hover:border-primary transition-all">
                            <div class="w-9 h-9 rounded-xl mx-auto mb-2 flex items-center justify-center" style="background:var(--green-light)">
                                <svg class="w-4 h-4" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="font-semibold text-sm text-mony-text">Simpanan Wajib</p>
                            <p class="text-xs text-mony-muted mt-0.5">Rp 10.000 (sekali, anggota resmi)</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="pokok" x-model="type" class="hidden peer">
                        <div class="peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 border border-gray-200 rounded-2xl p-4 text-center hover:border-primary transition-all">
                            <div class="w-9 h-9 rounded-xl mx-auto mb-2 flex items-center justify-center bg-blue-50">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <p class="font-semibold text-sm text-mony-text">Simpanan Pokok</p>
                            <p class="text-xs text-mony-muted mt-0.5">Min. Rp 50.000 / bulan</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Periode --}}
            <div x-show="type === 'pokok'" class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Bulan</label>
                    <select name="period_month" class="form-input">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" @selected($m === now()->month)>
                                {{ \Carbon\Carbon::create(null,$m)->isoFormat('MMMM') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Tahun</label>
                    <select name="period_year" class="form-input">
                        @foreach([now()->year, now()->year - 1] as $y)
                            <option value="{{ $y }}" @selected($y === now()->year)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Jumlah --}}
            <div>
                <label class="form-label">Jumlah Transfer (Rp)</label>
                <div class="flex items-center rounded-xl overflow-hidden border border-gray-200" :class="type === 'pokok' ? 'bg-white' : 'bg-gray-50'">
                    <span class="px-4 py-3 text-sm font-bold border-r border-gray-200" style="color:var(--green)">Rp</span>
                    <input type="number" name="amount" x-model.number="amount"
                           :min="type === 'pokok' ? 50000 : 10000"
                           :readonly="type === 'wajib'"
                           class="flex-1 px-4 py-3 text-sm font-bold outline-none"
                           :class="type === 'pokok' ? 'bg-white' : 'bg-gray-50'"
                           style="color:var(--green2)" required>
                </div>
                <p class="form-hint" x-show="type === 'wajib'">Nominal tetap — dibayar sekali di awal sebagai anggota resmi</p>
                <p class="form-hint" x-show="type === 'pokok'">Minimal Rp 50.000, boleh menabung lebih sesuai kemampuan</p>
            </div>

            {{-- Bank info --}}
            <div class="p-4 rounded-2xl" style="background:#f5faf3;border:1px solid #ddebd5">
                <p class="text-xs font-semibold text-mony-text mb-2.5">Transfer ke Rekening Koperasi</p>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between"><span class="text-mony-muted">Bank</span><strong class="text-mony-text">{{ $info->bank_name }}</strong></div>
                    <div class="flex justify-between"><span class="text-mony-muted">No. Rekening</span><strong class="text-mony-text font-mono">{{ $info->bank_account_number }}</strong></div>
                    <div class="flex justify-between"><span class="text-mony-muted">Atas Nama</span><strong class="text-mony-text">{{ $info->bank_account_name }}</strong></div>
                </div>
            </div>

            {{-- Upload --}}
            <div>
                <label class="form-label">Bukti Transfer <span class="text-red-500">*</span></label>
                <input type="file" name="transfer_proof" accept="image/*,application/pdf" class="form-input" required>
                <p class="form-hint">JPG/PNG/PDF, maks. 2MB</p>
            </div>

            <button type="submit" class="btn-primary w-full py-3 text-sm" :disabled="submitting"
                    onclick="return confirmAction(event,'Kirim bukti simpanan ini untuk dikonfirmasi?','Ya, Kirim')">
                <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <svg x-show="submitting" x-cloak class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v2m0 12v2m8-8h-2M6 12H4m13.66-5.66l-1.42 1.42M7.76 16.24l-1.42 1.42m12.02 0l-1.42-1.42M7.76 7.76L6.34 6.34"/></svg>
                <span x-text="submitting ? 'Mengirim...' : 'Kirim Bukti Simpanan'"></span>
            </button>
        </form>
    </div>
</div>{{-- end simpanan modal --}}

@include('partials.payment-success-modal')
@include('partials.payment-detail-modal')

</div>{{-- end top-level x-data --}}
@endsection