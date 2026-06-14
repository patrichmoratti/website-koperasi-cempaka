@extends('layouts.pengurus')
@php
$title = 'Riwayat Konfirmasi';
@endphp
@section('content')
<div x-data="{
    detailOpen: false,
    detailData: { title:'', subtitle:'', status:'pending', statusLabel:'', rows:[], note:null, link:null },
    openDetail(data) { this.detailData = data; this.detailOpen = true; }
}">

<div class="mb-6">
    <h1 class="text-2xl font-bold text-mony-text tracking-tight">Riwayat Konfirmasi</h1>
    <p class="text-sm text-mony-muted mt-1">Semua konfirmasi yang telah dilakukan pengurus</p>
</div>

{{-- Tab navigation --}}
<div class="flex gap-1 p-1 rounded-2xl mb-5" style="background: rgba(255,255,255,0.1);">
    @foreach(['semua'=>'Semua','registrasi'=>'Registrasi','pembayaran'=>'Pembayaran','pengajuan'=>'Pengajuan'] as $key => $label)
    @php
        $count = match($key) {
            'semua'      => $semua->count(),
            'registrasi' => $registrasi->count(),
            'pembayaran' => $pembayaran->count(),
            'pengajuan'  => $pengajuan->count(),
            default      => 0,
        };
    @endphp
    <a href="{{ route('pengurus.riwayat.index', ['tab' => $key]) }}"
       class="flex-1 text-center py-2 rounded-xl text-xs font-semibold transition-all inline-flex items-center justify-center gap-1.5"
       style="{{ $tab === $key ? 'background:white; color:var(--green); box-shadow:0 1px 6px rgba(0,0,0,0.15);' : 'color:rgba(255,255,255,0.55);' }}"
       onmouseover="{{ $tab !== $key ? "this.style.color='rgba(255,255,255,0.9)'" : '' }}"
       onmouseout="{{ $tab !== $key ? "this.style.color='rgba(255,255,255,0.55)'" : '' }}">
        <span>{{ $label }}</span>
        @if($count > 0)
        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center leading-none"
              style="{{ $tab === $key ? 'background:var(--green-light); color:var(--green);' : 'background:rgba(255,255,255,0.2); color:#fff;' }}">
            {{ $count }}
        </span>
        @endif
    </a>
    @endforeach
</div>

@php
$items = match($tab) {
    'registrasi' => $registrasi,
    'pembayaran' => $pembayaran,
    'pengajuan'  => $pengajuan,
    default      => $semua,
};

$typeIcons = [
    'registrasi' => ['bg' => 'bg-teal-50',   'text' => 'text-teal-600',   'path' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
    'pembayaran' => ['bg' => 'bg-amber-50',  'text' => 'text-amber-600',  'path' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    'pengajuan'  => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'path' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
];

$typeLabels = [
    'registrasi' => 'Registrasi',
    'pembayaran' => 'Pembayaran',
    'pengajuan'  => 'Pengajuan Gadai',
];
@endphp

@if($items->count())
    <div class="space-y-2">
        @foreach($items as $item)
        @php $icon = $typeIcons[$item->type] ?? $typeIcons['pengajuan']; @endphp
        <div @click="openDetail(@js($item->detail))"
             class="card p-4 flex items-center gap-4 cursor-pointer hover:shadow-card-hover transition-shadow">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $icon['bg'] }}">
                <svg class="w-5 h-5 {{ $icon['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon['path'] }}"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    <p class="font-medium text-sm text-mony-text truncate">{{ $item->name }}</p>
                    @if($tab === 'semua')
                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-gray-100 text-mony-muted flex-shrink-0">
                        {{ $typeLabels[$item->type] ?? $item->type }}
                    </span>
                    @endif
                </div>
                <p class="text-xs text-mony-muted">{{ $item->sub }} · {{ \Carbon\Carbon::parse($item->date)->format('d M Y, H:i') }}</p>
            </div>
            <div class="text-right flex-shrink-0">
                <span class="badge-{{ $item->badge }} text-xs">{{ $item->label }}</span>
            </div>
            <div class="flex-shrink-0 text-mony-muted">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </div>
        @endforeach
    </div>
@else
    <div class="card p-12 text-center text-mony-muted">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p>Belum ada riwayat konfirmasi</p>
    </div>
@endif

@include('partials.payment-detail-modal')

</div>{{-- end x-data --}}
@endsection