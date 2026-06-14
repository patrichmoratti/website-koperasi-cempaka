@extends('layouts.anggota')
@php $title = 'Simulasi Gadai'; @endphp
@section('content')

{{-- Header --}}
<div class="mb-8">
    <span class="badge-primary text-xs mb-3 inline-block">Simulasi &amp; Estimasi</span>
    <h1 class="text-3xl font-bold text-mony-text tracking-tight leading-tight">Simulasi Gadai</h1>
    <p class="text-sm text-mony-muted mt-2 leading-relaxed max-w-2xl">
        Rencanakan dulu, ajukan dengan tenang. Pilih jenis &amp; merk barang, lihat estimasi pinjaman,
        bunga, hingga total biaya tebus — sebelum kamu benar-benar mengajukan gadai.
    </p>
</div>

<div x-data="{
    items: {{ \Illuminate\Support\Js::from($simulasiItems) }},
    step: 1,
    jenisIndex: null,
    brandIndex: null,
    jumlah: 1,
    durasi: 4,
    bungaPct: 8,
    pulse: false,
    nominalPinjaman: 0,

    get jenis() { return this.jenisIndex !== null ? this.items[this.jenisIndex] : null; },
    get maxPct() { return this.jenis?.maxPct ?? 100; },
    get brand() { return (this.jenis && this.brandIndex !== null) ? this.jenis.brands[this.brandIndex] : null; },
    get nilaiSatuan() { return this.brand ? this.brand.value : 0; },
    get totalTaksiran() { return this.nilaiSatuan * this.jumlah; },
    get maxPinjaman() { return Math.floor(this.totalTaksiran * this.maxPct / 100); },
    get minPinjaman() { return Math.floor(this.maxPinjaman * 90 / 100); },
    get bungaBulanan() { return Math.round(this.nominalPinjaman * this.bungaPct / 100); },
    get totalBunga() { return this.bungaBulanan * this.durasi; },
    get totalTebus() { return this.nominalPinjaman + this.totalBunga; },
    get schedule() {
        let rows = [];
        for (let i = 1; i <= this.durasi; i++) {
            rows.push({ bulan: i, bunga: this.bungaBulanan, kumulatif: this.bungaBulanan * i });
        }
        return rows;
    },
    rupiah(n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); },
    bump() {
        const mn = this.minPinjaman, mx = this.maxPinjaman;
        if (this.nominalPinjaman < mn) this.nominalPinjaman = mn;
        if (this.nominalPinjaman > mx) this.nominalPinjaman = mx;
        this.pulse = true;
        this.$nextTick(() => {
            if (this.$refs.pinjamanInput) this.$refs.pinjamanInput.value = this.nominalPinjaman > 0 ? this.nominalPinjaman.toLocaleString('id-ID') : '';
            setTimeout(() => this.pulse = false, 300);
        });
    },
    formatPinjaman(event) {
        const raw = event.target.value.replace(/\D/g, '');
        const num = raw ? parseInt(raw) : 0;
        this.nominalPinjaman = Math.min(num, this.maxPinjaman);
        const fmt = this.nominalPinjaman > 0 ? this.nominalPinjaman.toLocaleString('id-ID') : '';
        event.target.value = fmt;
        event.target.setSelectionRange(fmt.length, fmt.length);
    },
    pilihJenis(i) { this.jenisIndex = i; this.brandIndex = null; this.jumlah = 1; this.nominalPinjaman = 0; this.step = 2; },
    pilihBrand(i) { this.brandIndex = i; this.step = 3; this.$nextTick(() => { this.nominalPinjaman = this.minPinjaman; this.bump(); }); },
    ulangi() { this.step = 1; this.jenisIndex = null; this.brandIndex = null; this.jumlah = 1; this.nominalPinjaman = 0; },
}">

    {{-- Step Progress --}}
    <div class="flex items-center mb-8">
        @foreach(['Jenis Barang', 'Merk &amp; Taksiran', 'Hasil Simulasi'] as $i => $label)
            <div class="flex items-center {{ $i < 2 ? 'flex-1' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 transition-all duration-300"
                         :class="step === {{ $i+1 }} ? 'ring-4' : ''"
                         :style="step > {{ $i+1 }}
                             ? 'background-color: var(--green-light); color: var(--green)'
                             : (step === {{ $i+1 }}
                                 ? 'background-color: white; color: var(--green); ring-color: rgba(255,255,255,0.25)'
                                 : 'background-color: rgba(255,255,255,0.12); color: rgba(255,255,255,0.4)')">
                        <template x-if="step > {{ $i+1 }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </template>
                        <template x-if="step <= {{ $i+1 }}">
                            <span>{{ $i+1 }}</span>
                        </template>
                    </div>
                    <span class="text-sm font-medium hidden sm:block transition-colors"
                          :style="step === {{ $i+1 }} ? 'color: white' : (step > {{ $i+1 }} ? 'color: rgba(255,255,255,0.75)' : 'color: rgba(255,255,255,0.38)')">{!! $label !!}</span>
                </div>
                @if($i < 2)
                    <div class="flex-1 h-px mx-4 transition-all duration-500"
                         :style="step > {{ $i+1 }} ? 'background-color: rgba(255,255,255,0.35)' : 'background-color: rgba(255,255,255,0.15)'"></div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ── Step 1: Pilih Jenis Barang ── --}}
    <div x-show="step === 1"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0">

        <div class="card p-6">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-mony-text">Pilih Jenis Barang</h2>
                <p class="text-sm text-mony-muted mt-1">Setiap kategori memiliki batas maksimal pinjaman yang berbeda. Pilih yang sesuai dengan barang yang akan kamu gadaikan.</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <template x-for="(item, i) in items" :key="item.id">
                    <button type="button" @click="pilihJenis(i)"
                            class="group relative rounded-xl overflow-hidden h-64 text-left hover:shadow-md transition-all duration-200">
                        <template x-if="item.image">
                            <img :src="item.image" :alt="item.name"
                                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        </template>
                        <template x-if="!item.image">
                            <div class="absolute inset-0 bg-mony-bg"></div>
                        </template>
                        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(15,35,25,0.88), rgba(15,35,25,0.18) 60%, rgba(15,35,25,0.04))"></div>
                        <div class="relative h-full flex flex-col justify-end p-3">
                            <p class="text-xs font-semibold text-white leading-tight" x-text="item.name"></p>
                            <p class="text-[11px] text-white/75 mt-0.5" x-text="'Maks. ' + (item.maxPct ?? 100) + '% dari taksiran'"></p>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- ── Step 2: Pilih Merk ── --}}
    <div x-show="step === 2" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0">

        <div class="card p-6">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h2 class="text-lg font-bold text-mony-text">Pilih Merk &amp; Kondisi</h2>
                    <p class="text-sm text-mony-muted mt-1">
                        Kategori: <strong class="text-mony-text" x-text="jenis?.name"></strong>
                        <span class="mx-1.5 text-gray-300">·</span>
                        nilai taksiran per <span x-text="jenis?.unit"></span> berdasarkan merk
                    </p>
                </div>
                <button type="button" @click="step = 1"
                        class="text-xs text-mony-muted hover:text-primary inline-flex items-center gap-1 flex-shrink-0 mt-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Ganti jenis
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <template x-for="(b, i) in (jenis ? jenis.brands : [])" :key="i">
                    <button type="button" @click="pilihBrand(i)"
                            class="group flex items-center justify-between gap-5 border border-gray-200 rounded-2xl px-6 py-5 text-left hover:border-primary hover:bg-primary/5 hover:shadow-sm transition-all duration-150">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0 transition-colors group-hover:bg-primary group-hover:text-white"
                                 style="background-color: var(--green-light); color: var(--green2)">
                                <span x-text="b.name.charAt(0).toUpperCase()"></span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-mony-text truncate" x-text="b.name"></p>
                                <p class="text-xs text-mony-muted mt-1">Taksiran <span x-text="rupiah(b.value)"></span> / <span x-text="jenis?.unit"></span></p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-[11px] text-mony-muted mb-1">Estimasi pinjaman</p>
                            <p class="text-base font-bold text-primary" x-text="rupiah(b.value * maxPct / 100 * 90 / 100) + ' – ' + rupiah(b.value * maxPct / 100)"></p>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- ── Step 3: Hasil Simulasi ── --}}
    <div x-show="step === 3" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5">

        {{-- Atur Simulasi — full width --}}
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                <div class="flex-1">
                    <div class="mb-4">
                        <h3 class="section-title">Atur Simulasi</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <p class="text-xs text-mony-muted" x-text="(jenis?.name ?? '') + ' · ' + (brand?.name ?? '')"></p>
                            <span class="text-gray-300">·</span>
                            <button type="button" @click="step = 2"
                                    class="text-xs text-primary hover:underline inline-flex items-center gap-0.5 flex-shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Ganti merk
                            </button>
                        </div>
                    </div>
                    <label class="text-xs font-semibold text-mony-text uppercase tracking-wide">
                        Jumlah Barang (<span x-text="jenis?.unit"></span>)
                    </label>
                    <div class="flex items-center gap-4 mt-3">
                        <button type="button" @click="jumlah = Math.max(1, jumlah - 1); bump()"
                                class="w-10 h-10 rounded-xl border-2 border-gray-200 flex items-center justify-center hover:border-primary hover:text-primary transition-all flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                        </button>
                        <div class="flex-1 text-center py-2.5 rounded-xl bg-mony-bg">
                            <span class="text-2xl font-bold text-mony-text" x-text="jumlah"></span>
                            <span class="text-sm text-mony-muted ml-1" x-text="jenis?.unit"></span>
                        </div>
                        <button type="button" @click="jumlah = jumlah + 1; bump()"
                                class="w-10 h-10 rounded-xl border-2 border-gray-200 flex items-center justify-center hover:border-primary hover:text-primary transition-all flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                </div>
                <div class="hidden sm:block w-px self-stretch bg-gray-100"></div>
                <div class="flex sm:flex-col gap-6 sm:gap-3 sm:w-32 sm:flex-shrink-0">
                    <div>
                        <p class="text-xs text-mony-muted">Bunga per bulan</p>
                        <p class="font-bold text-amber-700 mt-0.5" x-text="bungaPct + '%'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-mony-muted">Jatuh tempo awal</p>
                        <p class="font-bold text-mony-text mt-0.5" x-text="durasi + ' bulan'"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3 kartu hasil — horizontal --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="card p-5 transition-transform duration-300" :class="pulse ? 'scale-[1.02]' : ''">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gray-100 flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <p class="text-[11px] uppercase tracking-wide text-mony-muted font-semibold">Nilai Taksiran</p>
                </div>
                <p class="text-2xl font-bold text-mony-text" x-text="rupiah(totalTaksiran)"></p>
                <p class="text-[11px] text-mony-muted mt-1.5"><span x-text="rupiah(nilaiSatuan)"></span> &times; <span x-text="jumlah"></span> <span x-text="jenis?.unit"></span></p>
            </div>

            <div class="card p-5 transition-transform duration-300" :class="pulse ? 'scale-[1.02]' : ''" style="background-color: var(--green-light)">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: rgba(26,61,46,0.12)">
                        <svg class="w-5 h-5" style="color: var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-[11px] uppercase tracking-wide font-semibold" style="color: var(--green2)">Min. Estimasi Pinjaman</p>
                </div>
                <p class="text-2xl font-bold" style="color: var(--green)" x-text="rupiah(minPinjaman)"></p>
                <p class="text-[11px] mt-1.5" style="color: var(--green2)" x-text="'Min. ' + Math.round(maxPct * 0.9) + '% dari nilai taksiran'"></p>
            </div>

            <div class="card p-5 transition-transform duration-300" :class="pulse ? 'scale-[1.02]' : ''">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-amber-50 flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-[11px] uppercase tracking-wide text-mony-muted font-semibold">Bunga / Bulan</p>
                </div>
                <p class="text-2xl font-bold text-amber-700" x-text="rupiah(bungaBulanan)"></p>
                <p class="text-[11px] text-mony-muted mt-1.5"><span x-text="bungaPct"></span>% dari jumlah pinjaman</p>
            </div>
        </div>

        {{-- Input Nominal Pinjaman --}}
        <div class="card p-5">
            <h3 class="section-title mb-1">Berapa yang Ingin Kamu Pinjam?</h3>

            {{-- Min & Max --}}
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="flex items-center gap-2.5 p-3 rounded-xl" style="background-color: var(--green-light)">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: rgba(26,61,46,0.15)">
                        <svg class="w-3.5 h-3.5" style="color: var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--green2)" x-text="'Minimal (' + Math.round(maxPct * 0.9) + '%)'"></p>
                        <p class="text-sm font-bold leading-tight" style="color: var(--green)" x-text="rupiah(minPinjaman)"></p>
                    </div>
                </div>
                <div class="flex items-center gap-2.5 p-3 rounded-xl bg-mony-bg">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-white">
                        <svg class="w-3.5 h-3.5 text-mony-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-mony-muted" x-text="'Maksimal (' + maxPct + '%)'"></p>
                        <p class="text-sm font-bold leading-tight text-mony-text" x-text="rupiah(maxPinjaman)"></p>
                    </div>
                </div>
            </div>

            <div class="relative mb-3">
                <span class="absolute top-1/2 -translate-y-1/2 text-sm font-semibold text-mony-muted pointer-events-none select-none" style="left: 1rem;">Rp</span>
                <input type="text" x-ref="pinjamanInput"
                       @input="formatPinjaman($event)"
                       placeholder="0"
                       style="padding-left: 3.25rem;"
                       class="form-input text-xl font-bold">
            </div>

            {{-- Validasi inline --}}
            <div x-show="nominalPinjaman > 0 && nominalPinjaman < minPinjaman" x-cloak
                 class="flex items-center gap-1.5 text-xs text-red-600 mb-3">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Minimal <span x-text="rupiah(minPinjaman)" class="font-semibold mx-0.5"></span> <span x-text="'(' + Math.round(maxPct * 0.9) + '% dari taksiran)'"></span>.
            </div>
            <div x-show="nominalPinjaman > maxPinjaman && maxPinjaman > 0" x-cloak
                 class="flex items-center gap-1.5 text-xs text-red-600 mb-3">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Melebihi batas <span x-text="maxPct + '%'"></span> dari taksiran. Maksimal <span x-text="rupiah(maxPinjaman)" class="font-semibold ml-0.5"></span>.
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-xl bg-mony-bg flex flex-col gap-1">
                    <p class="text-[11px] text-mony-muted uppercase tracking-wide font-semibold">Bunga / Bulan (8%)</p>
                    <p class="text-base font-bold text-amber-700" x-text="rupiah(bungaBulanan)"></p>
                </div>
                <div class="p-3 rounded-xl flex flex-col gap-1" style="background: var(--green-light)">
                    <p class="text-[11px] uppercase tracking-wide font-semibold" style="color: var(--green2)">Total Tebus (4 bln)</p>
                    <p class="text-base font-bold" style="color: var(--green)" x-text="rupiah(totalTebus)"></p>
                </div>
            </div>
        </div>

        {{-- Jadwal Bunga & Total Tebus --}}
        <div class="card p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-mony-text">Jadwal Bunga &amp; Biaya Tebus</h2>
                    <p class="text-sm text-mony-muted mt-0.5">Estimasi cicilan bunga dan biaya tebus per bulan selama tenor <span x-text="durasi"></span> bulan.</p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-mony-bg">
                            <th class="py-3 px-4 text-left text-xs text-mony-muted font-semibold uppercase tracking-wide">Bulan</th>
                            <th class="py-3 px-4 text-right text-xs text-mony-muted font-semibold uppercase tracking-wide">Bunga Bulan Ini</th>
                            <th class="py-3 px-4 text-right text-xs text-mony-muted font-semibold uppercase tracking-wide">Total Bunga Berjalan</th>
                            <th class="py-3 px-4 text-right text-xs text-mony-muted font-semibold uppercase tracking-wide">Estimasi Biaya Tebus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in schedule" :key="row.bulan">
                            <tr class="border-t border-gray-100 hover:bg-mony-bg/50 transition-colors">
                                <td class="py-3.5 px-4 font-semibold text-mony-text" x-text="'Bulan ke-' + row.bulan"></td>
                                <td class="py-3.5 px-4 text-right text-amber-700 font-medium" x-text="rupiah(row.bunga)"></td>
                                <td class="py-3.5 px-4 text-right text-mony-muted" x-text="rupiah(row.kumulatif)"></td>
                                <td class="py-3.5 px-4 text-right font-bold text-mony-text" x-text="rupiah(nominalPinjaman + row.kumulatif)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Total Tebus highlight --}}
            <div class="mt-4 flex items-center justify-between py-4 px-5 rounded-2xl transition-transform duration-300" :class="pulse ? 'scale-[1.01]' : ''" style="background-color: var(--green-light)">
                <div>
                    <p class="text-sm font-bold" style="color: var(--green2)">Total Biaya Tebus (akhir bulan ke-<span x-text="durasi"></span>)</p>
                    <p class="text-xs mt-0.5" style="color: var(--green2)">Pokok pinjaman + total bunga selama tenor</p>
                </div>
                <p class="text-2xl font-bold ml-6 flex-shrink-0" style="color: var(--green)" x-text="rupiah(totalTebus)"></p>
            </div>

            {{-- Tips --}}
            <div class="mt-4 flex items-start gap-3 p-4 rounded-2xl bg-mony-bg text-sm text-mony-muted">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--green2)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p><strong class="text-mony-text">Tips:</strong> Ajukan pinjaman sesuai kebutuhan dan tebus lebih awal bila memungkinkan agar total bunga lebih ringan. Angka di atas adalah estimasi — nilai taksiran final ditentukan pengurus saat serah terima barang. Jatuh tempo otomatis diperpanjang 4 bulan setiap kali bunga dikonfirmasi, sehingga selama rutin membayar bunga barang tetap aman.</p>
            </div>
        </div>

        {{-- CTA --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('anggota.gadai.pengajuan.create') }}"
               class="btn-gold flex-1 justify-center text-sm py-3">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ajukan Gadai Sekarang
            </a>
            <button type="button" @click="ulangi()" class="btn-outline-cream flex-1 justify-center text-sm py-3">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Coba Simulasi Lain
            </button>
        </div>

    </div>{{-- end step 3 --}}
</div>{{-- end x-data --}}
@endsection