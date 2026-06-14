@extends('layouts.anggota')
@php $title = 'Gadai Saya'; @endphp
@section('content')

{{-- Page header --}}
<div class="flex items-center justify-between mb-5">
    <div>
        <span class="badge-primary text-xs mb-1 inline-block">Pegadaian</span>
        <h1 class="text-2xl font-bold text-mony-text tracking-tight">Gadai Saya</h1>
    </div>
    <a href="{{ route('anggota.gadai.pengajuan.create') }}" class="btn-primary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajukan Gadai
    </a>
</div>

{{-- Tab navigation --}}
<div class="flex gap-1 p-1 rounded-2xl mb-5" style="background: rgba(255,255,255,0.1);">
    @foreach(['proses'=>'Proses','verifikasi'=>'Verifikasi','aktif'=>'Aktif','selesai'=>'Selesai'] as $key => $label)
    <a href="{{ route('anggota.gadai.index', ['tab' => $key]) }}"
       class="flex-1 text-center py-2 rounded-xl text-xs font-semibold transition-all relative inline-flex items-center justify-center gap-1.5"
       style="{{ $tab === $key ? 'background:white; color:var(--green); box-shadow:0 1px 6px rgba(0,0,0,0.15);' : 'color:rgba(255,255,255,0.55);' }}"
       onmouseover="{{ $tab !== $key ? "this.style.color='rgba(255,255,255,0.9)'" : '' }}"
       onmouseout="{{ $tab !== $key ? "this.style.color='rgba(255,255,255,0.55)'" : '' }}">
        <span>{{ $label }}</span>
        @if(($counts[$key] ?? 0) > 0)
        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center leading-none"
              style="{{ $tab === $key ? 'background:var(--green-light); color:var(--green);' : 'background:rgba(255,255,255,0.2); color:#fff;' }}">
            {{ $counts[$key] }}
        </span>
        @endif
    </a>
    @endforeach
</div>

{{-- ══════════════════════════════════ --}}
{{-- PROSES TAB                        --}}
{{-- ══════════════════════════════════ --}}
@if($tab === 'proses')
@forelse($pengajuanProses as $p)
@php
    $thumb = !empty($p->item_photo_paths[0]) ? asset('storage/'.$p->item_photo_paths[0]) : null;
    $photos = $p->item_photo_paths ?? [];
