@extends('layouts.anggota')
@php
$title = 'Bayar Simpanan';
@endphp
@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('anggota.simpanan.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">Bayar Simpanan</h1>
</div>

<div class="max-w-lg" x-data="{ type: 'wajib' }">
    <form method="POST" action="{{ route('anggota.simpanan.bayar.store') }}" enctype="multipart/form-data"
          class="card p-6 space-y-5">
        @csrf

        <div>
            <label class="form-label">Tipe Simpanan</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="wajib" x-model="type" class="hidden peer">
                    <div class="peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 border border-gray-200 rounded-xl p-4 text-center hover:border-primary transition-all">
                        <p class="font-medium">Simpanan Wajib</p>
                        <p class="text-xs text-mony-muted">Rp 100.000/bulan</p>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="pokok" x-model="type" class="hidden peer">
                    <div class="peer-checked:ring-2 peer-checked:ring-primary peer-checked:bg-primary/5 border border-gray-200 rounded-xl p-4 text-center hover:border-primary transition-all">
                        <p class="font-medium">Simpanan Pokok</p>
                        <p class="text-xs text-mony-muted">Rp 500.000 (sekali)</p>
                    </div>
                </label>
            </div>
        </div>

        <div x-show="type === 'wajib'" class="grid grid-cols-2 gap-4">
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

        <div>
            <label class="form-label">Jumlah (Rp)</label>
            <input type="number" name="amount"
                   :value="type === 'pokok' ? 500000 : 100000"
                   class="form-input" min="1" required>
        </div>

        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl text-sm">
            <p class="font-medium mb-2">Transfer ke:</p>
            <p><span class="text-mony-muted">Bank:</span> <strong>{{ $info->bank_name }}</strong></p>
            <p><span class="text-mony-muted">No. Rek:</span> <strong>{{ $info->bank_account_number }}</strong></p>
            <p><span class="text-mony-muted">A/N:</span> <strong>{{ $info->bank_account_name }}</strong></p>
        </div>

        <div>
            <label class="form-label">Bukti Transfer</label>
            <input type="file" name="transfer_proof" accept="image/*,application/pdf" class="form-input" required>
            <p class="form-hint">JPG/PNG/PDF, maks. 2MB</p>
        </div>

        <button type="submit" class="btn-primary w-full py-3">Kirim Bukti Simpanan</button>
    </form>
</div>
@endsection
