@extends('layouts.pengurus')
@php
$title = 'Anggota';
@endphp
@section('content')
<h1 class="page-title mb-6">Daftar Anggota</h1>

<div class="card p-4 mb-4">
    <form method="GET" class="flex gap-3">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Cari nama, NIK, email...">
        </div>
        <button type="submit" class="btn-primary">Cari</button>
    </form>
</div>

<div class="card overflow-hidden">
    <table class="table-base">
        <thead><tr><th>Nama</th><th>NIK</th><th>HP</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse($anggota as $user)
            <tr>
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
                <td><a href="{{ route('pengurus.anggota.show', $user) }}" class="btn-ghost btn-sm">Detail</a></td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-8 text-mony-muted">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($anggota->hasPages()) <div class="px-4 py-3 border-t">{{ $anggota->links() }}</div> @endif
</div>
@endsection
