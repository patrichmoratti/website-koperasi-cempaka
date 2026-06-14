@extends('layouts.admin')
@php
$title = 'Manajemen Pengurus';
@endphp
@section('content')

<div x-data="{ openModal: null, createOpen: false }" @keydown.escape.window="if(!$store.lb?.show){ openModal = null; createOpen = false; }">

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-mony-text tracking-tight">Manajemen Pengurus</h1>
        <p class="text-sm mt-1 text-mony-muted">Kelola akun pengurus yang dapat mengakses sistem operasional koperasi</p>
    </div>
    <button type="button" @click="createOpen = true" class="btn-primary btn-sm flex-shrink-0">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Pengurus
    </button>
</div>

{{-- Stats strip --}}
<div class="card p-0 mb-4 overflow-hidden">
    <div class="grid grid-cols-2">
        <div class="flex items-center gap-3 p-4 border-r" style="border-color:#eef3ea">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:var(--green-light)">
                <svg class="w-4 h-4" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-mony-muted">Total Pengurus</p>
                <p class="text-xl font-bold text-mony-text">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 p-4">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#dcfce7">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-mony-muted">Akun Aktif</p>
                <p class="text-xl font-bold text-mony-text">{{ $stats['active'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="form-label">Cari</label>
            <input type="text" name="search" value="{{ $search }}"
                   class="form-input" placeholder="Nama atau email pengurus...">
        </div>
        <button type="submit" class="btn-primary">Cari</button>
        @if($search)
            <a href="{{ route('admin.pengurus.index') }}" class="btn-outline">Reset</a>
        @endif
    </form>
</div>

@include('admin.pengurus._popup_create')

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table-base">
            <thead>
                <tr>
                    <th>Pengurus</th>
                    <th>No. HP</th>
                    <th>Status</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengurus as $user)
                <tr @click="openModal = 'pengurus-{{ $user->id }}'" class="cursor-pointer">
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-sm flex-shrink-0"
                                 style="background: var(--green-light); color: var(--green)">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-sm">{{ $user->name }}</p>
                                <p class="text-xs text-mony-muted">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>
                        <span class="badge-{{ $user->account_status === 'active' ? 'success' : 'gray' }}">
                            {{ $user->account_status === 'active' ? 'Aktif' : 'Disuspend' }}
                        </span>
                    </td>
                    <td class="text-xs text-mony-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td @click.stop>
                        <div class="flex items-center gap-1">
                            <button type="button" @click.stop="openModal = 'pengurus-{{ $user->id }}'"
                                    class="btn-ghost btn-sm">Detail</button>

                            @if($user->account_status === 'active')
                            <form method="POST" action="{{ route('admin.pengurus.toggle-status', $user) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-danger btn-sm btn-icon" title="Suspend"
                                        onclick="return confirmAction(event, 'Suspend akun {{ addslashes($user->name) }}?', 'Ya, Suspend')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.pengurus.toggle-status', $user) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-success btn-sm btn-icon" title="Aktifkan"
                                        onclick="return confirmAction(event, 'Aktifkan akun {{ addslashes($user->name) }}?', 'Ya, Aktifkan')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-12 text-mony-muted">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-sm">
                            @if($search)
                                Tidak ada pengurus yang cocok dengan pencarian "{{ $search }}"
                            @else
                                Belum ada akun pengurus
                            @endif
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pengurus->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $pengurus->links() }}
        </div>
    @endif
</div>

@foreach($pengurus as $user)
    @include('admin.pengurus._popup_detail', ['user' => $user])
@endforeach

</div>{{-- end x-data --}}

@endsection