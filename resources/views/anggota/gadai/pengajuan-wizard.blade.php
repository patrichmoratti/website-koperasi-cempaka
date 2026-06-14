@extends('layouts.anggota')
@php $title = 'Ajukan Gadai'; @endphp
@section('content')

<div class="mb-8">
    <span class="badge-primary text-xs mb-3 inline-block">Pengajuan Gadai</span>
    <h1 class="text-3xl font-bold text-mony-text tracking-tight leading-tight">Ajukan Gadai Baru</h1>
    <p class="text-sm text-mony-muted mt-2 leading-relaxed max-w-2xl">
        Pilih jenis barang, merk, isi detail kondisi, lalu upload foto &amp; dokumen pendukung. Pengajuan akan diproses dalam 1&times;24 jam kerja.
    </p>
</div>

<div x-data="{
    items: {{ \Illuminate\Support\Js::from($jenisItems) }},
    step: 1,
    jenisIndex: null,
    brandIndex: null,
    jenisId: '',
    jenisName: '',
    jenisUnit: '',
    brandName: '',
    brandValue: 0,
    quantity: 1,
    loanAmount: 0,
    photos: [],
    videoName: '',
    docFiles: [],

    showModal: false,
    refNumber: '',
    replaceIndex: -1,

    get jenis() { return this.jenisIndex !== null ? this.items[this.jenisIndex] : null; },
    get maxPct() { return this.jenis?.maxPct ?? 100; },
    get totalTaksiran() { return this.brandValue * this.quantity; },
    get maxPinjaman() { return Math.floor(this.totalTaksiran * this.maxPct / 100); },
    get minPinjaman() { return Math.floor(this.maxPinjaman * 90 / 100); },

    rupiah(n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); },

    pilihJenis(i) {
        this.jenisIndex = i; this.jenisId = this.items[i].id;
        this.jenisName = this.items[i].name; this.jenisUnit = this.items[i].unit ?? '';
        this.brandIndex = null; this.brandName = ''; this.brandValue = 0;
        this.quantity = 1; this.loanAmount = 0;
        this.step = 2;
    },
    pilihBrand(i) {
        this.brandIndex = i; this.brandName = this.jenis.brands[i].name;
        this.brandValue = this.jenis.brands[i].value;
        this.quantity = 1;
        this.loanAmount = this.minPinjaman;
        this.step = 3;
        this.$nextTick(() => {
            if (this.$refs.loanInput) this.$refs.loanInput.value = this.loanAmount.toLocaleString('id-ID');
        });
    },
    updateQuantity() {
        if (!this.quantity || this.quantity < 1) this.quantity = 1;
        const mn = this.minPinjaman, mx = this.maxPinjaman;
        if (this.loanAmount < mn) this.loanAmount = mn;
        if (this.loanAmount > mx) this.loanAmount = mx;
        this.$nextTick(() => {
            if (this.$refs.loanInput) this.$refs.loanInput.value = this.loanAmount > 0 ? this.loanAmount.toLocaleString('id-ID') : '';
        });
    },
    formatLoan(event) {
        const raw = event.target.value.replace(/\D/g, '');
        const num = raw ? parseInt(raw) : 0;
        this.loanAmount = Math.min(num, this.maxPinjaman);
        const fmt = this.loanAmount > 0 ? this.loanAmount.toLocaleString('id-ID') : '';
        event.target.value = fmt;
        event.target.setSelectionRange(fmt.length, fmt.length);
    },
    addPhotos(files) {
        Array.from(files).forEach(f => {
            if (f.type.startsWith('image/') && this.photos.length < 3) {
                this.photos.push({ file: f, url: URL.createObjectURL(f) });
            }
        });
        const dt = new DataTransfer();
        for (const p of this.photos) dt.items.add(p.file);
        this.$refs.photoInput.files = dt.files;
    },
    removePhoto(i) {
        URL.revokeObjectURL(this.photos[i].url);
        this.photos.splice(i, 1);
        const dt = new DataTransfer();
        for (const p of this.photos) dt.items.add(p.file);
        this.$refs.photoInput.files = dt.files;
    },
    addDocs(files) {
        Array.from(files).forEach(f => {
            if (this.docFiles.length < 2) {
                this.docFiles.push({ file: f, name: f.name });
            }
        });
        const dt = new DataTransfer();
        for (const d of this.docFiles) dt.items.add(d.file);
        this.$refs.docInput.files = dt.files;
    },
    removeDoc(i) {
        this.docFiles.splice(i, 1);
        const dt = new DataTransfer();
        for (const d of this.docFiles) dt.items.add(d.file);
        this.$refs.docInput.files = dt.files;
    },
    replacePhotoAt(i) {
        this.replaceIndex = i;
        this.$refs.photoReplaceInput.click();
    },
    replacePhoto(files) {
        if (this.replaceIndex < 0 || !files.length) { this.replaceIndex = -1; return; }
        const f = files[0];
        if (!f.type.startsWith('image/')) { this.replaceIndex = -1; return; }
        URL.revokeObjectURL(this.photos[this.replaceIndex].url);
        this.photos.splice(this.replaceIndex, 1, { file: f, url: URL.createObjectURL(f) });
        const dt = new DataTransfer();
        for (const p of this.photos) dt.items.add(p.file);
        this.$refs.photoInput.files = dt.files;
        this.replaceIndex = -1;
    },
    submitForm() {
        const self = this;
        fetch(this.$refs.mainForm.action, {
            method: 'POST',
            body: new FormData(this.$refs.mainForm),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) { self.showModal = true; self.refNumber = data.ref_number || ''; }
            else { alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.'); }
        })
        .catch(function() { alert('Gagal mengirim pengajuan. Periksa koneksi kamu.'); });
    },

}">

    {{-- Step Indicator --}}
    <div class="flex items-center mb-8">
        @foreach(['Jenis Barang', 'Pilih Merk', 'Detail', 'Upload', 'Review'] as $i => $label)
        <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 transition-all duration-300"
                     :style="step > {{ $i+1 }}
                         ? 'background-color: var(--green-light); color: var(--green)'
                         : (step === {{ $i+1 }}
                             ? 'background-color: white; color: var(--green)'
                             : 'background-color: rgba(255,255,255,0.12); color: rgba(255,255,255,0.4)')">
                    <template x-if="step > {{ $i+1 }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="step <= {{ $i+1 }}">
                        <span>{{ $i+1 }}</span>
                    </template>
                </div>
                <span class="text-sm font-medium hidden sm:block transition-colors"
                      :style="step === {{ $i+1 }} ? 'color: white' : (step > {{ $i+1 }} ? 'color: rgba(255,255,255,0.75)' : 'color: rgba(255,255,255,0.38)')">{{ $label }}</span>
            </div>
            @if(!$loop->last)
            <div class="flex-1 h-px mx-4 transition-all duration-500"
                 :style="step > {{ $i+1 }} ? 'background-color: rgba(255,255,255,0.35)' : 'background-color: rgba(255,255,255,0.15)'"></div>
            @endif
        </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('anggota.gadai.pengajuan.store') }}" enctype="multipart/form-data" x-ref="mainForm">
        @csrf
        <input type="hidden" name="jenis_barang_id" :value="jenisId">
        <input type="hidden" name="brand_name" :value="brandName">
        <input type="hidden" name="estimated_value" :value="brandValue * quantity">

        {{-- ── Step 1: Jenis Barang ── --}}
        <div x-show="step === 1"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="card p-6">
                <h2 class="text-lg font-bold text-mony-text mb-1">Pilih Jenis Barang</h2>
                <p class="text-sm text-mony-muted mb-5">Pilih kategori barang yang akan kamu gadaikan.</p>
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
                @error('jenis_barang_id') <p class="form-error mt-3">{{ $message }}</p> @enderror
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
                        <h2 class="text-lg font-bold text-mony-text">Pilih Merk / Jenis</h2>
                        <p class="text-sm text-mony-muted mt-1">
                            <span x-text="jenis?.name"></span> &mdash; nilai taksiran per <span x-text="jenis?.unit"></span>
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

                {{-- Persyaratan / dokumen yang harus disiapkan --}}
                <div class="mt-5 p-4 rounded-2xl" style="background: var(--green-light)" x-show="jenis && jenis.requirements && jenis.requirements.length" x-cloak>
                    <p class="text-xs font-semibold mb-2" style="color: var(--green2)">Persyaratan &amp; Dokumen yang Perlu Disiapkan</p>
                    <ul class="space-y-1.5">
                        <template x-for="(r, i) in (jenis ? jenis.requirements : [])" :key="i">
                            <li class="flex items-start gap-2 text-xs" style="color: var(--green2)">
                                <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="r"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ── Step 3: Detail & Pinjaman ── --}}
        <div x-show="step === 3" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="space-y-4">

            {{-- Ringkasan taksiran --}}
            <div class="card p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="section-title">Informasi Taksiran</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <p class="text-xs text-mony-muted" x-text="jenisName + ' · ' + brandName"></p>
                            <span class="text-gray-300">·</span>
                            <button type="button" @click="step = 2"
                                    class="text-xs text-primary hover:underline inline-flex items-center gap-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Ganti merk
                            </button>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="p-4 rounded-xl bg-mony-bg">
                        <p class="text-[11px] uppercase tracking-wide text-mony-muted font-semibold mb-1.5">Nilai Taksiran</p>
                        <p class="text-xl font-bold text-mony-text" x-text="rupiah(totalTaksiran)"></p>
                        <p class="text-[11px] text-mony-muted mt-1">
                            <span x-text="rupiah(brandValue)"></span> &times; <span x-text="quantity"></span> <span x-text="jenisUnit"></span>
                        </p>
                    </div>
                    <div class="p-4 rounded-xl" style="background-color: var(--green-light)">
                        <p class="text-[11px] uppercase tracking-wide font-semibold mb-1.5" style="color: var(--green2)">Rentang Pinjaman</p>
                        <p class="text-base font-bold leading-tight" style="color: var(--green)" x-text="rupiah(minPinjaman)"></p>
                        <p class="text-[10px] mt-0.5" style="color: var(--green2)" x-text="'s.d. ' + rupiah(maxPinjaman)"></p>
                        <p class="text-[10px] mt-0.5" style="color: var(--green2)" x-text="Math.round(maxPct * 0.9) + '%–' + maxPct + '% dari taksiran'"></p>
                    </div>
                    <div class="p-4 rounded-xl bg-amber-50">
                        <p class="text-[11px] uppercase tracking-wide text-amber-700 font-semibold mb-1.5">Bunga / Bulan</p>
                        <p class="text-xl font-bold text-amber-700">8%</p>
                    </div>
                </div>
            </div>

            {{-- Detail barang --}}
            <div class="card p-5">
                <h3 class="section-title mb-1">Detail Barang</h3>

                {{-- Peringatan kejujuran --}}
                <div class="flex items-start gap-2.5 p-3 mb-4 rounded-xl bg-amber-50 border border-amber-200">
                    <svg class="w-4 h-4 flex-shrink-0 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-xs text-amber-700">Pastikan isi detail barang sesuai keadaan karena dapat memengaruhi proses verifikasi, dan penilaian pengurus terhadap pinjaman yang dapat diberikan.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="form-label">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="3"
                                  class="form-input @error('description') border-red-400 @enderror"
                                  placeholder="Contoh: Laptop ASUS ROG G15, Core i7, RAM 16GB, SSD 512GB, tahun 2022, kondisi mulus" required>{{ old('description') }}</textarea>
                        @error('description') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">
                            Jumlah / Berat Barang
                            <span x-cloak x-show="jenisUnit" class="text-mony-muted font-normal">(<span x-text="jenisUnit"></span>)</span>
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="weight_or_quantity"
                               x-model.number="quantity"
                               @input="updateQuantity()" @change="updateQuantity()"
                               min="1" step="1" class="form-input"
                               placeholder="Masukkan jumlah atau berat" required>
                        <p class="text-[11px] text-mony-muted mt-1.5" x-show="totalTaksiran > 0" x-cloak>
                            Total taksiran: <strong class="text-mony-text" x-text="rupiah(totalTaksiran)"></strong>
                            &nbsp;<span class="text-gray-400">(<span x-text="rupiah(brandValue)"></span> &times; <span x-text="quantity"></span> <span x-text="jenisUnit"></span>)</span>
                        </p>
                        <p class="text-[11px] text-mony-muted mt-1.5" x-show="totalTaksiran > 0" x-cloak>
                            Maks. pinjaman diperbarui: <strong style="color: var(--green)" x-text="rupiah(maxPinjaman)"></strong>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Nominal Pinjaman --}}
            <div class="card p-5">
                <h3 class="section-title mb-1">Nominal Pinjaman yang Diajukan</h3>
                <p class="text-xs text-mony-muted mb-4">Ketik nominal pinjaman dalam rentang yang tersedia.</p>

                {{-- Min & Max cards --}}
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

                <label class="form-label">Nominal Pinjaman <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute top-1/2 -translate-y-1/2 text-sm font-semibold text-mony-muted pointer-events-none select-none" style="left: 1rem;">Rp</span>
                    <input type="text" x-ref="loanInput"
                           @input="formatLoan($event)"
                           placeholder="0"
                           style="padding-left: 3.25rem;"
                           class="form-input text-lg font-semibold {{ $errors->has('loan_request_amount') ? 'border-red-400' : '' }}">
                </div>
                <input type="hidden" name="loan_request_amount" :value="loanAmount">

                {{-- Validasi inline --}}
                <div class="mt-2 space-y-1">
                    <div x-show="loanAmount > 0 && loanAmount < minPinjaman" x-cloak
                         class="flex items-center gap-1.5 text-xs text-red-600">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Minimal <span x-text="rupiah(minPinjaman)" class="font-semibold mx-0.5"></span> <span x-text="'(' + Math.round(maxPct * 0.9) + '% dari taksiran)'"></span>.
                    </div>
                    <div x-show="loanAmount > maxPinjaman && maxPinjaman > 0" x-cloak
                         class="flex items-center gap-1.5 text-xs text-red-600">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Melebihi batas <span x-text="maxPct + '%'"></span> dari taksiran. Maksimal <span x-text="rupiah(maxPinjaman)" class="font-semibold ml-0.5"></span>.
                    </div>
                    <div x-show="loanAmount >= minPinjaman && loanAmount <= maxPinjaman && loanAmount > 0" x-cloak
                         class="flex items-center gap-1.5 text-xs" style="color: var(--green)">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Nominal valid.
                    </div>
                </div>

                @error('loan_request_amount') <p class="form-error mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 justify-between">
                <button type="button" @click="step = 2" class="btn-outline">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </button>
                <button type="button" @click="step = 4" class="btn-primary">
                    Lanjut — Upload
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        {{-- ── Step 4: Upload ── --}}
        <div x-show="step === 4" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="space-y-4">

            {{-- Foto Barang (wajib 3) --}}
            <div class="card p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="section-title">Foto Barang <span class="text-red-500 text-xs font-normal">*</span></h3>
                        <p class="text-xs text-mony-muted mt-0.5">Wajib upload 3 foto dari berbagai sudut. JPG/PNG, maks 2MB/foto.</p>
                    </div>
                    <span class="text-sm font-bold px-3 py-1 rounded-lg"
                          :class="photos.length >= 3 ? 'bg-primary/10 text-primary' : 'bg-mony-bg text-gray-400'"
                          x-text="photos.length + ' / 3'"></span>
                </div>

                <div x-show="photos.length > 0" class="grid grid-cols-3 gap-3 mb-3">
                    <template x-for="(p, i) in photos" :key="i">
                        <div class="relative group rounded-xl overflow-hidden bg-gray-100" style="aspect-ratio: 1;">
                            <img :src="p.url" class="w-full h-full object-cover">
                            {{-- Hover overlay with Ganti + Hapus --}}
                            <div class="absolute inset-0 flex flex-col justify-between p-1.5 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-xs text-white/70 font-medium" x-text="'Foto ' + (i + 1)"></span>
                                <div class="flex gap-1">
                                    <button type="button" @click.prevent="replacePhotoAt(i)"
                                            class="flex-1 flex items-center justify-center gap-0.5 py-1 rounded-lg bg-white/90 text-xs font-semibold text-gray-800">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Ganti
                                    </button>
                                    <button type="button" @click.prevent="removePhoto(i)"
                                            class="flex-1 flex items-center justify-center gap-0.5 py-1 rounded-lg bg-red-500 text-xs font-semibold text-white">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="photos.length < 3"
                     class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-primary transition-colors cursor-pointer"
                     @click="$refs.photoTrigger.click()"
                     @dragover.prevent="$el.classList.add('border-primary','bg-primary/5')"
                     @dragleave.prevent="$el.classList.remove('border-primary','bg-primary/5')"
                     @drop.prevent="$el.classList.remove('border-primary','bg-primary/5'); addPhotos($event.dataTransfer.files)">
                    <div class="w-12 h-12 rounded-2xl mx-auto mb-3 flex items-center justify-center" style="background: var(--green-light)">
                        <svg class="w-6 h-6" style="color: var(--green2)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-mony-text">Tambah Foto</p>
                    <p class="text-xs text-mony-muted mt-1" x-text="'Sisa ' + (3 - photos.length) + ' foto · klik atau drag & drop'"></p>
                </div>
                {{-- submission input: holds DataTransfer files, submitted with form --}}
                <input type="file" name="item_photos[]" multiple class="hidden" x-ref="photoInput">
                {{-- trigger input: cleared after selection so user can re-pick same file --}}
                <input type="file" accept="image/*" multiple class="hidden" x-ref="photoTrigger"
                       @change="addPhotos($event.target.files); $event.target.value = ''">
                {{-- replace input: for swapping a specific photo --}}
                <input type="file" accept="image/*" class="hidden" x-ref="photoReplaceInput"
                       @change="replacePhoto($event.target.files); $event.target.value = ''">
                <div x-show="photos.length > 0 && photos.length < 3" x-cloak
                     class="mt-2 flex items-center gap-1.5 text-xs text-amber-600">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Masih perlu <span x-text="3 - photos.length" class="font-bold mx-0.5"></span> foto lagi.
                </div>
                @error('item_photos') <p class="form-error mt-2">{{ $message }}</p> @enderror
                @error('item_photos.*') <p class="form-error mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Video Barang (wajib) --}}
            <div class="card p-5">
                <h3 class="section-title mb-1">Video Barang <span class="text-red-500 text-xs font-normal">*</span></h3>
                <p class="text-xs text-mony-muted mb-4">Wajib upload 1 video pendek kondisi barang. MP4/MOV, maks 20MB.</p>

                <div x-show="videoName" x-cloak class="flex items-center gap-3 mb-3 p-3 rounded-xl" style="background: var(--green-light)">
                    <svg class="w-5 h-5 flex-shrink-0" style="color: var(--green2)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm font-medium flex-1 truncate" style="color: var(--green)" x-text="videoName"></p>
                    <button type="button" @click.prevent="videoName = ''; $refs.videoInput.value = ''"
                            class="text-red-400 hover:text-red-600 flex-shrink-0 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div x-show="!videoName"
                     class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-primary transition-colors cursor-pointer"
                     @click="$refs.videoInput.click()">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm font-medium text-mony-text">Upload Video</p>
                    <p class="text-xs text-mony-muted mt-1">MP4, MOV — maks 20MB</p>
                </div>
                <input type="file" name="item_video" accept="video/mp4,video/quicktime,video/avi,video/webm" class="hidden" x-ref="videoInput"
                       @change="videoName = $event.target.files[0]?.name ?? ''">
            </div>

            {{-- Dokumen Pendukung (fix: DataTransfer accumulation) --}}
            <div class="card p-5">
                <div class="flex items-center justify-between mb-1">
                    <div>
                        <h3 class="section-title">Dokumen Pendukung</h3>
                        <p class="text-xs text-mony-muted mt-0.5">STNK, BPKB, nota pembelian, sertifikat, dll. JPG/PNG/PDF, maks 2MB/file.</p>
                    </div>
                    <span class="text-sm font-bold px-3 py-1 rounded-lg"
                          :class="docFiles.length > 0 ? 'bg-primary/10 text-primary' : 'bg-mony-bg text-gray-400'"
                          x-text="docFiles.length + ' / 2'"></span>
                </div>

                <div x-show="docFiles.length > 0" x-cloak class="space-y-2 mt-3 mb-3">
                    <template x-for="(d, i) in docFiles" :key="i">
                        <div class="flex items-center gap-3 p-3 bg-mony-bg rounded-xl">
                            <svg class="w-4 h-4 flex-shrink-0 text-mony-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm text-mony-text flex-1 truncate" x-text="d.name"></p>
                            <button type="button" @click.prevent="removeDoc(i)"
                                    class="text-red-400 hover:text-red-600 flex-shrink-0 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <div x-show="docFiles.length < 2" class="mt-3"
                     :class="docFiles.length === 0 ? '' : ''">
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-primary transition-colors cursor-pointer"
                         @click="$refs.docTrigger.click()">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-medium text-mony-text">Upload Dokumen</p>
                        <p class="text-xs text-mony-muted mt-1" x-text="'Sisa ' + (2 - docFiles.length) + ' slot'"></p>
                    </div>
                </div>
                {{-- submission input: holds DataTransfer files, submitted with form --}}
                <input type="file" name="supporting_docs[]" multiple class="hidden" x-ref="docInput">
                {{-- trigger input: cleared after selection so user can re-pick same file --}}
                <input type="file" accept="image/*,application/pdf" class="hidden" x-ref="docTrigger"
                       @change="addDocs($event.target.files); $event.target.value = ''">
            </div>

            <div class="flex gap-3 justify-between">
                <button type="button" @click="step = 3" class="btn-outline">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </button>
                <button type="button" @click="step = 5"
                        :disabled="photos.length < 3 || !videoName"
                        class="btn-primary disabled:opacity-40 disabled:cursor-not-allowed">
                    Review Pengajuan
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            {{-- Upload requirements reminder --}}
            <div class="flex items-start gap-2 text-xs text-mony-muted">
                <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Tombol "Review Pengajuan" aktif setelah 3 foto dan 1 video terupload.</span>
            </div>
        </div>

        {{-- ── Step 5: Review & Submit ── --}}
        <div x-show="step === 5" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="card p-5 mb-4">
                <h3 class="section-title mb-1">Review Pengajuan</h3>
                <p class="text-xs text-mony-muted mb-4">Periksa kembali sebelum mengirim. Pengajuan yang sudah disubmit tidak dapat diubah.</p>

                <div class="space-y-2.5 text-sm">
                    <div class="flex items-center justify-between py-2.5 border-b border-gray-100">
                        <span class="text-mony-muted flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Jenis Barang
                        </span>
                        <span class="font-semibold text-mony-text" x-text="jenisName"></span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 border-b border-gray-100">
                        <span class="text-mony-muted flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            Merk / Jenis
                        </span>
                        <span class="font-semibold text-mony-text" x-text="brandName"></span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 border-b border-gray-100">
                        <span class="text-mony-muted flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                            Jumlah / Berat
                        </span>
                        <span class="font-semibold text-mony-text"><span x-text="quantity"></span> <span x-text="jenisUnit"></span></span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 border-b border-gray-100">
                        <span class="text-mony-muted flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Nilai Taksiran
                        </span>
                        <span class="font-semibold text-mony-text" x-text="rupiah(totalTaksiran)"></span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 border-b border-gray-100">
                        <span class="text-mony-muted flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Pinjaman Diminta
                        </span>
                        <span class="font-semibold text-primary" x-text="rupiah(loanAmount)"></span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 border-b border-gray-100">
                        <span class="text-mony-muted flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Bunga &amp; Tenor
                        </span>
                        <span class="font-medium">
                            <span x-text="rupiah(Math.round(loanAmount * 8 / 100))"></span>
                            <span class="text-mony-muted font-normal">/ bulan &middot; bunga 8% &middot; jatuh tempo diperpanjang tiap bayar bunga</span>
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 border-b border-gray-100">
                        <span class="text-mony-muted flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Foto Barang
                        </span>
                        <span class="font-medium" x-text="photos.length + ' foto'"></span>
                    </div>
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-mony-muted flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Video &amp; Dokumen
                        </span>
                        <span class="font-medium" x-text="(videoName ? '1 video' : '-') + ' · ' + docFiles.length + ' dokumen'"></span>
                    </div>
                </div>

                <div class="mt-4 p-3 rounded-xl text-xs flex items-start gap-2.5 bg-amber-50 border border-amber-200">
                    <svg class="w-4 h-4 flex-shrink-0 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="text-amber-700">Nilai taksiran akhir ditentukan pengurus setelah barang diperiksa secara fisik. Jumlah pinjaman yang disetujui mungkin berbeda dari yang diminta.</span>
                </div>
            </div>

            <div class="flex gap-3 justify-between">
                <button type="button" @click="step = 4" class="btn-outline">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </button>
                <button type="button" @click="submitForm()" class="btn-primary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Ajukan Sekarang
                </button>
            </div>
        </div>

    </form>

    {{-- Success Modal (shown AFTER successful AJAX submission) --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(8,20,12,0.72); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">

        <div class="popup-sheet bg-white w-full scrollbar-hide shadow-2xl"
             style="max-width:440px; max-height:85vh; overflow-y:auto; border-radius:24px;"
             x-transition:enter="transition ease-out duration-350"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            {{-- Branded Header (same style as konfirmasi-modal) --}}
            <div class="relative overflow-hidden rounded-t-[28px] sm:rounded-t-3xl px-5 py-5"
                 style="background:linear-gradient(135deg, var(--green) 0%, var(--green2) 100%);">
                <div class="absolute inset-0 opacity-[0.05]"
                     style="background-image:radial-gradient(circle, #fff 1px, transparent 1px); background-size:14px 14px;"></div>
                <div class="relative flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0"
                             style="background:rgba(255,255,255,0.18);">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white leading-tight">Pengajuan Terkirim!</h2>
                            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6);">Diproses 1&times;24 jam kerja oleh tim koperasi</p>
                        </div>
                    </div>
                    <a href="{{ route('anggota.gadai.index') }}"
                       class="w-8 h-8 flex items-center justify-center rounded-full flex-shrink-0 transition-colors"
                       style="background:rgba(255,255,255,0.15);"
                       onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                       onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Nomor Referensi --}}
            <div class="mx-4 mt-4 py-2.5 rounded-xl text-center border-2 border-dashed" style="border-color:var(--green);">
                <p class="text-xs text-mony-muted mb-0.5">Nomor Referensi Pengajuan</p>
                <p class="font-bold text-sm tracking-wide" style="color:var(--green);" x-text="refNumber || '—'"></p>
            </div>

            <div class="px-4 pt-4 pb-6 space-y-4">

                {{-- Foto Barang --}}
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-0.5 h-3.5 rounded-full inline-block flex-shrink-0" style="background:var(--green);"></span>
                        <p class="text-xs font-semibold text-mony-text">Foto Barang</p>
                        <span class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-full"
                              style="background:var(--green-light); color:var(--green);" x-text="photos.length + ' foto'"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-1.5">
                        <template x-for="(p, i) in photos" :key="i">
                            <div class="rounded-xl overflow-hidden" style="aspect-ratio:1; border:1px solid #ddebd5;">
                                <img :src="p.url" class="w-full h-full object-cover">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Berkas Terupload --}}
                <div x-show="videoName || docFiles.length > 0">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-0.5 h-3.5 rounded-full inline-block flex-shrink-0" style="background:var(--green);"></span>
                        <p class="text-xs font-semibold text-mony-text">Berkas Terupload</p>
                    </div>
                    <div class="space-y-1.5">
                        <div x-show="videoName"
                             class="flex items-center gap-2.5 px-3 py-2 rounded-xl"
                             style="background:var(--green-light);">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0" style="background:var(--green);">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium flex-1 truncate" style="color:var(--green);" x-text="videoName"></span>
                            <span class="text-xs font-bold px-1.5 py-0.5 rounded-md bg-white/60" style="color:var(--green2);">Video</span>
                        </div>
                        <template x-for="(d, i) in docFiles" :key="i">
                            <div class="flex items-center gap-2.5 px-3 py-2 rounded-xl" style="background:var(--green-light);">
                                <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0" style="background:var(--green);">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-medium flex-1 truncate" style="color:var(--green);" x-text="d.name"></span>
                                <span class="text-xs font-bold px-1.5 py-0.5 rounded-md bg-white/60" style="color:var(--green2);">Dok</span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Ringkasan Pengajuan (same table style as konfirmasi-modal) --}}
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-0.5 h-3.5 rounded-full inline-block flex-shrink-0" style="background:var(--green);"></span>
                        <p class="text-xs font-semibold text-mony-text">Ringkasan Pengajuan</p>
                    </div>
                    <div class="rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                        <div class="px-4 py-2" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                            <p class="text-xs font-semibold text-mony-muted" x-text="jenisName + (brandName ? ' · ' + brandName : '')"></p>
                        </div>
                        <div class="flex justify-between items-center px-4 py-2.5 text-xs" style="border-bottom:1px solid #f0f7ee;">
                            <span class="text-mony-muted">Jumlah / Berat</span>
                            <span class="font-semibold text-mony-text"><span x-text="quantity"></span> <span x-text="jenisUnit"></span></span>
                        </div>
                        <div class="flex justify-between items-center px-4 py-2.5 text-xs" style="border-bottom:1px solid #f0f7ee;">
                            <span class="text-mony-muted">Nilai Taksiran</span>
                            <span class="font-semibold text-mony-text" x-text="rupiah(totalTaksiran)"></span>
                        </div>
                        <div class="flex justify-between items-center px-4 py-3 text-xs" style="background:var(--green-light); border-bottom:1px solid #ddebd5;">
                            <span class="font-semibold" style="color:var(--green);">Ajuan Pinjaman</span>
                            <span class="font-bold text-sm" style="color:var(--green);" x-text="rupiah(loanAmount)"></span>
                        </div>
                        <div class="flex justify-between items-center px-4 py-2.5 text-xs">
                            <span class="text-mony-muted">Estimasi Bunga / Bulan (8%)</span>
                            <span class="font-semibold text-amber-600" x-text="rupiah(Math.round(loanAmount * 8 / 100))"></span>
                        </div>
                    </div>
                </div>

                {{-- Langkah Selanjutnya --}}
                <div class="rounded-xl p-4 text-xs" style="background:rgba(234,243,222,0.7); border:1px solid #c6e4a8;">
                    <p class="font-semibold mb-2.5" style="color:var(--green);">Langkah selanjutnya:</p>
                    <div class="space-y-2" style="color:var(--green2);">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 text-[10px] font-bold text-white mt-0.5"
                                  style="background:var(--green);">1</span>
                            <span>Tunggu konfirmasi pengurus dalam 1&times;24 jam kerja</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 text-[10px] font-bold text-white mt-0.5"
                                  style="background:var(--green2);">2</span>
                            <span>Bawa barang ke kantor koperasi setelah pengajuan dikonfirmasi</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 text-[10px] font-bold text-white mt-0.5"
                                  style="background:var(--green2);">3</span>
                            <span>Pengurus menilai barang &amp; transaksi gadai aktif dibuat</span>
                        </div>
                    </div>
                </div>

                {{-- CTA --}}
                <a href="{{ route('anggota.gadai.index') }}"
                   class="btn-primary w-full flex items-center justify-center gap-2">
                    Lihat Status Pengajuan
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

            </div>
        </div>
    </div>

</div>
@endsection