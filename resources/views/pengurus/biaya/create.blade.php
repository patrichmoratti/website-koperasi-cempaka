@extends('layouts.pengurus')
@php
$title = 'Catat Biaya';
@endphp
@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('pengurus.dashboard') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Dashboard
    </a>
    <h1 class="page-title">Catat Biaya Operasional</h1>
</div>

<div class="max-w-lg">
    <form method="POST" action="{{ route('pengurus.biaya.store') }}" class="card p-6 space-y-4">
        @csrf
        <div>
            <label class="form-label">Tanggal <span class="text-red-500">*</span></label>
            <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" class="form-input" required>
        </div>
        <div>
            <label class="form-label">Kategori <span class="text-red-500">*</span></label>
            <select name="category" class="form-input" required>
                @foreach(\App\Models\BiayaOperasional::categories() as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Jumlah (Rp) <span class="text-red-500">*</span></label>
            <input type="number" name="amount" class="form-input" min="1" required>
        </div>
        <div>
            <label class="form-label">Keterangan</label>
            <textarea name="description" class="form-input" rows="3"></textarea>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Simpan</button>
            <a href="{{ route('pengurus.dashboard') }}" class="btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
