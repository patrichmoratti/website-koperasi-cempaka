@extends('layouts.anggota')
@php
$title = 'Ajukan Gadai';
@endphp
@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('anggota.gadai.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">Pengajuan Gadai Baru</h1>
</div>

<div class="max-w-2xl"
     x-data="{
        step: 1,
        maxStep: 4,
        jenisId: '',
        jenisName: '',
        maxPct: 80,
        estimasiNilai: 0,
        maxPinjaman: 0,
        photos: [],
        docs: [],
        updateMax() { this.maxPinjaman = Math.floor(this.estimasiNilai * this.maxPct / 100); }
     }">

    {{-- Step Indicator --}}
    <div class="flex items-center gap-2 mb-6">
        @foreach(['Pilih Barang', 'Detail', 'Upload Foto', 'Review'] as $i => $label)
        <div class="flex items-center gap-2 {{ !$loop->last ? 'flex-1' : '' }}">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium flex-shrink-0 transition-colors"
                 :class="step > {{ $i+1 }} ? 'bg-primary text-white' : (step === {{ $i+1 }} ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500')">
                <template x-if="step > {{ $i+1 }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </template>
                <template x-if="step <= {{ $i+1 }}">
                    <span>{{ $i+1 }}</span>
                </template>
            </div>
            <span class="text-xs font-medium hidden sm:block" :class="step === {{ $i+1 }} ? 'text-primary' : 'text-mony-muted'">{{ $label }}</span>
            @if(!$loop->last) <div class="flex-1 h-px bg-gray-200 mx-2"></div> @endif
        </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('anggota.gadai.pengajuan.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Step 1: Pilih Jenis Barang --}}
        <div x-show="step === 1">
            <div class="card p-5 mb-4">
                <h3 class="section-title mb-4">Pilih Jenis Barang</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($jenisBarang as $jenis)
                    <label class="cursor-pointer">
                        <input type="radio" name="jenis_barang_id" value="{{ $jenis->id }}" class="hidden peer"
                               @change="jenisId = '{{ $jenis->id }}'; jenisName = '{{ addslashes($jenis->name) }}'; maxPct = {{ $jenis->max_loan_percentage }}; updateMax()">
                        <div class="peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 border border-gray-200 rounded-xl p-4 text-center hover:border-primary transition-all">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl mx-auto mb-2 flex items-center justify-center overflow-hidden">
                                @if($jenis->image_path)
                                    <img src="{{ asset('storage/' . $jenis->image_path) }}" alt="{{ $jenis->name }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                @endif
                            </div>
                            <p class="text-xs font-medium">{{ $jenis->name }}</p>
                            <p class="text-xs text-mony-muted">Max {{ $jenis->max_loan_percentage }}%</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('jenis_barang_id') <p class="form-error mt-2">{{ $message }}</p> @enderror
            </div>
            <div class="flex justify-end">
                <button type="button" @click="step = 2" :disabled="!jenisId" class="btn-primary">
                    Lanjut →
                </button>
            </div>
        </div>

        {{-- Step 2: Detail Barang --}}
        <div x-show="step === 2">
            <div class="card p-5 mb-4">
                <h3 class="section-title mb-4">Detail Barang: <span class="text-primary" x-text="jenisName"></span></h3>
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="description" class="form-input @error('description') border-red-400 @enderror" rows="3"
                                  placeholder="Contoh: Laptop ASUS ROG G15, Core i7, RAM 16GB, SSD 512GB, tahun 2022" required>{{ old('description') }}</textarea>
                        @error('description') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Kondisi <span class="text-red-500">*</span></label>
                            <input type="text" name="condition" class="form-input" placeholder="Baik / Sangat Baik / dll" required>
                        </div>
                        <div>
                            <label class="form-label">Berat / Jumlah</label>
                            <input type="text" name="weight_or_quantity" class="form-input" placeholder="1 unit / 5 gram / dll">
                        </div>
                        <div>
                            <label class="form-label">Estimasi Nilai (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="estimated_value"
                                   x-model="estimasiNilai" @input="updateMax()"
                                   class="form-input" min="100000" required>
                        </div>
                        <div>
                            <label class="form-label">Pinjaman Diminta (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="loan_request_amount"
                                   class="form-input @error('loan_request_amount') border-red-400 @enderror"
                                   :max="maxPinjaman" min="100000" required>
                            <p class="form-hint" x-show="maxPinjaman > 0">
                                Maks: Rp <span x-text="maxPinjaman.toLocaleString('id-ID')"></span>
                                (<span x-text="maxPct"></span>% dari estimasi)
                            </p>
                        </div>
                    </div>
                    @error('loan_request_amount') <p class="form-error">{{ $message }}</p> @enderror
                    @error('estimated_value') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex gap-3 justify-between">
                <button type="button" @click="step = 1" class="btn-outline">← Kembali</button>
                <button type="button" @click="step = 3" class="btn-primary">Lanjut →</button>
            </div>
        </div>

        {{-- Step 3: Upload Foto --}}
        <div x-show="step === 3">
            <div class="card p-5 mb-4">
                <h3 class="section-title mb-4">Upload Foto Barang</h3>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-primary transition-colors"
                     @dragover.prevent @drop.prevent="
                        Array.from($event.dataTransfer.files).forEach(f => {
                            if(f.type.startsWith('image/')) photos.push({file: f, url: URL.createObjectURL(f)});
                        })">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-mony-muted mb-2">Drag foto atau klik tombol di bawah</p>
                    <input type="file" name="item_photos[]" accept="image/*" multiple class="hidden" id="photoInput"
                           @change="Array.from($event.target.files).forEach(f => photos.push({file: f, url: URL.createObjectURL(f)}))">
                    <label for="photoInput" class="btn-outline btn-sm cursor-pointer">Pilih Foto</label>
                </div>
                <div class="grid grid-cols-4 gap-2 mt-3">
                    <template x-for="(p, i) in photos" :key="i">
                        <div class="relative">
                            <img :src="p.url" class="w-full h-20 object-cover rounded-lg">
                            <button type="button" @click="photos.splice(i, 1)"
                                    class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center">×</button>
                        </div>
                    </template>
                </div>
                @error('item_photos') <p class="form-error">{{ $message }}</p> @enderror
                @error('item_photos.*') <p class="form-error">{{ $message }}</p> @enderror

                <div class="mt-4">
                    <label class="form-label">Dokumen Pendukung (opsional)</label>
                    <input type="file" name="supporting_docs[]" multiple class="form-input" accept="image/*,application/pdf">
                </div>
            </div>
            <div class="flex gap-3 justify-between">
                <button type="button" @click="step = 2" class="btn-outline">← Kembali</button>
                <button type="button" @click="step = 4" :disabled="photos.length < 1" class="btn-primary">Review →</button>
            </div>
        </div>

        {{-- Step 4: Review & Submit --}}
        <div x-show="step === 4">
            <div class="card p-5 mb-4">
                <h3 class="section-title mb-4">Review Pengajuan</h3>
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-sm text-yellow-800 mb-4">
                    <strong>Perhatian:</strong> Pastikan semua data sudah benar sebelum mengajukan. Pengajuan yang sudah disubmit tidak dapat diubah.
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-mony-muted">Jenis Barang</span>
                        <span class="font-medium" x-text="jenisName"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-mony-muted">Estimasi Nilai</span>
                        <span class="font-medium" x-text="'Rp ' + Number(estimasiNilai).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-mony-muted">Bunga per Bulan</span>
                        <span class="font-medium">8% dari pinjaman</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-mony-muted">Jatuh Tempo</span>
                        <span class="font-medium">4 bulan dari tanggal gadai</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-mony-muted">Foto Barang</span>
                        <span class="font-medium" x-text="photos.length + ' foto'"></span>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 justify-between">
                <button type="button" @click="step = 3" class="btn-outline">← Kembali</button>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Ajukan Sekarang
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
