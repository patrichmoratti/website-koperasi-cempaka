@extends('layouts.admin')
@php
$title = 'Buat Periode SHU';
@endphp
@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('admin.shu.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">Buat Periode SHU</h1>
</div>

<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.shu.store') }}" class="card p-6 space-y-4">
        @csrf

        <div>
            <label class="form-label">Tahun <span class="text-red-500">*</span></label>
            <input type="number" name="year" value="{{ old('year', now()->year) }}"
                   class="form-input @error('year') border-red-400 @enderror"
                   min="2020" max="2099" required>
            @error('year') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="border-t pt-4">
            <p class="section-title mb-3">Persentase Alokasi SHU</p>
            <p class="text-xs text-mony-muted mb-4">Total persentase harus = 100%</p>
            <div class="grid grid-cols-2 gap-3">
                @foreach([
                    'pct_dana_cadangan'   => ['Dana Cadangan', 25],
                    'pct_jasa_modal'      => ['Jasa Modal (Anggota)', 25],
                    'pct_jasa_usaha'      => ['Jasa Usaha (Anggota)', 30],
                    'pct_dana_pengurus'   => ['Dana Pengurus', 10],
                    'pct_dana_pendidikan' => ['Dana Pendidikan', 5],
                    'pct_dana_sosial'     => ['Dana Sosial', 5],
                ] as $field => [$label, $default])
                <div>
                    <label class="form-label">{{ $label }} (%)</label>
                    <input type="number" name="{{ $field }}" value="{{ old($field, $default) }}"
                           class="form-input" min="0" max="100" step="0.01" required>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">Buat Periode</button>
            <a href="{{ route('admin.shu.index') }}" class="btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
