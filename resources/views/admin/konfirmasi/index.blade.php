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
                <tr>
                    <td class="font-medium">{{ $p->transaksi?->anggota?->name }}</td>
                    <td class="font-mono text-xs">{{ $p->transaksi?->reference_number }}</td>
                    <td><span class="badge-{{ $p->payment_type === 'tebus' ? 'info' : 'success' }}">{{ $p->payment_type_label }}</span></td>
                    <td class="font-medium">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                    <td class="text-xs text-mony-muted">{{ $p->submitted_at->diffForHumans() }}</td>
                    <td>
                        @if($p->transfer_proof_path)
                            <a href="{{ asset('storage/' . $p->transfer_proof_path) }}" target="_blank"
                               class="text-primary text-xs hover:underline">Lihat</a>
                        @else <span class="text-mony-muted text-xs">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-1" x-data="{ showReject: false }">
                            <form method="POST" action="{{ route('admin.konfirmasi.pembayaran.confirm', $p) }}">
                                @csrf
                                <button class="btn-success btn-sm" onclick="return confirm('Konfirmasi pembayaran ini?')">Konfirmasi</button>
                            </form>
                            <button @click="showReject = !showReject" class="btn-danger btn-sm">Tolak</button>
                            <div x-show="showReject" class="absolute mt-10 bg-white border border-gray-200 rounded-xl shadow-lg p-4 z-10 w-64" style="display:none">
                                <form method="POST" action="{{ route('admin.konfirmasi.pembayaran.reject', $p) }}">
                                    @csrf
                                    <textarea name="reason" class="form-input mb-2 text-xs" rows="2" placeholder="Alasan penolakan..." required></textarea>
                                    <button type="submit" class="btn-danger btn-sm w-full">Tolak</button>
                                </form>
                            </div>
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

@elseif($tab === 'simpanan')
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
                <tr>
                    <td class="font-medium">{{ $s->anggota?->name }}</td>
                    <td><span class="badge-{{ $s->type === 'pokok' ? 'info' : 'primary' }}">{{ $s->type_label }}</span></td>
                    <td class="text-xs">{{ $s->period_label }}</td>
                    <td class="font-medium">Rp {{ number_format($s->amount, 0, ',', '.') }}</td>
                    <td class="text-xs text-mony-muted">{{ $s->submitted_at->diffForHumans() }}</td>
                    <td>
                        @if($s->transfer_proof_path)
                            <a href="{{ asset('storage/' . $s->transfer_proof_path) }}" target="_blank"
                               class="text-primary text-xs hover:underline">Lihat</a>
                        @else <span class="text-mony-muted text-xs">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-1" x-data="{ showReject: false }">
                            <form method="POST" action="{{ route('admin.konfirmasi.simpanan.confirm', $s) }}">
                                @csrf
                                <button class="btn-success btn-sm" onclick="return confirm('Konfirmasi simpanan ini?')">Konfirmasi</button>
                            </form>
                            <button @click="showReject = !showReject" class="btn-danger btn-sm">Tolak</button>
                            <div x-show="showReject" class="mt-2" style="display:none">
                                <form method="POST" action="{{ route('admin.konfirmasi.simpanan.reject', $s) }}">
                                    @csrf
                                    <textarea name="reason" class="form-input mb-1 text-xs" rows="2" placeholder="Alasan..." required></textarea>
                                    <button type="submit" class="btn-danger btn-sm">Konfirmasi Tolak</button>
                                </form>
                            </div>
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

@else
    {{-- Registrasi --}}
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
                <tr>
                    <td class="font-medium">{{ $user->name }}</td>
                    <td class="text-xs">{{ $user->email }}</td>
                    <td class="font-mono text-xs">{{ $user->nik ?? '-' }}</td>
                    <td class="text-xs">{{ $user->phone ?? '-' }}</td>
                    <td>
                        <div class="flex gap-1">
                            @if($user->ktp_photo)
                                <a href="{{ asset('storage/' . $user->ktp_photo) }}" target="_blank"
                                   class="text-xs text-primary hover:underline">KTP</a>
                            @endif
                            @if($user->selfie_photo)
                                <a href="{{ asset('storage/' . $user->selfie_photo) }}" target="_blank"
                                   class="text-xs text-primary hover:underline">Selfie</a>
                            @endif
                        </div>
                    </td>
                    <td class="text-xs text-mony-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="flex gap-1" x-data="{ showReject: false }">
                            <form method="POST" action="{{ route('admin.konfirmasi.registrasi.confirm', $user) }}">
                                @csrf
                                <button class="btn-success btn-sm" onclick="return confirm('Setujui registrasi {{ $user->name }}?')">Setujui</button>
                            </form>
                            <button @click="showReject = !showReject" class="btn-danger btn-sm">Tolak</button>
                            <div x-show="showReject" class="mt-2" style="display:none">
                                <form method="POST" action="{{ route('admin.konfirmasi.registrasi.reject', $user) }}">
                                    @csrf
                                    <textarea name="reason" class="form-input mb-1 text-xs" rows="2" placeholder="Alasan..." required></textarea>
                                    <button type="submit" class="btn-danger btn-sm">Tolak</button>
                                </form>
                            </div>
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
@endif
@endsection
