@extends('layouts.admin')
@php
$title = 'Katalog Barang Gadai';
@endphp
@section('content')
@php
    $catalogImages = [
        'Laptop / Notebook' => 'laptop.jpg',
        'Smartphone / HP' => 'handphone.jpg',
        'TV / Televisi' => 'television.jpg',
        'Kulkas / Lemari Es' => 'kulkas.webp',
        'Rice Cooker / Magic Com' => 'rice-cooker.png',
        'Kompor Gas' => 'kompor.jpg',
        'Kipas Angin / AC' => 'kipas.jpeg',
        'Perhiasan Emas' => 'perhiasan.jpg',
        'Sepeda / Sepeda Listrik' => 'sepeda.jpg',
        'Motor / Sepeda Motor' => 'motor.webp',
    ];
@endphp
<div class="flex items-center justify-between mb-6">
    <h1 class="page-title">Katalog Jenis Barang Gadai</h1>
    <a href="{{ route('admin.katalog.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Jenis Barang
    </a>
</div>

<div class="card p-4 mb-4">
    <form method="GET" class="flex gap-3 items-end">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Cari nama barang...">
        </div>
        <button type="submit" class="btn-primary">Cari</button>
        @if(request('search'))
            <a href="{{ route('admin.katalog.index') }}" class="btn-outline">Reset</a>
        @endif
    </form>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($katalog as $item)
    <div class="card overflow-hidden hover:shadow-card-hover transition-shadow">
        <div class="h-36 bg-gray-100 flex items-center justify-center overflow-hidden">
            @if($item->image_path)
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}"
                     class="w-full h-full object-cover">
            @elseif(isset($catalogImages[$item->name]))
                <img src="{{ asset('images/' . $catalogImages[$item->name]) }}" alt="{{ $item->name }}"
                     class="w-full h-full object-cover">
            @else
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            @endif
        </div>
        <div class="p-4">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h3 class="font-semibold text-sm text-mony-text">{{ $item->name }}</h3>
                    <p class="text-xs text-mony-muted">{{ $item->category }}</p>
                </div>
                <span class="badge-{{ $item->is_active ? 'success' : 'gray' }} text-xs ml-2 flex-shrink-0">
                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <div class="flex items-center justify-end text-xs text-mony-muted mb-3">
                <span>{{ $item->unit }}</span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.katalog.edit', $item) }}" class="btn-outline btn-sm flex-1 text-center">Edit</a>
                @if($item->is_active)
                    <form method="POST" action="{{ route('admin.katalog.destroy', $item) }}"
                          onsubmit="return confirmAction(event, 'Nonaktifkan {{ addslashes($item->name) }}?', 'Ya, Nonaktifkan')">
                        @csrf @method('DELETE')
                        <button class="btn-ghost btn-sm btn-icon text-red-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12 text-mony-muted">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        <p>Belum ada katalog barang</p>
    </div>
    @endforelse
</div>

@if($katalog->hasPages())
    <div class="mt-4">{{ $katalog->links() }}</div>
@endif
@endsection
