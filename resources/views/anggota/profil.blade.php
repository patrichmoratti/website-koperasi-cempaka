@extends('layouts.anggota')
@php
$title = 'Profil Saya';
@endphp
@section('content')
<h1 class="text-2xl font-bold text-mony-text tracking-tight mb-6">Profil Saya</h1>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    {{-- Edit Profil --}}
    <div class="card p-6">
        <h3 class="section-title mb-4">Informasi Profil</h3>
        <form method="POST" action="{{ route('anggota.profil.update') }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" value="{{ $user->email }}" class="form-input bg-gray-50" disabled>
            </div>
            <div>
                <label class="form-label">NIK</label>
                <input type="text" value="{{ $user->nik ?? '-' }}" class="form-input bg-gray-50 font-mono" disabled>
            </div>
            <div>
                <label class="form-label">No. HP</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" required>
                @error('phone') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-input" rows="2" required>{{ old('address', $user->address) }}</textarea>
                @error('address') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="space-y-4">
        <div class="card p-6">
            <h3 class="section-title mb-4">Ganti Password</h3>
            <form method="POST" action="{{ route('anggota.profil.password') }}" class="space-y-4" x-data="{ show: false }">
                @csrf @method('PUT')
                <div>
                    <label class="form-label">Password Saat Ini</label>
                    <input :type="show ? 'text' : 'password'" name="current_password" class="form-input" required>
                    @error('current_password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Password Baru</label>
                    <input :type="show ? 'text' : 'password'" name="password" class="form-input" required>
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input :type="show ? 'text' : 'password'" name="password_confirmation" class="form-input" required>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="show" class="rounded border-gray-300 text-primary">
                    <span class="text-xs text-mony-muted">Tampilkan password</span>
                </label>
                <button type="submit" class="btn-outline">Ganti Password</button>
            </form>
        </div>

        {{-- KYC Photos --}}
        <div class="card p-5">
            <h3 class="section-title mb-3">Foto KYC</h3>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <p class="text-xs text-mony-muted mb-1">KTP</p>
                    @if($user->ktp_photo)
                        <img src="{{ asset('storage/' . $user->ktp_photo) }}" alt="KTP" class="w-full h-24 object-cover rounded-lg border border-gray-200">
                    @else
                        <div class="w-full h-24 bg-gray-100 rounded-lg flex items-center justify-center text-xs text-mony-muted">Belum ada</div>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-mony-muted mb-1">Selfie</p>
                    @if($user->selfie_photo)
                        <img src="{{ asset('storage/' . $user->selfie_photo) }}" alt="Selfie" class="w-full h-24 object-cover rounded-lg border border-gray-200">
                    @else
                        <div class="w-full h-24 bg-gray-100 rounded-lg flex items-center justify-center text-xs text-mony-muted">Belum ada</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
