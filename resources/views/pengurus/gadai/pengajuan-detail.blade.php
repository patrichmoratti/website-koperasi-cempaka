@extends('layouts.pengurus')
@php
$title = 'Detail Pengajuan';
@endphp
@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('pengurus.gadai.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">Detail Pengajuan Gadai</h1>
</div>

<div class="card p-6 max-w-2xl">
    <div class="grid grid-cols-2 gap-4 text-sm mb-6">
        <div><p class="text-mony-muted">Anggota</p><p class="font-medium">{{ $pengajuan->anggota?->name }}</p></div>
        <div><p class="text-mony-muted">Jenis Barang</p><p class="font-medium">{{ $pengajuan->jenisBarang?->name }}</p></div>
        <div><p class="text-mony-muted">Kondisi</p><p class="font-medium">{{ $pengajuan->condition }}</p></div>
        <div><p class="text-mony-muted">Berat/Jumlah</p><p class="font-medium">{{ $pengajuan->weight_or_quantity ?? '-' }}</p></div>
        <div><p class="text-mony-muted">Estimasi Nilai</p><p class="font-semibold">Rp {{ number_format($pengajuan->estimated_value, 0, ',', '.') }}</p></div>
        <div><p class="text-mony-muted">Pinjaman Diminta</p><p class="font-semibold text-primary">Rp {{ number_format($pengajuan->loan_request_amount, 0, ',', '.') }}</p></div>
        <div class="col-span-2"><p class="text-mony-muted">Deskripsi</p><p class="font-medium">{{ $pengajuan->description }}</p></div>
    </div>

    @if($pengajuan->item_photo_paths)
    <div class="mb-6">
        <p class="text-mony-muted text-sm mb-2">Foto Barang</p>
        <div class="grid grid-cols-3 gap-2">
            @foreach($pengajuan->item_photo_paths as $photo)
                <div @click="$store.lb = { show: true, src: '{{ asset('storage/' . $photo) }}', type: 'image' }"
                     class="rounded-lg overflow-hidden border border-gray-200 cursor-pointer group" style="height:6rem;">
                    <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover group-hover:opacity-85 transition-opacity">
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="flex items-center gap-3">
        <span class="badge-{{ $pengajuan->status_color }}">{{ $pengajuan->status_label }}</span>
        <p class="text-sm text-mony-muted">Diajukan {{ $pengajuan->submitted_at->diffForHumans() }}</p>
    </div>
</div>
@endsection