@endphp
<div x-data="{ open: false }" class="mb-3">
    {{-- Card --}}
    <div @click="open = true"
         class="card p-4 flex items-start gap-3 cursor-pointer hover:shadow-md transition-shadow active:scale-[0.99]">
        <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-100">
            @if($thumb)
            <img src="{{ $thumb }}" class="w-full h-full object-cover">
            @else
            <div class="w-full h-full flex items-center justify-center" style="background:var(--green-light)">
                <svg class="w-7 h-7" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2 mb-1">
                <div>
                    <p class="font-semibold text-sm text-mony-text leading-tight">{{ $p->jenisBarang?->name }}</p>
                    <p class="text-xs text-mony-muted">{{ $p->brand_name ?? '-' }}</p>
                </div>
                <span class="badge-warning text-xs flex-shrink-0">Proses</span>
            </div>
            <div class="flex items-end justify-between mt-2">
                <div>
                    <p class="text-xs text-mony-muted">Ajuan Pinjaman</p>
                    <p class="text-sm font-bold" style="color:var(--green)">Rp {{ number_format($p->loan_request_amount, 0, ',', '.') }}</p>
                </div>
                <p class="text-xs text-mony-muted">{{ $p->submitted_at?->format('d M Y') }}</p>
            </div>
        </div>
        <svg class="w-4 h-4 text-gray-300 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </div>

    {{-- Popup: Detail Pengajuan (proses) --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @click.self="open = false" @keydown.escape.window="if(!$store.lb.show){ open = false }"
         style="background:rgba(8,20,12,0.72); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);"
         x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
<div class="bg-white popup-sheet w-full shadow-2xl scrollbar-hide" style="max-width:620px; max-height:88vh; overflow-y:auto; border-radius:24px;"
             @click.stop x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            {{-- Header --}}
            <div class="relative overflow-hidden rounded-t-3xl px-6 py-5" style="background:linear-gradient(135deg, var(--green) 0%, var(--green2) 100%);">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:rgba(255,255,255,0.15);">
                            <img src="{{ asset('images/logo-mony.png') }}" alt="MONY" class="w-7 h-7 object-contain">
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white leading-tight">Detail Pengajuan</h2>
                            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6);">Diajukan {{ $p->submitted_at?->format('d M Y') }}</p>
                        </div>
                    </div>
                    <button @click="open = false" class="w-8 h-8 flex items-center justify-center rounded-full flex-shrink-0 transition-colors" style="background:rgba(255,255,255,0.15);" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            {{-- Photos --}}
            @if(count($photos))
            <div class="grid grid-cols-3 gap-2 px-4 mt-4">
                @foreach(array_slice($photos, 0, 3) as $photo)
                <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/'.$photo) }}', type: 'image' }"
                     class="rounded-xl overflow-hidden cursor-pointer group" style="aspect-ratio:1">
                    <img src="{{ asset('storage/'.$photo) }}" class="w-full h-full object-cover group-hover:opacity-85 transition-opacity">
                </div>
                @endforeach
            </div>
            @endif
            {{-- Video --}}
            @if(!empty($p->item_video_path))
            <div class="px-4 mt-3">
                <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/'.$p->item_video_path) }}', type: 'video' }"
                     class="relative rounded-2xl overflow-hidden cursor-pointer group" style="background:#111; aspect-ratio:16/9; max-height:170px;">
                    <video src="{{ asset('storage/'.$p->item_video_path) }}" class="w-full h-full object-contain" preload="metadata" muted></video>
                    <div class="absolute inset-0 flex items-center justify-center" style="background:rgba(0,0,0,0.32);">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform" style="background:rgba(255,255,255,0.92);">
                            <svg class="w-5 h-5 ml-0.5" style="color:var(--green)" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-mony-muted mt-1.5 text-center">Video barang — klik untuk putar</p>
            </div>
            @endif
            {{-- Reference --}}
            <div class="mx-4 mt-4 py-2.5 rounded-xl text-center border-2 border-dashed" style="border-color:var(--green);">
                <p class="text-xs text-mony-muted mb-0.5">Nomor Referensi</p>
                <p class="font-bold text-sm tracking-wide" style="color:var(--green);">{{ $p->ref_number }}</p>
            </div>
            {{-- Data Nasabah --}}
            @include('anggota.gadai._popup_nasabah', ['user' => $user])
            {{-- Detail pengajuan --}}
            <div class="mx-4 mt-3 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                    <p class="text-xs font-semibold text-mony-text">Detail Pengajuan</p>
                </div>
                @php $rows = [['Jenis Barang',$p->jenisBarang?->name??'-'],['Merk / Tipe',$p->brand_name??'-'],['Nilai Taksiran','Rp '.number_format($p->estimated_value,0,',','.')],['Ajuan Pinjaman','Rp '.number_format($p->loan_request_amount,0,',','.')],['Status','Menunggu Review'],['Tanggal Pengajuan',$p->submitted_at?->format('d M Y')??'-']]; @endphp
                @foreach($rows as $i=>[$label,$val])
                <div class="flex justify-between items-center px-4 py-2 text-xs {{ $i < count($rows)-1 ? 'border-b' : '' }}" style="{{ $i < count($rows)-1 ? 'border-color:#f0f7ee;' : '' }}">
                    <span class="text-mony-muted">{{ $label }}</span>
                    <span class="font-semibold text-mony-text text-right max-w-44">{{ $val }}</span>
                </div>
                @endforeach
            </div>
            {{-- Progress --}}
            <div class="mx-4 mt-3 mb-4 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                    <p class="text-xs font-semibold text-mony-text">Progres Pengajuan</p>
                </div>
                <div class="px-4 py-3 space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" style="background:var(--green);">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div class="pb-3 flex-1 border-b border-gray-100">
                            <p class="text-xs font-semibold text-mony-text">Pengajuan diterima sistem</p>
                            <p class="text-xs text-mony-muted">{{ $p->submitted_at?->format('d M Y - H:i') }} WIB</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 border-2" style="border-color:var(--green); background:var(--green-light);">
                            <svg class="w-3.5 h-3.5" style="color:var(--green);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="pb-3 flex-1 border-b border-gray-100">
                            <p class="text-xs font-semibold" style="color:var(--green);">Pengajuan sedang diperiksa pengurus</p>
                            <p class="text-xs text-mony-muted">Proses verifikasi kurang lebih 1×24 jam</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 opacity-35">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 bg-gray-200"></div>
                        <div>
                            <p class="text-xs font-semibold text-mony-text">Keputusan pengurus</p>
                            <p class="text-xs text-mony-muted">Proses verifikasi kurang lebih 1×24 jam</p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Cancel --}}
            <div class="px-4 pb-5">
                <form action="{{ route('anggota.gadai.pengajuan.cancel', $p) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-outline w-full justify-center border-red-200 hover:bg-red-50" style="color:#dc2626">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Batalkan Pengajuan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@empty
