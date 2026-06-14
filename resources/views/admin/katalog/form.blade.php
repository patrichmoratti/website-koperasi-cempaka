@extends('layouts.admin')
@php
$title = $item->id ? 'Edit Jenis Barang' : 'Tambah Jenis Barang';
$brandsRawDefault = $item->brands ? collect($item->brands)->map(fn($b) => ($b['name'] ?? '') . ' | ' . ($b['value'] ?? 0))->implode("\n") : '';

$categoryOptions = ['Elektronik', 'Peralatan Rumah Tangga', 'Perhiasan & Logam Mulia', 'Kendaraan & Transportasi'];
$currentCategory = old('category', $item->category);
if ($currentCategory && !in_array($currentCategory, $categoryOptions)) {
    $categoryOptions[] = $currentCategory;
}

$unitOptions = ['unit', 'gram', 'kg', 'pcs'];
$currentUnit = old('unit', $item->unit ?? 'unit');
if ($currentUnit && !in_array($currentUnit, $unitOptions)) {
    $unitOptions[] = $currentUnit;
}
@endphp
@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('admin.katalog.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <div>
        <h1 class="page-title">{{ $item->id ? 'Edit' : 'Tambah' }} Jenis Barang Gadai</h1>
        @if($item->id)
            <p class="text-xs text-mony-muted mt-0.5">{{ $item->name }} &middot; {{ $item->category }}</p>
        @endif
    </div>
    @if($item->id)
        <span class="badge-{{ $item->is_active ? 'success' : 'gray' }} ml-auto">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
    @endif
</div>

<form method="POST"
      action="{{ $item->id ? route('admin.katalog.update', $item) : route('admin.katalog.store') }}"
      enctype="multipart/form-data"
      x-data="{
        unit: {{ \Illuminate\Support\Js::from(old('unit', $item->unit ?? 'unit')) }},
        maxPct: {{ (float) old('max_loan_percentage', $item->max_loan_percentage ?? 80) }},
        baseValue: {{ (int) old('base_value_per_unit', $item->base_value_per_unit ?? 0) }},
        brandsRaw: {{ \Illuminate\Support\Js::from(old('brands_raw', $brandsRawDefault)) }},
        conditionsRaw: {{ \Illuminate\Support\Js::from(old('conditions_raw', $item->conditions ? implode("\n", $item->conditions) : '')) }},
        requirementsRaw: {{ \Illuminate\Support\Js::from(old('requirements_raw', $item->requirements ? implode("\n", $item->requirements) : '')) }},
        rupiah(n) { return 'Rp ' + Math.round(n || 0).toLocaleString('id-ID'); },
        get minPct() { return Math.round(this.maxPct * 0.9); },
        get exampleMin() { return Math.floor(this.baseValue * this.maxPct / 100 * 0.9); },
        get exampleMax() { return Math.floor(this.baseValue * this.maxPct / 100); },
        get brandList() {
            return this.brandsRaw.split('\n')
                .map(line => line.split('|'))
                .filter(p => p[0] && p[0].trim())
                .map(p => ({ name: p[0].trim(), value: p[1] ? (parseInt(p[1].trim()) || 0) : 0 }));
        },
        get conditionList() {
            return this.conditionsRaw.split('\n').map(s => s.trim()).filter(s => s);
        },
        get requirementList() {
            return this.requirementsRaw.split('\n').map(s => s.trim()).filter(s => s);
        },
      }">
    @csrf
    @if($item->id) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">

            {{-- Informasi Dasar --}}
            <div class="card p-6 space-y-4">
                <h3 class="section-title">Informasi Dasar</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="form-label">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $item->name) }}"
                               class="form-input @error('name') border-red-400 @enderror" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Kategori <span class="text-red-500">*</span></label>
                        <select name="category" class="form-input @error('category') border-red-400 @enderror" required>
                            @foreach($categoryOptions as $cat)
                                <option value="{{ $cat }}" {{ $currentCategory === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('category') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Satuan</label>
                        <select name="unit" x-model="unit" class="form-input @error('unit') border-red-400 @enderror" required>
                            @foreach($unitOptions as $u)
                                <option value="{{ $u }}">{{ $u }}</option>
                            @endforeach
                        </select>
                        <p class="form-hint">Dipakai pada label taksiran &amp; jumlah barang.</p>
                        @error('unit') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Nilai Dasar per Satuan (Rp)</label>
                        <input type="number" name="base_value_per_unit" x-model.number="baseValue"
                               class="form-input @error('base_value_per_unit') border-red-400 @enderror" min="0">
                        <p class="form-hint">Referensi nilai bila merek belum diisi.</p>
                        @error('base_value_per_unit') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-input" rows="2">{{ old('description', $item->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Merek & Estimasi Nilai --}}
            <div class="card p-6 space-y-4">
                <div>
                    <h3 class="section-title">Merek &amp; Estimasi Nilai</h3>
                    <p class="text-xs text-mony-muted mt-1">Setiap merek akan muncul sebagai pilihan untuk anggota saat mengajukan gadai, lengkap dengan estimasi pinjamannya.</p>
                </div>
                <div>
                    <label class="form-label">Daftar Merek (format: Nama Merek | Nilai Taksiran, satu per baris)</label>
                    <textarea name="brands_raw" x-model="brandsRaw" class="form-input font-mono text-xs" rows="5"
                              placeholder="iPhone 14 Pro | 14000000&#10;Samsung Galaxy S23 | 9000000"></textarea>
                </div>

                <div x-show="brandList.length" x-cloak>
                    <p class="text-xs font-semibold uppercase tracking-wide text-mony-muted mb-2">Pratinjau di Sisi Anggota</p>
                    <div class="space-y-2">
                        <template x-for="b in brandList" :key="b.name">
                            <div class="flex items-center justify-between gap-4 p-3 rounded-xl bg-mony-bg">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-mony-text truncate" x-text="b.name"></p>
                                    <p class="text-xs text-mony-muted mt-0.5">Taksiran <span x-text="rupiah(b.value)"></span> / <span x-text="unit"></span></p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-[11px] text-mony-muted">Estimasi pinjaman</p>
                                    <p class="text-sm font-bold text-primary" x-text="rupiah(b.value * maxPct / 100 * 0.9) + ' – ' + rupiah(b.value * maxPct / 100)"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Kondisi & Persyaratan --}}
            <div class="card p-6 space-y-4">
                <h3 class="section-title">Kondisi &amp; Persyaratan</h3>

                <div>
                    <label class="form-label">Kondisi Barang (satu per baris)</label>
                    <textarea name="conditions_raw" x-model="conditionsRaw" class="form-input font-mono text-xs" rows="4"
                              placeholder="Baru&#10;Sangat Baik&#10;Baik&#10;Cukup"></textarea>
                    <div class="flex flex-wrap gap-1.5 mt-2" x-show="conditionList.length" x-cloak>
                        <template x-for="c in conditionList" :key="c">
                            <span class="badge-gray" x-text="c"></span>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="form-label">Persyaratan &amp; Dokumen (satu per baris)</label>
                    <textarea name="requirements_raw" x-model="requirementsRaw" class="form-input font-mono text-xs" rows="3"
                              placeholder="Charger asli&#10;Box jika ada"></textarea>
                    <p class="text-xs text-mony-muted mt-1">Ditampilkan sebagai checklist kepada anggota saat memilih merek di form pengajuan gadai.</p>
                    <div class="space-y-1.5 mt-2 p-3 rounded-xl" style="background: var(--green-light)" x-show="requirementList.length" x-cloak>
                        <template x-for="r in requirementList" :key="r">
                            <div class="flex items-center gap-2 text-xs" style="color: var(--green2)">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="r"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">

            {{-- Gambar --}}
            <div class="card p-6 space-y-3">
                <h3 class="section-title">Gambar Barang</h3>
                @if($item->image_path)
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="Gambar" class="w-full h-32 object-cover rounded-xl">
                @endif
                <input type="file" name="image" accept="image/*" class="form-input">
                <p class="form-hint">JPG/PNG, maks. 2MB. Ditampilkan di katalog &amp; pemilihan jenis barang.</p>
                @error('image') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Pengaturan Pinjaman --}}
            <div class="card p-6 space-y-3">
                <div>
                    <h3 class="section-title">Pengaturan Pinjaman</h3>
                    <p class="text-xs text-mony-muted mt-1">Menentukan batas atas pinjaman yang bisa diajukan anggota dari nilai taksiran barang.</p>
                </div>
                <div>
                    <label class="form-label">Max % Pinjaman <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="max_loan_percentage" x-model.number="maxPct"
                               class="form-input @error('max_loan_percentage') border-red-400 @enderror" min="1" max="100" step="0.01" required>
                        <span class="absolute top-1/2 -translate-y-1/2 right-3 text-sm text-mony-muted">%</span>
                    </div>
                    @error('max_loan_percentage') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="p-3 rounded-xl text-xs" style="background: var(--green-light); color: var(--green2)">
                    <p class="font-semibold mb-1">Rentang pinjaman anggota: <span x-text="minPct"></span>%–<span x-text="maxPct"></span>% dari taksiran</p>
                    <p>Contoh untuk taksiran <span class="font-semibold" x-text="rupiah(baseValue)"></span>: anggota dapat mengajukan
                        <span class="font-semibold" x-text="rupiah(exampleMin)"></span> – <span class="font-semibold" x-text="rupiah(exampleMax)"></span>.</p>
                    <p class="mt-1">Batas ini juga berlaku saat pengurus/admin menilai &amp; menyetujui pinjaman.</p>
                </div>
            </div>

            {{-- Status --}}
            <div class="card p-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="text-sm font-medium">Aktif &mdash; tampil di katalog &amp; pengajuan gadai</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    {{ $item->id ? 'Simpan Perubahan' : 'Tambah Barang' }}
                </button>
                <a href="{{ route('admin.katalog.index') }}" class="btn-outline">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection