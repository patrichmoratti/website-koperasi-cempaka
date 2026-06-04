@extends('layouts.admin')
@php
$title = 'Manajemen Anggota';
@endphp
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="page-title">Manajemen Anggota</h1>
        <p class="text-sm mt-1" style="color: var(--text-muted)">Kelola data dan status anggota koperasi</p>
    </div>
</div>

{{-- Filters --}}
<div class="card p-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="form-label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-input" placeholder="Nama, email, NIK...">
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">Semua Status</option>
                <option value="pending"   @if(request('status') === 'pending')   selected @endif>Pending</option>
                <option value="active"    @if(request('status') === 'active')    selected @endif>Aktif</option>
                <option value="rejected"  @if(request('status') === 'rejected')  selected @endif>Ditolak</option>
                <option value="suspended" @if(request('status') === 'suspended') selected @endif>Disuspend</option>
            </select>
        </div>
        <button type="submit" class="btn-primary">Cari</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.anggota.index') }}" class="btn-outline">Reset</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table-base">
            <thead>
                <tr>
                    <th>Anggota</th>
                    <th>NIK</th>
                    <th>No. HP</th>
                    <th>Status</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if($anggota->count() > 0)
                    @foreach($anggota as $user)
                    @php
                        $statusColor = ['pending'=>'warning','active'=>'success','rejected'=>'danger','suspended'=>'gray'];
                        $statusLabel = ['pending'=>'Pending','active'=>'Aktif','rejected'=>'Ditolak','suspended'=>'Disuspend'];
                        $color = $statusColor[$user->account_status] ?? 'gray';
                        $label = $statusLabel[$user->account_status] ?? $user->account_status;
                    @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-sm flex-shrink-0"
                                     style="background: var(--green-light); color: var(--green)">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-sm">{{ $user->name }}</p>
                                    <p class="text-xs" style="color: var(--text-muted)">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="font-mono text-xs">{{ $user->nik ?? '-' }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>
                            <span class="badge-{{ $color }}">{{ $label }}</span>
                        </td>
                        <td class="text-xs" style="color: var(--text-muted)">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.anggota.show', $user) }}"
                                   class="btn-ghost btn-sm btn-icon" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                @if($user->account_status === 'pending')
                                <form method="POST" action="{{ route('admin.anggota.activate', $user) }}">
                                    @csrf
                                    <button type="submit" class="btn-success btn-sm btn-icon" title="Aktifkan"
                                            onclick="return confirm('Aktifkan akun ini?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif

                                @if($user->account_status === 'active')
                                <form method="POST" action="{{ route('admin.anggota.suspend', $user) }}">
                                    @csrf
                                    <button type="submit" class="btn-danger btn-sm btn-icon" title="Suspend"
                                            onclick="return confirm('Suspend akun {{ addslashes($user->name) }}?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                <tr>
                    <td colspan="6" class="text-center py-12" style="color: var(--text-muted)">
                        <svg class="w-10 h-10 mx-auto mb-3" style="color: var(--green-light)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-sm">Tidak ada anggota ditemukan</p>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($anggota->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $anggota->links() }}
        </div>
    @endif
</div>

@endsection
