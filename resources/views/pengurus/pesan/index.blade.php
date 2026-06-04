@extends('layouts.pengurus')
@php
$title = 'Pesan';
@endphp
@section('content')
<h1 class="page-title mb-6">Pesan dari Anggota</h1>

@if($threads->isEmpty())
    <div class="card p-12 text-center text-mony-muted">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <p>Belum ada pesan masuk</p>
    </div>
@else
    <div class="card divide-y divide-gray-100 max-w-2xl">
        @foreach($threads as $thread)
        @php
            $partner = $thread->sender_id === auth()->id() ? $thread->receiver : $thread->sender;
        @endphp
        <a href="{{ route('pengurus.pesan.show', $partner) }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors">
            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-semibold flex-shrink-0">
                {{ strtoupper(substr($partner->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <p class="font-medium text-sm {{ !$thread->is_read && $thread->receiver_id === auth()->id() ? 'text-mony-text' : 'text-mony-muted' }}">{{ $partner->name }}</p>
                    <span class="text-xs text-mony-muted">{{ $thread->sent_at->diffForHumans() }}</span>
                </div>
                <p class="text-xs text-mony-muted truncate">{{ Str::limit($thread->content, 60) }}</p>
            </div>
            @if(!$thread->is_read && $thread->receiver_id === auth()->id())
                <span class="w-2 h-2 bg-primary rounded-full flex-shrink-0"></span>
            @endif
        </a>
        @endforeach
    </div>
@endif
@endsection
