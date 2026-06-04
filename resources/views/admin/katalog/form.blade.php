@extends('layouts.admin')
@php
$title = $item->id ? 'Edit Jenis Barang' : 'Tambah Jenis Barang';
@endphp
@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('admin.katalog.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">{{ $item->id ? 'Edit' : 'Tambah' }} Jenis Barang Gadai</h1>
</div>

<div class="max-w-2xl">
    <form method="POST"
          action="{{ $item->id ? route('admin.katalog.update', $item) : route('admin.katalog.store') }}"
          enctype="multipart/form-data"
          class="card p-6 space-y-4">
        @csrf
        @if($item->id) @method('PUT') @endif

        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="form-label">Nama Barang <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $item->name) }}"
                       class="form-input @error('name') border-red-400 @enderror" required>
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="category" value="{{ old('category', $item->category) }}"
                       class="form-input" placeholder="Elektronik, Perhiasan, dll" required>
            </div>

            <div>
                <label class="form-label">Satuan</label>
                <input type="text" name="unit" value="{{ old('unit', $item->unit ?? 'unit') }}" class="form-input">
            </div>

            <div>
                <label class="form-label">Nilai Dasar per Satuan (Rp)</label>
                <input type="number" name="base_value_per_unit" value="{{ old('base_value_per_unit', $item->base_value_per_unit) }}" class="form-input" min="0">
            </div>

            <div>
                <label class="form-label">Max % Pinjaman</label>
                <input type="number" name="max_loan_percentage" value="{{ old('max_loan_percentage', $item->max_loan_percentage ?? 80) }}"
                       class="form-input" min="1" max="100" step="0.01">
            </div>

            <div class="col-span-2">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-input" rows="2">{{ old('description', $item->description) }}</textarea>
            </div>

            <div class="col-span-2">
                <label class="form-label">Kondisi (satu per baris)</label>
                <textarea name="conditions_raw" class="form-input font-mono text-xs" rows="4"
                          placeholder="Baru&#10;Sangat Baik&#10;Baik&#10;Cukup">{{ old('conditions_raw', $item->conditions ? implode("\n", $item->conditions) : '') }}</textarea>
            </div>

            <div class="col-span-2">
                <label class="form-label">Merek & Estimasi Nilai (format: MerekNama | NilaiRupiah, satu per baris)</label>
                <textarea name="brands_raw" class="form-input font-mono text-xs" rows="4"
                          placeholder="iPhone 14 Pro | 14000000&#10;Samsung Galaxy S23 | 9000000">{{ old('brands_raw', $item->brands ? collect($item->brands)->map(fn($b) => ($b['name'] ?? '') . ' | ' . ($b['value'] ?? 0))->implode("\n") : '') }}</textarea>
            </div>

            <div class="col-span-2">
                <label class="form-label">Persyaratan (satu per baris)</label>
                <textarea name="requirements_raw" class="form-input font-mono text-xs" rows="3"
                          placeholder="Charger asli&#10;Box jika ada">{{ old('requirements_raw', $item->requirements ? implode("\n", $item->requirements) : '') }}</textarea>
            </div>

            <div class="col-span-2">
                <label class="form-label">Gambar Barang</label>
                @if($item->image_path)
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="Gambar" class="w-32 h-24 object-cover rounded-lg mb-2">
                @endif
                <input type="file" name="image" accept="image/*" class="form-input">
                <p class="form-hint">JPG/PNG, maks. 2MB</p>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="text-sm font-medium">Aktif</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">
                {{ $item->id ? 'Simpan Perubahan' : 'Tambah Barang' }}
            </button>
            <a href="{{ route('admin.katalog.index') }}" class="btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
