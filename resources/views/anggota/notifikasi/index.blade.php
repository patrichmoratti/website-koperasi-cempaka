@extends('layouts.anggota')
@php
$title = 'Notifikasi';
@endphp
@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="page-title">Notifikasi</h1>
        @if($unreadCount > 0) <p class="text-sm text-mony-muted mt-1">{{ $unreadCount }} belum dibaca</p> @endif
    </div>
    @if($unreadCount > 0)
    <form method="POST" action="{{ route('anggota.notifikasi.read-all') }}">
        @csrf
        <button class="btn-outline btn-sm">Tandai Semua Dibaca</button>
    </form>
    @endif
</div>

<div class="card p-4 mb-4">
    <form method="GET" class="flex gap-3">
        <div>
            <select name="category" class="form-input" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach(['mendesak'=>'Mendesak','pengingat'=>'Pengingat','update'=>'Update'] as $k => $v)
                    <option value="{{ $k }}" @selected(request('category') === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>

@if($notifikasi->count())
    <div class="space-y-2">
        @foreach($notifikasi as $notif)
        <div class="card p-4 flex items-start gap-4 {{ !$notif->is_read ? 'border-l-4 border-primary bg-primary/2' : '' }}">
            <div class="w-3 h-3 rounded-full mt-1 flex-shrink-0
                {{ ['mendesak'=>'bg-red-500','pengingat'=>'bg-yellow-500','update'=>'bg-blue-500'][$notif->category] ?? 'bg-gray-400' }}"></div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="font-medium text-sm {{ !$notif->is_read ? 'text-mony-text' : 'text-mony-muted' }}">{{ $notif->title }}</p>
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
            @if(!$notif->is_read)
                <form method="POST" action="{{ route('anggota.notifikasi.read', $notif) }}">
                    @csrf
                    <button class="text-mony-muted hover:text-primary text-xs mt-1 flex-shrink-0" title="Tandai dibaca">✓</button>
                </form>
            @endif
        </div>
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
