@extends('layouts.anggota')
@php
$title = 'Simulasi Gadai';
@endphp
@section('content')
<div class="mb-6">
    <h1 class="page-title">Simulasi Gadai</h1>
    <p class="text-sm text-mony-muted mt-1">Hitung estimasi pinjaman dan cicilan bunga sebelum mengajukan</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-4xl"
     x-data="{
        jenisId: '',
        maxPct: 80,
        estimasiNilai: 0,
        pinjaman: 0,
        durasi: 4,
        bunga: 8,
        get monthlyInterest() { return Math.round(this.pinjaman * this.bunga / 100); },
        get totalBunga() { return this.monthlyInterest * this.durasi; },
        get totalTebus() { return parseInt(this.pinjaman) + this.totalBunga; },
        get maxPinjaman() { return Math.floor(this.estimasiNilai * this.maxPct / 100); },
        get schedule() {
            let rows = [];
            for(let i = 1; i <= this.durasi; i++) {
                rows.push({
                    bulan: i,
                    bunga: this.monthlyInterest,
                    kumulatifBunga: this.monthlyInterest * i,
                    sisaPokok: parseInt(this.pinjaman)
                });
            }
            return rows;
        }
     }">

    {{-- Input --}}
    <div class="card p-6 space-y-4">
        <h3 class="section-title">Parameter Simulasi</h3>

        <div>
            <label class="form-label">Jenis Barang</label>
            <select x-model="jenisId" @change="maxPct = $event.target.options[$event.target.selectedIndex].dataset.pct || 80" class="form-input">
                <option value="">-- Pilih Jenis Barang --</option>
                @foreach($jenisBarang as $j)
                    <option value="{{ $j->id }}" data-pct="{{ $j->max_loan_percentage }}">{{ $j->name }} (Max {{ $j->max_loan_percentage }}%)</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label">Estimasi Nilai Barang (Rp)</label>
            <input type="number" x-model="estimasiNilai" class="form-input" min="0" placeholder="0">
            <p class="form-hint">Maksimal pinjaman: Rp <span x-text="maxPinjaman.toLocaleString('id-ID')"></span></p>
        </div>

        <div>
            <label class="form-label">Jumlah Pinjaman (Rp)</label>
            <input type="number" x-model="pinjaman" :max="maxPinjaman" class="form-input" min="0" placeholder="0">
        </div>

        <div>
            <label class="form-label">Durasi Gadai (Bulan): <span class="font-bold text-primary" x-text="durasi"></span></label>
            <input type="range" x-model.number="durasi" min="1" max="4" class="w-full accent-primary">
            <div class="flex justify-between text-xs text-mony-muted mt-1">
                <span>1 bulan</span><span>4 bulan (max)</span>
            </div>
        </div>

        <div class="p-4 bg-primary/5 border border-primary/20 rounded-xl text-sm">
            <p class="text-mony-muted">Bunga: <strong class="text-primary">8% per bulan</strong> (dari jumlah pinjaman)</p>
        </div>
    </div>

    {{-- Result --}}
    <div class="space-y-4">
        <div class="card p-6">
            <h3 class="section-title mb-4">Hasil Simulasi</h3>
            <template x-if="pinjaman > 0">
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-mony-muted">Jumlah Pinjaman</span>
                        <span class="font-semibold">Rp <span x-text="parseInt(pinjaman).toLocaleString('id-ID')"></span></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-mony-muted">Bunga per Bulan</span>
                        <span class="font-semibold text-yellow-600">Rp <span x-text="monthlyInterest.toLocaleString('id-ID')"></span></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-mony-muted">Total Bunga (<span x-text="durasi"></span> bln)</span>
                        <span class="font-semibold text-yellow-600">Rp <span x-text="totalBunga.toLocaleString('id-ID')"></span></span>
                    </div>
                    <div class="flex justify-between py-3 bg-primary/5 px-3 rounded-xl">
                        <span class="font-semibold text-primary">Total Tebus</span>
                        <span class="font-bold text-primary text-lg">Rp <span x-text="totalTebus.toLocaleString('id-ID')"></span></span>
                    </div>
                </div>
            </template>
            <template x-if="pinjaman <= 0">
                <p class="text-center text-mony-muted py-4 text-sm">Masukkan jumlah pinjaman untuk melihat simulasi</p>
            </template>
        </div>

        <template x-if="pinjaman > 0">
            <div class="card p-5">
                <h3 class="section-title mb-3">Jadwal Pembayaran Bunga</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b border-gray-100">
                            <th class="py-2 text-left text-xs text-mony-muted font-medium">Bulan</th>
                            <th class="py-2 text-right text-xs text-mony-muted font-medium">Bunga</th>
                            <th class="py-2 text-right text-xs text-mony-muted font-medium">Total Bunga</th>
                        </tr></thead>
                        <tbody>
                            <template x-for="row in schedule" :key="row.bulan">
                                <tr class="border-b border-gray-50">
                                    <td class="py-2" x-text="'Bulan ' + row.bulan"></td>
                                    <td class="py-2 text-right text-yellow-600" x-text="'Rp ' + row.bunga.toLocaleString('id-ID')"></td>
                                    <td class="py-2 text-right font-medium" x-text="'Rp ' + row.kumulatifBunga.toLocaleString('id-ID')"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </div>
</div>

<div class="mt-6 max-w-4xl">
    <a href="{{ route('anggota.gadai.pengajuan.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajukan Gadai Sekarang
    </a>
</div>
@endsection
