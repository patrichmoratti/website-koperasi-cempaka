@extends('layouts.anggota')
@php
$title = 'Pesan';
@endphp
@section('content')
<h1 class="page-title mb-6">Pesan</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 max-w-4xl">
    <div class="card p-5">
        <h3 class="section-title mb-4">Hubungi Pengurus</h3>
        <div class="space-y-2">
            @foreach($pengurus as $p)
            <a href="{{ route('anggota.pesan.show', $p) }}"
               class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="w-9 h-9 rounded-full bg-secondary/10 flex items-center justify-center text-secondary font-semibold">
                    {{ strtoupper(substr($p->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-medium text-sm">{{ $p->name }}</p>
                    <p class="text-xs text-mony-muted">{{ ucfirst($p->role) }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <div class="lg:col-span-2 card p-5">
        <h3 class="section-title mb-4">Thread Percakapan</h3>
        @if($threads->count())
            <div class="space-y-2">
                @foreach($threads as $thread)
                @php $partner = $thread->partner @endphp
                <a href="{{ route('anggota.pesan.show', $partner) }}"
                   class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center text-secondary font-bold flex-shrink-0">
                        {{ strtoupper(substr($partner->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-sm">{{ $partner->name }}</p>
                        <p class="text-xs text-mony-muted truncate">{{ $thread->content }}</p>
                    </div>
                    <p class="text-xs text-mony-muted flex-shrink-0">{{ $thread->sent_at->diffForHumans() }}</p>
                </a>
                @endforeach
            </div>
        @else
            <p class="text-sm text-mony-muted">Pilih pengurus untuk memulai percakapan</p>
        @endif
    </div>
</div>
@endsection
