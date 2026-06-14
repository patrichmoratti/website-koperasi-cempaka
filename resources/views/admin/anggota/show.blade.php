@extends('layouts.admin')
@php
$title = 'Detail Anggota: ' . $user->name;
@endphp
@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('admin.anggota.index') }}" class="btn-ghost btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
    <h1 class="page-title">Detail Anggota</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Profile Card --}}
    <div class="card p-6">
        <div class="flex flex-col items-center text-center mb-6">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-2xl mb-3">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h2 class="font-semibold text-mony-text">{{ $user->name }}</h2>
            <p class="text-sm text-mony-muted">{{ $user->email }}</p>
            @php $colors = ['pending'=>'warning','active'=>'success','rejected'=>'danger','suspended'=>'gray'] @endphp
            <span class="badge-{{ $colors[$user->account_status] ?? 'gray' }} mt-2">{{ ucfirst($user->account_status) }}</span>
        </div>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-mony-muted">NIK</span>
                <span class="font-mono">{{ $user->nik ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-mony-muted">No. HP</span>
                <span>{{ $user->phone ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-mony-muted">Bergabung</span>
                <span>{{ $user->created_at->format('d M Y') }}</span>
            </div>
        </div>

        <div class="mt-4">
            <p class="text-xs text-mony-muted mb-1">Alamat</p>
            <p class="text-sm">{{ $user->address ?? '-' }}</p>
        </div>

        <div class="mt-6 space-y-2">
            @if($user->account_status === 'pending')
                <form method="POST" action="{{ route('admin.anggota.activate', $user) }}">
                    @csrf
                    <button class="btn-success w-full">Aktivasi Akun</button>
                </form>
                <div x-data="{ show: false }">
                    <button @click="show = !show" class="btn-danger w-full">Tolak</button>
                    <div x-show="show" class="mt-2">
                        <form method="POST" action="{{ route('admin.anggota.reject', $user) }}">
                            @csrf
                            <textarea name="reason" class="form-input mb-2" rows="2" placeholder="Alasan penolakan..." required></textarea>
                            <button type="submit" class="btn-danger w-full btn-sm">Konfirmasi Tolak</button>
                        </form>
                    </div>
                </div>
            @endif

            @if($user->account_status === 'active')
                <form method="POST" action="{{ route('admin.anggota.suspend', $user) }}"
                      onsubmit="return confirmAction(event, 'Suspend akun ini?', 'Ya, Suspend')">
                    @csrf
                    <button class="btn-danger w-full">Suspend Akun</button>
                </form>
            @endif

            @if($user->account_status === 'suspended')
                <form method="POST" action="{{ route('admin.anggota.reactivate', $user) }}"
                      onsubmit="return confirmAction(event, 'Aktifkan kembali akun ini?', 'Ya, Aktifkan')">
                    @csrf
                    <button class="btn-success w-full">Aktifkan Kembali</button>
                </form>
            @endif

            @if($user->account_status !== 'pending')
                <form method="POST" action="{{ route('admin.anggota.reset-password', $user) }}"
                      onsubmit="return confirmAction(event, 'Reset password akun ini?', 'Ya, Reset')">
                    @csrf
                    <button class="btn-outline w-full">Reset Password</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Right panels --}}
    <div class="lg:col-span-2 space-y-4">
        {{-- KYC Photos --}}
        <div class="card p-5">
            <h3 class="section-title mb-4">Foto KYC</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-mony-muted mb-2">Foto KTP</p>
                    @if($user->ktp_photo)
                        <img src="{{ asset('storage/' . $user->ktp_photo) }}" alt="KTP"
                             class="w-full h-40 object-cover rounded-lg border border-gray-200 cursor-pointer"
                             onclick="this.classList.toggle('h-40')">
                    @else
                        <div class="w-full h-40 bg-gray-100 rounded-lg flex items-center justify-center text-mony-muted text-sm">Belum ada</div>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-mony-muted mb-2">Foto Selfie + KTP</p>
                    @if($user->selfie_photo)
                        <img src="{{ asset('storage/' . $user->selfie_photo) }}" alt="Selfie"
                             class="w-full h-40 object-cover rounded-lg border border-gray-200 cursor-pointer"
                             onclick="this.classList.toggle('h-40')">
                    @else
                        <div class="w-full h-40 bg-gray-100 rounded-lg flex items-center justify-center text-mony-muted text-sm">Belum ada</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Simpanan Summary --}}
        <div class="card p-5">
            <h3 class="section-title mb-3">Ringkasan Simpanan</h3>
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-gray-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-mony-muted">Pokok</p>
                    <p class="font-semibold text-sm">Rp {{ number_format($user->totalSimpananPokok(), 0, ',', '.') }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center">
                    <p class="text-xs text-mony-muted">Wajib</p>
                    <p class="font-semibold text-sm">Rp {{ number_format($user->totalSimpananWajib(), 0, ',', '.') }}</p>
                </div>
                <div class="bg-primary/5 rounded-xl p-3 text-center">
                    <p class="text-xs text-mony-muted">Total</p>
                    <p class="font-semibold text-sm text-primary">Rp {{ number_format($user->totalSimpanan(), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Gadai History --}}
        <div class="card p-5">
            <h3 class="section-title mb-3">Riwayat Gadai ({{ $user->transaksiGadai->count() }})</h3>
            @if($user->transaksiGadai->count())
                <div class="space-y-2">
                    @foreach($user->transaksiGadai->take(5) as $t)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium">{{ $t->jenisBarang?->name }}</p>
                            <p class="text-xs text-mony-muted">{{ $t->reference_number }} · {{ $t->pawn_date->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium">Rp {{ number_format($t->loan_amount, 0, ',', '.') }}</p>
                            @php $c = ['aktif'=>'success','ditebus'=>'info','dilelang'=>'danger','selesai'=>'gray'] @endphp
                            <span class="badge-{{ $c[$t->status] ?? 'gray' }} text-xs">{{ $t->status_label }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-mony-muted">Belum ada riwayat gadai</p>
            @endif
        </div>
    </div>
</div>
@endsection
