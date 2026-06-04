@extends('layouts.anggota')
@php
$title = 'Chat: ' . $user->name;
@endphp
@section('content')
<div class="flex items-center gap-4 mb-4">
    <a href="{{ route('anggota.pesan.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-secondary/10 flex items-center justify-center text-secondary font-bold">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <p class="font-medium text-sm">{{ $user->name }}</p>
            <p class="text-xs text-mony-muted">{{ ucfirst($user->role) }}</p>
        </div>
    </div>
</div>

<div class="card max-w-2xl">
    <div class="h-96 overflow-y-auto p-4 space-y-3" id="chatBox">
        @forelse($messages as $msg)
        <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-xs px-4 py-2 rounded-2xl text-sm
                {{ $msg->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-gray-100 text-mony-text' }}">
                {{ $msg->content }}
                <p class="text-xs {{ $msg->sender_id === auth()->id() ? 'text-white/70' : 'text-mony-muted' }} mt-1 text-right">{{ $msg->sent_at->format('H:i') }}</p>
            </div>
        </div>
        @empty
        <p class="text-center text-sm text-mony-muted py-8">Belum ada pesan. Mulai percakapan!</p>
        @endforelse
    </div>
    <div class="p-4 border-t border-gray-100">
        <form method="POST" action="{{ route('anggota.pesan.send', $user) }}" class="flex gap-3">
            @csrf
            <input type="text" name="content" class="form-input flex-1" placeholder="Ketik pesan..." required>
            <button class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </button>
        </form>
    </div>
</div>
<script>document.getElementById('chatBox').scrollTop = document.getElementById('chatBox').scrollHeight;</script>
@endsection