<div class="card p-12 text-center">
    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    <p class="text-sm text-mony-muted mb-4">Belum ada pengajuan yang sedang diproses</p>
    <a href="{{ route('anggota.gadai.pengajuan.create') }}" class="btn-primary btn-sm">Ajukan Sekarang</a>
</div>
@endforelse
@endif

{{-- ══════════════════════════════════ --}}
{{-- VERIFIKASI TAB                    --}}
{{-- ══════════════════════════════════ --}}
@if($tab === 'verifikasi')
@forelse($pengajuanVerifikasi as $p)
@php
    $isDiterima = $p->status === 'diterima';
    $thumb  = !empty($p->item_photo_paths[0]) ? asset('storage/'.$p->item_photo_paths[0]) : null;
    $photos = $p->item_photo_paths ?? [];
@endphp
<div x-data="{ open: false }" class="mb-3">
    {{-- Card --}}
    <div @click="open = true" class="card p-4 flex items-start gap-3 cursor-pointer hover:shadow-md transition-shadow active:scale-[0.99]">
        <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-100">
            @if($thumb)<img src="{{ $thumb }}" class="w-full h-full object-cover">
            @else<div class="w-full h-full flex items-center justify-center" style="background:var(--green-light)"><svg class="w-7 h-7" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>@endif
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2 mb-1">
                <div>
                    <p class="font-semibold text-sm text-mony-text">{{ $p->jenisBarang?->name }}</p>
                    <p class="text-xs text-mony-muted">{{ $p->brand_name ?? '-' }}</p>
                </div>
                <span class="badge-{{ $p->status_color }} text-xs flex-shrink-0">{{ $p->status_label }}</span>
            </div>
            <div class="flex items-end justify-between mt-2">
                <div>
                    <p class="text-xs text-mony-muted">Ajuan Pinjaman</p>
                    <p class="text-sm font-bold" style="color:var(--green)">Rp {{ number_format($p->loan_request_amount, 0, ',', '.') }}</p>
                </div>
                <p class="text-xs text-mony-muted">{{ $p->processed_at?->format('d M Y') }}</p>
            </div>
            @if(!$isDiterima && $p->reason_if_rejected)
            <p class="text-xs text-red-500 mt-1 truncate">Alasan: {{ $p->reason_if_rejected }}</p>
            @endif
        </div>
        <svg class="w-4 h-4 text-gray-300 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </div>

    {{-- Popup: Verifikasi --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @click.self="open = false" @keydown.escape.window="if(!$store.lb.show){ open = false }"
         style="background:rgba(8,20,12,0.72); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);"
         x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
<div class="bg-white popup-sheet w-full shadow-2xl scrollbar-hide" style="max-width:620px; max-height:88vh; overflow-y:auto; border-radius:24px;"
             @click.stop x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            {{-- Header --}}
            <div class="relative overflow-hidden rounded-t-3xl px-6 py-5"
                 style="background:{{ $isDiterima ? 'linear-gradient(135deg,var(--green) 0%,var(--green2) 100%)' : 'linear-gradient(135deg,#dc2626 0%,#991b1b 100%)' }};">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:rgba(255,255,255,0.15);">
                            @if($isDiterima)
                            <img src="{{ asset('images/logo-mony.png') }}" alt="MONY" class="w-7 h-7 object-contain">
                            @else
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white leading-tight">{{ $isDiterima ? 'Pengajuan Diterima!' : 'Pengajuan Ditolak!' }}</h2>
                            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6);">Diproses {{ $p->processed_at?->format('d M Y') }}</p>
                        </div>
                    </div>
                    <button @click="open = false" class="w-8 h-8 flex items-center justify-center rounded-full flex-shrink-0 transition-colors" style="background:rgba(255,255,255,0.15);" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            @if(count($photos))
            <div class="grid grid-cols-3 gap-2 px-4 mt-4">
                @foreach(array_slice($photos, 0, 3) as $photo)
                <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/'.$photo) }}', type: 'image' }"
                     class="rounded-xl overflow-hidden cursor-pointer group" style="aspect-ratio:1">
                    <img src="{{ asset('storage/'.$photo) }}" class="w-full h-full object-cover group-hover:opacity-85 transition-opacity">
                </div>
                @endforeach
            </div>
            @endif
            @if(!empty($p->item_video_path))
            <div class="px-4 mt-3">
                <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/'.$p->item_video_path) }}', type: 'video' }"
                     class="relative rounded-2xl overflow-hidden cursor-pointer group" style="background:#111; aspect-ratio:16/9; max-height:170px;">
                    <video src="{{ asset('storage/'.$p->item_video_path) }}" class="w-full h-full object-contain" preload="metadata" muted></video>
                    <div class="absolute inset-0 flex items-center justify-center" style="background:rgba(0,0,0,0.32);">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform" style="background:rgba(255,255,255,0.92);">
                            <svg class="w-5 h-5 ml-0.5" style="color:var(--green)" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-mony-muted mt-1.5 text-center">Video barang — klik untuk putar</p>
            </div>
            @endif
            <div class="mx-4 mt-4 py-2.5 rounded-xl text-center border-2 border-dashed" style="border-color:var(--green);">
                <p class="text-xs text-mony-muted mb-0.5">Nomor Referensi</p>
                <p class="font-bold text-sm tracking-wide" style="color:var(--green);">{{ $p->ref_number }}</p>
            </div>
            @include('anggota.gadai._popup_nasabah', ['user' => $user])
            {{-- Detail --}}
            <div class="mx-4 mt-3 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;"><p class="text-xs font-semibold text-mony-text">Detail Pengajuan</p></div>
                @php $rows2 = [['Jenis Barang',$p->jenisBarang?->name??'-'],['Merk / Tipe',$p->brand_name??'-'],['Nilai Taksiran','Rp '.number_format($p->estimated_value,0,',','.')],['Ajuan Pinjaman','Rp '.number_format($p->loan_request_amount,0,',','.')],['Status',$p->status_label],['Tanggal Pengajuan',$p->submitted_at?->format('d M Y')??'-']]; @endphp
                @foreach($rows2 as $i=>[$label,$val])
                <div class="flex justify-between items-center px-4 py-2 text-xs {{ $i < count($rows2)-1 ? 'border-b' : '' }}" style="{{ $i < count($rows2)-1 ? 'border-color:#f0f7ee;' : '' }}">
                    <span class="text-mony-muted">{{ $label }}</span>
                    <span class="font-semibold text-mony-text text-right max-w-44">{{ $val }}</span>
                </div>
                @endforeach
            </div>
            {{-- Petugas --}}
            @if($p->processor)
            <div class="mx-4 mt-3 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;"><p class="text-xs font-semibold text-mony-text">Petugas Verifikasi</p></div>
                <div class="flex justify-between px-4 py-2.5 text-xs">
                    <span class="text-mony-muted">Nama Petugas</span>
                    <span class="font-semibold text-mony-text">{{ $p->processor->name }}</span>
                </div>
            </div>
            @endif
            {{-- Progress timeline --}}
            <div class="mx-4 mt-3 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;"><p class="text-xs font-semibold text-mony-text">Progres Pengajuan</p></div>
                <div class="px-4 py-3 space-y-3">
                    @php
                        $timeline = [
                            ['Pengajuan diterima sistem', ($p->submitted_at?->format('d M Y - H:i') ?? '-').' WIB', true, false],
                            ['Pengajuan telah diperiksa pengurus', ($p->processed_at?->format('d M Y - H:i') ?? '-').' WIB', true, false],
                            [$isDiterima ? 'Pengajuan diterima — bawa barang ke koperasi' : 'Pengajuan Ditolak!', ($p->processed_at?->format('d M Y - H:i') ?? '-').' WIB', true, !$isDiterima],
                        ];
                        if ($isDiterima) {
                            $timeline[] = ['Penilaian barang & transaksi aktif', 'Menunggu barang dibawa ke koperasi', false, false];
                        }
                    @endphp
                    @foreach($timeline as $i => [$step, $time, $done, $rejected])
                    @php $isLast = $i === count($timeline) - 1; $isPending = !$done; @endphp
                    <div class="flex items-start gap-3 {{ $isPending ? 'opacity-40' : '' }}">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 {{ $rejected ? 'bg-red-500' : ($isPending ? 'bg-gray-200' : '') }}" style="{{ !$rejected && !$isPending ? 'background:var(--green);' : '' }}">
                            @if($rejected)
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            @elseif($isPending)
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @else
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </div>
                        <div class="{{ !$isLast ? 'pb-3 border-b border-gray-100 flex-1' : 'flex-1' }}">
                            <p class="text-xs font-semibold {{ $rejected ? 'text-red-600' : 'text-mony-text' }}">{{ $step }}</p>
                            <p class="text-xs text-mony-muted">{{ $time }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            {{-- Alasan penolakan / Langkah selanjutnya --}}
            @if(!$isDiterima && $p->reason_if_rejected)
            <div class="mx-4 mt-3 mb-4 p-4 rounded-2xl border border-red-200 bg-red-50">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-xs font-bold text-red-700">Alasan Penolakan</p>
                </div>
                <p class="text-xs text-red-700">{{ $p->reason_if_rejected }}</p>
            </div>
            @elseif($isDiterima)
            <div class="mx-4 mt-3 mb-4 p-4 rounded-2xl" style="background:var(--green-light); border:1px solid #ddebd5;">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 flex-shrink-0" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs font-bold" style="color:var(--green)">Langkah Selanjutnya</p>
                </div>
                <ol class="text-xs space-y-1" style="color:var(--green2); padding-left:1rem; list-style:decimal;">
                    <li>Silakan <strong>membawa barang jaminan ke kantor koperasi</strong> untuk dilakukan proses pengecekan dan penilaian (taksiran) secara langsung.</li>
                    <li>Barang akan <strong>diperiksa</strong> untuk menentukan nilai taksiran.</li>
                    <li>Hasil penilaian akan menjadi dasar penentuan jumlah pinjaman yang disetujui.</li>
                    <li>Setelah disetujui, <strong>proses pencairan dana</strong> akan segera dilakukan.</li>
                </ol>
            </div>
            @else
            <div class="pb-4"></div>
            @endif
        </div>
    </div>
</div>
@empty
<div class="card p-12 text-center">
    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm text-mony-muted">Belum ada pengajuan yang diverifikasi</p>
</div>
@endforelse
@endif

{{-- ══════════════════════════════════ --}}
{{-- AKTIF TAB                         --}}
{{-- ══════════════════════════════════ --}}
@if($tab === 'aktif')
@forelse($transaksiAktif as $t)
@php
    $tPhotos = !empty($t->pengajuan->item_photo_paths) ? $t->pengajuan->item_photo_paths : ($t->item_photo_paths ?? []);
    $tThumb  = !empty($tPhotos[0]) ? asset('storage/'.$tPhotos[0]) : null;
@endphp
<div x-data="{ open: false }" class="mb-3">
    {{-- Card --}}
    <div @click="open = true" class="card p-4 flex items-start gap-3 cursor-pointer hover:shadow-md transition-shadow {{ $t->isOverdue() ? 'border-l-4' : '' }}" style="{{ $t->isOverdue() ? 'border-left-color:#ef4444;' : '' }}">
        <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-100">
            @if($tThumb)<img src="{{ $tThumb }}" class="w-full h-full object-cover">
            @else<div class="w-full h-full flex items-center justify-center" style="background:var(--green-light)"><svg class="w-7 h-7" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>@endif
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2 mb-1">
                <div>
                    <p class="font-semibold text-sm text-mony-text">{{ $t->jenisBarang?->name }}</p>
                    <p class="text-xs text-mony-muted font-mono">{{ $t->reference_number }}</p>
                </div>
                <span class="badge-{{ $t->status_color }} text-xs flex-shrink-0">{{ $t->status_label }}</span>
            </div>
            <div class="flex items-end justify-between mt-2">
                <div>
                    <p class="text-xs text-mony-muted">Pinjaman</p>
                    <p class="text-sm font-bold" style="color:var(--green)">Rp {{ number_format($t->loan_amount, 0, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs {{ $t->isOverdue() ? 'text-red-500 font-semibold' : 'text-mony-muted' }}">
                        JT: {{ $t->due_date->format('d M Y') }}
                        @if($t->isOverdue())<span class="text-red-500"> (!)</span>@elseif($t->status==='aktif')<span class="text-mony-muted"> ({{ $t->daysUntilDue() }}h)</span>@endif
                    </p>
                </div>
            </div>
        </div>
        <svg class="w-4 h-4 text-gray-300 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </div>

    @include('anggota.gadai._popup_nota', ['t' => $t])
</div>
@empty
<div class="card p-12 text-center">
    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
    <p class="text-sm text-mony-muted mb-4">Belum ada gadai aktif</p>
    <a href="{{ route('anggota.gadai.pengajuan.create') }}" class="btn-primary btn-sm">Ajukan Sekarang</a>
</div>
@endforelse
@endif

{{-- ══════════════════════════════════ --}}
{{-- SELESAI TAB                       --}}
{{-- ══════════════════════════════════ --}}
@if($tab === 'selesai')
@forelse($transaksiSelesai as $t)
@php
    $isLelang  = $t->status === 'dilelang';
    $tPhotos   = !empty($t->pengajuan->item_photo_paths) ? $t->pengajuan->item_photo_paths : ($t->item_photo_paths ?? []);
    $tThumb    = !empty($tPhotos[0]) ? asset('storage/'.$tPhotos[0]) : null;
    $tMonths   = (int) $t->pawn_date->diffInMonths($t->due_date);
    $totalBunga= $t->pembayaran->where('payment_type','bunga')->where('status','confirmed')->sum('amount');
    $lelang    = $t->lelang;
@endphp
<div x-data="{ open: false }" class="mb-3">
    {{-- Card --}}
    <div @click="open = true" class="card p-4 flex items-start gap-3 cursor-pointer hover:shadow-md transition-shadow">
        <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-100">
            @if($tThumb)<img src="{{ $tThumb }}" class="w-full h-full object-cover">
            @else<div class="w-full h-full flex items-center justify-center" style="background:var(--green-light)"><svg class="w-7 h-7" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>@endif
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2 mb-1">
                <div>
                    <p class="font-semibold text-sm text-mony-text">{{ $t->jenisBarang?->name }}</p>
                    <p class="text-xs text-mony-muted font-mono">{{ $t->reference_number }}</p>
                </div>
                <span class="badge-{{ $t->status_color }} text-xs flex-shrink-0">{{ $t->status_label }}</span>
            </div>
            <div class="flex items-end justify-between mt-2">
                <div>
                    <p class="text-xs text-mony-muted">Pinjaman</p>
                    <p class="text-sm font-bold" style="color:var(--green)">Rp {{ number_format($t->loan_amount, 0, ',', '.') }}</p>
                </div>
                <p class="text-xs text-mony-muted">{{ $t->due_date->format('d M Y') }}</p>
            </div>
        </div>
        <svg class="w-4 h-4 text-gray-300 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </div>

    {{-- Popup: Ditebus / Dilelang --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @click.self="open = false" @keydown.escape.window="if(!$store.lb.show){ open = false }"
         style="background:rgba(8,20,12,0.72); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);"
         x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
<div class="bg-white popup-sheet w-full shadow-2xl scrollbar-hide" style="max-width:620px; max-height:88vh; overflow-y:auto; border-radius:24px;"
             @click.stop x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            {{-- Header --}}
            <div class="relative overflow-hidden rounded-t-3xl px-6 py-5"
                 style="background:{{ $isLelang ? 'linear-gradient(135deg,#b45309 0%,#92400e 100%)' : 'linear-gradient(135deg,var(--green) 0%,var(--green2) 100%)' }};">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:rgba(255,255,255,0.15);">
                            @if($isLelang)
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            @else
                            <img src="{{ asset('images/logo-mony.png') }}" alt="MONY" class="w-7 h-7 object-contain">
                            @endif
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white leading-tight">{{ $isLelang ? 'Barang Dilelang!' : 'Barang Ditebus!' }}</h2>
                            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6);">{{ $isLelang ? 'Proses lelang selesai' : 'Semua kewajiban telah diselesaikan' }}</p>
                        </div>
                    </div>
                    <button @click="open = false" class="w-8 h-8 flex items-center justify-center rounded-full flex-shrink-0 transition-colors" style="background:rgba(255,255,255,0.15);" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            @if(count($tPhotos))
            <div class="grid grid-cols-3 gap-2 px-4 mt-4">
                @foreach(array_slice($tPhotos, 0, 3) as $photo)
                <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/'.$photo) }}', type: 'image' }"
                     class="rounded-xl overflow-hidden cursor-pointer group" style="aspect-ratio:1">
                    <img src="{{ asset('storage/'.$photo) }}" class="w-full h-full object-cover group-hover:opacity-85 transition-opacity">
                </div>
                @endforeach
            </div>
            @endif
            @if(!empty($t->pengajuan?->item_video_path))
            <div class="px-4 mt-3">
                <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/'.$t->pengajuan->item_video_path) }}', type: 'video' }"
                     class="relative rounded-2xl overflow-hidden cursor-pointer group" style="background:#111; aspect-ratio:16/9; max-height:170px;">
                    <video src="{{ asset('storage/'.$t->pengajuan->item_video_path) }}" class="w-full h-full object-contain" preload="metadata" muted></video>
                    <div class="absolute inset-0 flex items-center justify-center" style="background:rgba(0,0,0,0.32);">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform" style="background:rgba(255,255,255,0.92);">
                            <svg class="w-5 h-5 ml-0.5" style="color:var(--green)" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-mony-muted mt-1.5 text-center">Video barang — klik untuk putar</p>
            </div>
            @endif
            <div class="mx-4 mt-4 py-2.5 rounded-xl text-center border-2 border-dashed" style="border-color:var(--green);">
                <p class="text-xs text-mony-muted mb-0.5">Nomor Referensi</p>
                <p class="font-bold text-sm tracking-wide" style="color:var(--green);">{{ $t->reference_number }}</p>
            </div>
            {{-- Banner --}}
            @if($isLelang)
            <div class="mx-4 mt-3 p-3 rounded-2xl border border-red-200 bg-red-50">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-xs font-bold text-red-700">Barang telah Dilelang!</p>
                        <p class="text-xs text-red-600 mt-0.5">Barang tidak dibayar bunganya selama 4 bulan berturut-turut sehingga diproses lelang.@if($lelang) Proses lelang selesai pada {{ $lelang->auction_date?->format('d M Y') }}.@endif</p>
                    </div>
                </div>
            </div>
            @else
            <div class="mx-4 mt-3 p-3 rounded-2xl" style="background:var(--green-light); border:1px solid #ddebd5;">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <div>
                        <p class="text-xs font-bold" style="color:var(--green)">Barang berhasil Ditebus!</p>
                        <p class="text-xs mt-0.5" style="color:var(--green2)">Seluruh kewajiban telah diselesaikan. Barang dapat diambil di kantor koperasi dengan menunjukkan nota ini.</p>
                    </div>
                </div>
            </div>
            @endif
            {{-- Detail barang --}}
            <div class="mx-4 mt-3 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;"><p class="text-xs font-semibold text-mony-text">Detail Barang Jaminan</p></div>
                @php $tRows2 = [['Jenis Barang',$t->jenisBarang?->name??'-'],['Merk Barang',$t->pengajuan?->brand_name??$t->item_description??'-'],['Nilai Taksiran','Rp '.number_format($t->appraisal_value,0,',','.')],['Nilai Pinjaman','Rp '.number_format($t->loan_amount,0,',','.')],['Bunga/bln','Rp '.number_format($t->monthlyInterest(),0,',','.')],['Mulai Gadai',$t->pawn_date->format('d M Y')],['Durasi Gadai',$tMonths.' bulan'],[$isLelang ? 'Tanggal Lelang' : 'Tanggal Penebusan',$t->due_date->format('d M Y')]]; @endphp
                @foreach($tRows2 as $i=>[$label,$val])
                <div class="flex justify-between items-center px-4 py-2 text-xs {{ $i < count($tRows2)-1 ? 'border-b' : '' }}" style="{{ $i < count($tRows2)-1 ? 'border-color:#f0f7ee;' : '' }}">
                    <span class="text-mony-muted">{{ $label }}</span>
                    <span class="font-semibold text-mony-text">{{ $val }}</span>
                </div>
                @endforeach
            </div>
            {{-- Ringkasan / Hasil lelang --}}
            @if($isLelang && $lelang)
            <div class="mx-4 mt-3 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;"><p class="text-xs font-semibold text-mony-text">Hasil Lelang</p></div>
                @php
                    $selisih = ($lelang->auction_value ?? 0) - $t->loan_amount;
                @endphp
                @php
                    $lelangRows = [
                        ['Tanggal Lelang', $lelang->auction_date?->format('d M Y') ?? '-'],
                        ['Harga Jual Lelang', 'Rp '.number_format($lelang->auction_value ?? 0, 0, ',', '.')],
                        ['Pokok Pinjaman', 'Rp '.number_format($t->loan_amount, 0, ',', '.')],
                        ['Kekurangan / Lebih', ($selisih >= 0 ? '+' : '').('Rp '.number_format($selisih, 0, ',', '.'))],
                    ];
                @endphp
                @foreach($lelangRows as $i => [$label, $val])
                <div class="flex justify-between items-center px-4 py-2 text-xs {{ $i < 3 ? 'border-b' : '' }}" style="{{ $i < 3 ? 'border-color:#f0f7ee;' : '' }}">
                    <span class="text-mony-muted">{{ $label }}</span>
                    <span class="font-bold {{ $selisih < 0 && $i === 3 ? 'text-red-500' : 'text-mony-text' }}">{{ $val }}</span>
                </div>
                @endforeach
            </div>
            @if($selisih < 0)
            <div class="mx-4 mt-3 p-3 rounded-2xl border border-red-200 bg-red-50">
                <p class="text-xs text-red-700">Jika hasil lelang tidak mencukupi untuk menutup seluruh kewajiban, nasabah wajib membayar kekurangan tersebut ke koperasi atau dipotong dari simpanan nasabah.</p>
            </div>
            @endif
            @else
            {{-- Riwayat pembayaran (ditebus) --}}
            <div class="mx-4 mt-3 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;"><p class="text-xs font-semibold text-mony-text">Ringkasan Pinjaman</p></div>
                @php
                    $ringkasanRows = [
                        ['Jumlah Pinjaman', 'Rp '.number_format($t->loan_amount, 0, ',', '.')],
                        ['Total Bunga Dibayar', 'Rp '.number_format($totalBunga, 0, ',', '.')],
                        ['Total Biaya', 'Rp '.number_format($t->loan_amount + $totalBunga, 0, ',', '.')],
                    ];
                @endphp
                @foreach($ringkasanRows as $i => [$label, $val])
                <div class="flex justify-between items-center px-4 py-2 text-xs {{ $i < 2 ? 'border-b' : '' }}" style="{{ $i < 2 ? 'border-color:#f0f7ee;' : '' }}">
                    <span class="text-mony-muted">{{ $label }}</span>
                    <span class="font-bold {{ $i === 2 ? '' : 'text-mony-text' }}" style="{{ $i === 2 ? 'color:var(--green);' : '' }}">{{ $val }}</span>
                </div>
                @endforeach
            </div>
            {{-- Ketentuan Pengambilan --}}
            <div class="mx-4 mt-3 p-4 rounded-2xl" style="background:var(--green-light); border:1px solid #ddebd5;">
                <div class="flex items-center gap-2 mb-2"><svg class="w-4 h-4 flex-shrink-0" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><p class="text-xs font-bold" style="color:var(--green)">Ketentuan Pengambilan Barang</p></div>
                <ol class="text-xs space-y-1" style="color:var(--green2); padding-left:1rem; list-style:decimal;">
                    <li>Datang ke kantor KSP Cempaka pada <strong>jam operasional (Senin–Sabtu, 08.00–16.00 WIB)</strong>.</li>
                    <li>Tunjukkan <strong>nota digital</strong> atau nomor referensi <strong>{{ $t->reference_number }}</strong> kepada petugas.</li>
                    <li>Bawa <strong>KTP asli</strong> yang sesuai dengan data pendaftaran untuk verifikasi identitas.</li>
                    <li>Tanda tangani <strong>surat serah terima barang</strong> dan barang dapat langsung dibawa pulang.</li>
                </ol>
            </div>
            @endif
            {{-- Footer note --}}
            <div class="mx-4 mt-3 mb-5 text-center">
                <p class="text-xs text-mony-muted leading-relaxed">Nota ini diterbitkan secara digital oleh sistem Mony. Berlaku sebagai bukti transaksi yang sah.<br>Butuh bantuan? Hubungi pengurus koperasi.</p>
            </div>
        </div>
    </div>
</div>
@empty
<div class="card p-12 text-center">
    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm text-mony-muted">Belum ada gadai selesai</p>
</div>
@endforelse
@endif

@endsection