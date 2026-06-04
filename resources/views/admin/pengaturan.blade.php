@extends('layouts.admin')
@php
$title = 'Pengaturan';
@endphp
@section('content')
<h1 class="page-title mb-6">Pengaturan Koperasi</h1>

<form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data"
      class="max-w-2xl space-y-4">
    @csrf @method('PUT')

    <div class="card p-6 space-y-4">
        <h3 class="section-title">Informasi Koperasi</h3>

        <div>
            <label class="form-label">Nama Koperasi</label>
            <input type="text" name="name" value="{{ old('name', $info->name) }}" class="form-input" required>
        </div>

        <div>
            <label class="form-label">Alamat</label>
            <textarea name="address" class="form-input" rows="2">{{ old('address', $info->address) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $info->phone) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $info->email) }}" class="form-input">
            </div>
        </div>

        <div>
            <label class="form-label">Visi</label>
            <textarea name="vision" class="form-input" rows="2">{{ old('vision', $info->vision) }}</textarea>
        </div>

        <div>
            <label class="form-label">Misi</label>
            <textarea name="mission" class="form-input" rows="3">{{ old('mission', $info->mission) }}</textarea>
        </div>
    </div>

    <div class="card p-6 space-y-4">
        <h3 class="section-title">Informasi Rekening Bank</h3>

        <div>
            <label class="form-label">Nama Bank</label>
            <input type="text" name="bank_name" value="{{ old('bank_name', $info->bank_name) }}" class="form-input">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Nomor Rekening</label>
                <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $info->bank_account_number) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Atas Nama</label>
                <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $info->bank_account_name) }}" class="form-input">
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="section-title mb-3">Syarat & Ketentuan</h3>
        <textarea name="terms_and_conditions" class="form-input font-mono text-xs" rows="10">{{ old('terms_and_conditions', $info->terms_and_conditions) }}</textarea>
    </div>

    <button type="submit" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Simpan Pengaturan
    </button>
</form>
@endsection
