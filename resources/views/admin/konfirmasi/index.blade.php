@extends('layouts.admin')
@php
$title = 'Konfirmasi';
@endphp
@section('content')
<h1 class="page-title mb-6">Pusat Konfirmasi</h1>

{{-- Tab Nav --}}
<div class="flex gap-1 mb-4 bg-gray-100 p-1 rounded-xl w-fit flex-wrap">
    @foreach(['pembayaran_gadai'=>'Pembayaran Gadai','simpanan'=>'Simpanan','registrasi'=>'Registrasi'] as $key => $label)
        <a href="{{ route('admin.konfirmasi.index', ['tab' => $key]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $tab === $key ? 'bg-white shadow text-mony-text' : 'text-mony-muted hover:text-mony-text' }}">
            {{ $label }}
            @if($counts[$key] > 0)
                <span class="ml-1 badge-danger text-xs">{{ $counts[$key] }}</span>
            @endif
        </a>
    @endforeach
</div>

@if($tab === 'pembayaran_gadai')
    <div x-data="{ openModal: null }">
    <div class="card overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="section-title">Pembayaran Gadai Pending ({{ $counts['pembayaran_gadai'] }})</h3>
        </div>
        <table class="table-base">
            <thead><tr>
                <th>Anggota</th><th>No. Ref Gadai</th><th>Tipe</th>
                <th>Jumlah</th><th>Waktu</th><th>Bukti</th><th>Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($pembayaranGadai as $p)
                <tr @click="openModal = 'pembayaran-{{ $p->id }}'" class="cursor-pointer">
                    <td class="font-medium">{{ $p->transaksi?->anggota?->name }}</td>
                    <td class="font-mono text-xs">{{ $p->transaksi?->reference_number }}</td>
                    <td><span class="badge-{{ $p->payment_type === 'tebus' ? 'info' : 'success' }}">{{ $p->payment_type_label }}</span></td>
                    <td class="font-medium">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                    <td class="text-xs text-mony-muted">{{ $p->submitted_at->diffForHumans() }}</td>
                    <td @click.stop>
                        @if($p->transfer_proof_path)
                            <button type="button" @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $p->transfer_proof_path) }}', type: 'image' }" class="text-primary text-xs hover:underline">Lihat</button>
                        @else <span class="text-mony-muted text-xs">-</span>
                        @endif
                    </td>
                    <td @click.stop>
                        <div class="flex gap-1">
                            <form method="POST" action="{{ route('admin.konfirmasi.pembayaran.confirm', $p) }}">
                                @csrf
                                <button class="btn-success btn-sm" onclick="return confirmAction(event, 'Konfirmasi pembayaran ini?', 'Ya, Konfirmasi')">Konfirmasi</button>
                            </form>
                            <button type="button" class="btn-danger btn-sm"
                                    onclick="openRejectModal('Alasan penolakan pembayaran {{ addslashes($p->transaksi?->anggota?->name) }}', '{{ route('admin.konfirmasi.pembayaran.reject', $p) }}')">Tolak</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-mony-muted">Tidak ada pembayaran pending</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($pembayaranGadai->hasPages())
            <div class="px-4 py-3 border-t">{{ $pembayaranGadai->links() }}</div>
        @endif
    </div>
    @foreach($pembayaranGadai as $p)
        @include('partials.konfirmasi-modal', ['type' => 'pembayaran', 'item' => $p, 'routePrefix' => 'admin'])
    @endforeach
    </div>

