@extends('layouts.admin')
@php
$title = 'Biaya Operasional';
@endphp
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="page-title">Biaya Operasional</h1>
    <a href="{{ route('admin.biaya.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Catat Biaya
    </a>
</div>

<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="stat-card">
        <span class="stat-label">Biaya Bulan Ini</span>
        <span class="stat-value text-xl">Rp {{ number_format($totalMonth, 0, ',', '.') }}</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Biaya Tahun Ini</span>
        <span class="stat-value text-xl">Rp {{ number_format($totalYear, 0, ',', '.') }}</span>
    </div>
</div>

<div class="card p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Kategori</label>
            <select name="category" class="form-input">
                <option value="">Semua</option>
                @foreach(\App\Models\BiayaOperasional::categories() as $cat)
                    <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Bulan</label>
            <select name="month" class="form-input">
                <option value="">Semua</option>
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" @selected(request('month') == $m)>{{ \Carbon\Carbon::create(null,$m)->isoFormat('MMMM') }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Tahun</label>
            <select name="year" class="form-input">
                @foreach(range(now()->year, 2020) as $y)
                    <option value="{{ $y }}" @selected(request('year', now()->year) == $y)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
    </form>
</div>

<div class="card overflow-hidden">
    <table class="table-base">
        <thead><tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th>Jumlah</th><th>Dicatat Oleh</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($biaya as $b)
            <tr>
                <td class="text-xs">{{ $b->date->format('d/m/Y') }}</td>
                <td><span class="badge-gray text-xs">{{ $b->category }}</span></td>
                <td class="text-sm">{{ $b->description ?? '-' }}</td>
                <td class="font-medium">Rp {{ number_format($b->amount, 0, ',', '.') }}</td>
                <td class="text-xs text-mony-muted">{{ $b->recorder?->name }}</td>
                <td>
                    <div class="flex gap-1">
                        <a href="{{ route('admin.biaya.edit', $b) }}" class="btn-ghost btn-sm btn-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.biaya.destroy', $b) }}"
                              onsubmit="return confirm('Hapus biaya ini?')">
                            @csrf @method('DELETE')
                            <button class="btn-ghost btn-sm btn-icon text-red-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-8 text-mony-muted">Tidak ada data biaya</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($biaya->hasPages())
        <div class="px-4 py-3 border-t">{{ $biaya->links() }}</div>
    @endif
</div>
@endsection
