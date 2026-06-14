@extends('layouts.admin')
@php
$title = 'Pembayaran';
@endphp
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-mony-text tracking-tight">Data Pembayaran</h1>
        <p class="text-sm mt-1 text-mony-muted">Rekap seluruh pembayaran anggota yang telah disetujui &mdash; simpanan &amp; gadai</p>
    </div>
    <a href="{{ route('admin.simpanan.export') }}" class="btn-outline btn-sm flex-shrink-0">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export Excel
    </a>
</div>

{{-- Stats strip --}}
<div class="card p-0 mb-4 overflow-hidden">
    <div class="grid grid-cols-1 sm:grid-cols-3">
        <div class="flex items-center gap-3 p-4 border-b sm:border-b-0 sm:border-r" style="border-color:#eef3ea">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:var(--green-light)">
                <svg class="w-4 h-4" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-9 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-mony-muted">Total Simpanan Disetujui</p>
                <p class="text-xl font-bold text-mony-text">Rp {{ number_format($summary['total_simpanan'], 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 p-4 border-b sm:border-b-0 sm:border-r" style="border-color:#eef3ea">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#fdf1de">
                <svg class="w-4 h-4" style="color:#D97706" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-mony-muted">Total Pembayaran Gadai Disetujui</p>
                <p class="text-xl font-bold text-mony-text">Rp {{ number_format($summary['total_gadai'], 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 p-4">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#e7f0fb">
                <svg class="w-4 h-4" style="color:#3B82F6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-mony-muted">Total Keseluruhan</p>
                <p class="text-xl font-bold text-mony-text">Rp {{ number_format($summary['total_simpanan'] + $summary['total_gadai'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="form-label">Cari Anggota</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Nama anggota...">
        </div>
        <div class="w-48">
            <label class="form-label">Jenis Pembayaran</label>
            <select name="jenis" class="form-input">
                <option value="">Semua</option>
                <option value="simpanan_pokok" @selected(request('jenis') === 'simpanan_pokok')>Simpanan Pokok</option>
                <option value="simpanan_wajib" @selected(request('jenis') === 'simpanan_wajib')>Simpanan Wajib</option>
                <option value="gadai_bunga" @selected(request('jenis') === 'gadai_bunga')>Gadai - Bunga</option>
                <option value="gadai_tebus" @selected(request('jenis') === 'gadai_tebus')>Gadai - Tebus</option>
            </select>
        </div>
        <div class="w-36">
            <label class="form-label">Bulan</label>
            <select name="month" class="form-input">
                <option value="">Semua</option>
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" @selected(request('month') == $m)>{{ \Carbon\Carbon::create(null,$m)->isoFormat('MMMM') }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-28">
            <label class="form-label">Tahun</label>
            <select name="year" class="form-input">
                <option value="">Semua</option>
                @foreach(range(now()->year, 2020) as $y)
                    <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->hasAny(['search','jenis','month','year']))
            <a href="{{ route('admin.simpanan.index') }}" class="btn-outline">Reset</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table-base">
            <thead>
                <tr>
                    <th>Anggota</th>
                    <th>Jenis</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                    <th>Disetujui Oleh</th>
                    <th>Tanggal Disetujui</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayaran as $p)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-sm flex-shrink-0"
                                 style="background: var(--green-light); color: var(--green)">
                                {{ strtoupper(substr($p['anggota']?->name ?? '-', 0, 1)) }}
                            </div>
                            <p class="font-medium text-sm">{{ $p['anggota']?->name ?? '-' }}</p>
                        </div>
                    </td>
                    <td><span class="badge-{{ $p['badge'] }}">{{ $p['jenis'] }}</span></td>
                    <td class="text-xs">
                        @if($p['detail_route'])
                            <a href="{{ $p['detail_route'] }}" class="text-primary hover:underline">{{ $p['keterangan'] }}</a>
                        @else
                            {{ $p['keterangan'] }}
                        @endif
                    </td>
                    <td class="font-medium text-sm">Rp {{ number_format($p['amount'], 0, ',', '.') }}</td>
                    <td class="text-xs">{{ $p['confirmed_by']?->name ?? '-' }}</td>
                    <td class="text-xs text-mony-muted">{{ $p['confirmed_at']?->format('d/m/Y H:i') ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-mony-muted">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-9 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm">Belum ada pembayaran yang disetujui</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pembayaran->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $pembayaran->links() }}
        </div>
    @endif
</div>

@endsection