@elseif($tab === 'simpanan')
    <div x-data="{ openModal: null }">
    <div class="card overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="section-title">Simpanan Pending ({{ $counts['simpanan'] }})</h3>
        </div>
        <table class="table-base">
            <thead><tr>
                <th>Anggota</th><th>Tipe</th><th>Periode</th>
                <th>Jumlah</th><th>Waktu</th><th>Bukti</th><th>Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($simpanan as $s)
                <tr @click="openModal = 'simpanan-{{ $s->id }}'" class="cursor-pointer">
                    <td class="font-medium">{{ $s->anggota?->name }}</td>
                    <td><span class="badge-{{ $s->type === 'pokok' ? 'info' : 'primary' }}">{{ $s->type_label }}</span></td>
                    <td class="text-xs">{{ $s->period_label }}</td>
                    <td class="font-medium">Rp {{ number_format($s->amount, 0, ',', '.') }}</td>
                    <td class="text-xs text-mony-muted">{{ $s->submitted_at->diffForHumans() }}</td>
                    <td @click.stop>
                        @if($s->transfer_proof_path)
                            <a href="{{ asset('storage/' . $s->transfer_proof_path) }}" target="_blank"
                               class="text-primary text-xs hover:underline">Lihat</a>
                        @else <span class="text-mony-muted text-xs">-</span>
                        @endif
                    </td>
                    <td @click.stop>
                        <div class="flex gap-1">
                            <form method="POST" action="{{ route('admin.konfirmasi.simpanan.confirm', $s) }}">
                                @csrf
                                <button class="btn-success btn-sm" onclick="return confirmAction(event, 'Konfirmasi simpanan ini?', 'Ya, Konfirmasi')">Konfirmasi</button>
                            </form>
                            <button type="button" class="btn-danger btn-sm"
                                    onclick="openRejectModal('Alasan penolakan simpanan {{ addslashes($s->anggota?->name) }}', '{{ route('admin.konfirmasi.simpanan.reject', $s) }}')">Tolak</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-mony-muted">Tidak ada simpanan pending</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($simpanan->hasPages())
            <div class="px-4 py-3 border-t">{{ $simpanan->links() }}</div>
        @endif
    </div>
    @foreach($simpanan as $s)
        @include('partials.konfirmasi-modal', ['type' => 'simpanan', 'item' => $s, 'routePrefix' => 'admin'])
    @endforeach
    </div>

@else
    {{-- Registrasi --}}
    <div x-data="{ openModal: null }">
    <div class="card overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="section-title">Registrasi Anggota Pending ({{ $counts['registrasi'] }})</h3>
        </div>
        <table class="table-base">
            <thead><tr>
                <th>Nama</th><th>Email</th><th>NIK</th><th>HP</th><th>KYC</th><th>Tanggal</th><th>Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($registrasi as $user)
                <tr @click="openModal = 'registrasi-{{ $user->id }}'" class="cursor-pointer">
                    <td class="font-medium">{{ $user->name }}</td>
                    <td class="text-xs">{{ $user->email }}</td>
                    <td class="font-mono text-xs">{{ $user->nik ?? '-' }}</td>
                    <td class="text-xs">{{ $user->phone ?? '-' }}</td>
                    <td @click.stop>
                        <div class="flex gap-1">
                            @if($user->ktp_photo)
                                <button type="button" @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $user->ktp_photo) }}', type: 'image' }" class="text-xs text-primary hover:underline">KTP</button>
                            @endif
                            @if($user->selfie_photo)
                                <button type="button" @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $user->selfie_photo) }}', type: 'image' }" class="text-xs text-primary hover:underline">Selfie</button>
                            @endif
                        </div>
                    </td>
                    <td class="text-xs text-mony-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td @click.stop>
                        <div class="flex gap-1">
                            <form method="POST" action="{{ route('admin.konfirmasi.registrasi.confirm', $user) }}">
                                @csrf
                                <button class="btn-success btn-sm" onclick="return confirmAction(event, 'Setujui registrasi {{ addslashes($user->name) }}?', 'Ya, Setujui')">Setujui</button>
                            </form>
                            <button type="button" class="btn-danger btn-sm"
                                    onclick="openRejectModal('Alasan penolakan registrasi {{ addslashes($user->name) }}', '{{ route('admin.konfirmasi.registrasi.reject', $user) }}')">Tolak</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-mony-muted">Tidak ada registrasi pending</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($registrasi->hasPages())
            <div class="px-4 py-3 border-t">{{ $registrasi->links() }}</div>
        @endif
    </div>
    @foreach($registrasi as $user)
        @include('partials.konfirmasi-modal', ['type' => 'registrasi', 'item' => $user, 'routePrefix' => 'admin'])
    @endforeach
    </div>
@endif
@endsection