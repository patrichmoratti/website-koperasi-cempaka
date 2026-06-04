@extends('layouts.admin')
@php
$title = 'Kirim Notifikasi';
@endphp
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="page-title">Kirim Notifikasi</h1>
</div>

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.notifikasi.send') }}"
          class="card p-6 space-y-5"
          x-data="{ target: 'all' }">
        @csrf

        <div>
            <label class="form-label">Target Penerima <span class="text-red-500">*</span></label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="target" value="all" x-model="target"
                           class="text-primary focus:ring-primary">
                    <span class="text-sm">Semua Anggota</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="target" value="specific" x-model="target"
                           class="text-primary focus:ring-primary">
                    <span class="text-sm">Anggota Tertentu</span>
                </label>
            </div>
        </div>

        <div x-show="target === 'specific'">
            <label class="form-label">Pilih Anggota</label>
            <select name="user_ids[]" class="form-input" multiple size="6">
                @foreach($anggota as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
            <p class="form-hint">Tahan Ctrl/Cmd untuk memilih beberapa anggota</p>
        </div>

        <div>
            <label class="form-label">Kategori <span class="text-red-500">*</span></label>
            <select name="category" class="form-input" required>
                <option value="update">Update</option>
                <option value="pengingat">Pengingat</option>
                <option value="mendesak">Mendesak</option>
            </select>
        </div>

        <div>
            <label class="form-label">Judul <span class="text-red-500">*</span></label>
            <input type="text" name="title" class="form-input @error('title') border-red-400 @enderror"
                   placeholder="Judul notifikasi..." required>
            @error('title') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="form-label">Pesan <span class="text-red-500">*</span></label>
            <textarea name="message" class="form-input @error('message') border-red-400 @enderror"
                      rows="4" placeholder="Isi pesan notifikasi..." required></textarea>
            @error('message') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primary" onclick="return confirm('Kirim notifikasi ini?')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            Kirim Notifikasi
        </button>
    </form>
</div>
@endsection
