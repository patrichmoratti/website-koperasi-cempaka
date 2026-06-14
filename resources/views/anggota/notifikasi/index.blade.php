@extends('layouts.anggota')
@php
$title = 'Notifikasi';

$icons = [
    'simpanan'    => ['bg' => 'bg-blue-50',   'text' => 'text-blue-600',   'path' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
    'pembayaran'  => ['bg' => 'bg-amber-50',  'text' => 'text-amber-600',  'path' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    'pengajuan'   => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'path' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
    'gadai'       => ['bg' => 'bg-green-50',  'text' => 'text-green-600',  'path' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
    'jatuh_tempo' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'path' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    'registrasi'  => ['bg' => 'bg-teal-50',   'text' => 'text-teal-600',   'path' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
    'default'     => ['bg' => 'bg-gray-100',  'text' => 'text-gray-500',   'path' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
];
@endphp
@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-mony-text tracking-tight">Notifikasi</h1>
        @if($unreadCount > 0) <p class="text-sm text-mony-muted mt-1">{{ $unreadCount }} belum dibaca</p> @endif
    </div>
    @if($unreadCount > 0)
    <form method="POST" action="{{ route('anggota.notifikasi.read-all') }}">
        @csrf
        <button class="btn-outline btn-sm">Tandai Semua Dibaca</button>
    </form>
    @endif
</div>

@if($notifikasi->count())
    <div class="space-y-2">
        @foreach($notifikasi as $notif)
        @php $icon = $icons[$notif->icon_key] ?? $icons['default']; @endphp
        <form method="POST" action="{{ route('anggota.notifikasi.read', $notif) }}"
              class="card p-4 flex items-start gap-4 transition-colors {{ !$notif->is_read ? 'border-l-4 border-primary bg-primary/2 cursor-pointer hover:bg-gray-50/60' : '' }}"
              @if(!$notif->is_read) onclick="this.requestSubmit()" @endif>
            @csrf
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $icon['bg'] }}">
                <svg class="w-5 h-5 {{ $icon['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon['path'] }}"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="font-medium text-sm {{ !$notif->is_read ? 'text-mony-text' : 'text-mony-muted' }}">
                            {{ $notif->title }}
                            @if(!$notif->is_read)<span class="inline-block w-2 h-2 rounded-full bg-primary ml-1.5 align-middle"></span>@endif
                        </p>
                        <p class="text-sm text-mony-muted mt-1">{{ $notif->message }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xs text-mony-muted">{{ $notif->created_at->diffForHumans() }}</p>
                        <span class="badge-{{ ['mendesak'=>'danger','pengingat'=>'warning','update'=>'info'][$notif->category] ?? 'gray' }} text-xs mt-1">
                            {{ $notif->category_label }}
                        </span>
                    </div>
                </div>
            </div>
        </form>
        @endforeach
    </div>
    @if($notifikasi->hasPages()) <div class="mt-4">{{ $notifikasi->links() }}</div> @endif
@else
    <div class="card p-12 text-center text-mony-muted">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <p>Tidak ada notifikasi</p>
    </div>
@endif
@endsection