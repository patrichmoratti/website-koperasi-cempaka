@extends('layouts.pengurus')
@php
$title = 'Anggota';
@endphp
@section('content')
<div x-data="{ openModal: null, createOpen: false }" @keydown.escape.window="if(!$store.lb?.show){ openModal = null; createOpen = false; }">

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-mony-text tracking-tight">Daftar Anggota</h1>
    <button type="button" @click="createOpen = true" class="btn-primary btn-sm flex-shrink-0">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Anggota
    </button>
</div>

<div class="card p-4 mb-4">
    <form method="GET" class="flex gap-3">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Cari nama, NIK, email...">
        </div>
        <button type="submit" class="btn-primary">Cari</button>
    </form>
</div>

@include('pengurus.anggota._popup_create_anggota')

<div class="card overflow-hidden">
    <table class="table-base">
        <thead><tr><th>Nama</th><th>NIK</th><th>HP</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($anggota as $user)
            <tr @click="openModal = 'anggota-{{ $user->id }}'" class="cursor-pointer">
                <td>
                    <p class="font-medium">{{ $user->name }}</p>
                    <p class="text-xs text-mony-muted">{{ $user->email }}</p>
                </td>
                <td class="font-mono text-xs">{{ $user->nik ?? '-' }}</td>
                <td class="text-sm">{{ $user->phone ?? '-' }}</td>
                <td>
                    @php $colors = ['pending'=>'warning','active'=>'success','rejected'=>'danger','suspended'=>'gray'] @endphp
                    <span class="badge-{{ $colors[$user->account_status] ?? 'gray' }}">{{ ucfirst($user->account_status) }}</span>
                </td>
                <td @click.stop>
                    <button type="button" @click.stop="openModal = 'anggota-{{ $user->id }}'" class="btn-ghost btn-sm">Detail</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-8 text-mony-muted">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($anggota->hasPages()) <div class="px-4 py-3 border-t">{{ $anggota->links() }}</div> @endif
</div>

@foreach($anggota as $user)
    @include('pengurus.anggota._popup_anggota', ['user' => $user])
@endforeach

</div>{{-- end x-data --}}
@endsection