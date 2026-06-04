@extends('layouts.pengurus')
@php
$title = 'Konfirmasi';
@endphp
@section('content')
<h1 class="page-title mb-6">Pusat Konfirmasi</h1>

<div class="flex gap-1 mb-4 bg-gray-100 p-1 rounded-xl w-fit flex-wrap">
    @foreach(['pengajuan'=>'Pengajuan Gadai','pembayaran_gadai'=>'Pembayaran','simpanan'=>'Simpanan','registrasi'=>'Registrasi'] as $key => $label)
        <a href="{{ route('pengurus.konfirmasi.index', ['tab' => $key]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $tab === $key ? 'bg-white shadow text-mony-text' : 'text-mony-muted hover:text-mony-text' }}">
            {{ $label }}
            @if($counts[$key] > 0)
                <span class="ml-1 badge-danger text-xs">{{ $counts[$key] }}</span>
            @endif
        </a>
    @endforeach
</div>

@if($tab === 'pengajuan')
    <div class="card overflow-hidden">
        <table class="table-base">
            <thead><tr><th>Anggota</th><th>Barang</th><th>Pinjaman</th><th>Estimasi</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($pengajuan as $p)
                <tr x-data="{ showApprove: false, showReject: false }">
                    <td>
                        <p class="font-medium text-sm">{{ $p->anggota?->name }}</p>
                        <p class="text-xs text-mony-muted">{{ $p->submitted_at->diffForHumans() }}</p>
                    </td>
                    <td>{{ $p->jenisBarang?->name }}<br><span class="text-xs text-mony-muted">{{ $p->condition }}</span></td>
                    <td class="font-medium">Rp {{ number_format($p->loan_request_amount, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($p->estimated_value, 0, ',', '.') }}</td>
                    <td>
                        <div class="space-y-2">
                            <button @click="showApprove = !showApprove; showReject = false" class="btn-success btn-sm">Setujui</button>
                            <button @click="showReject = !showReject; showApprove = false" class="btn-danger btn-sm">Tolak</button>
                        </div>
                        <div x-show="showApprove" class="mt-2 bg-green-50 p-3 rounded-xl border border-green-200" style="display:none">
                            <form method="POST" action="{{ route('pengurus.konfirmasi.pengajuan.approve', $p) }}" class="space-y-2">
                                @csrf
                                <input type="number" name="appraisal_value" placeholder="Nilai Taksir (Rp)" class="form-input text-xs" value="{{ $p->estimated_value }}" required>
                                <input type="number" name="loan_amount" placeholder="Pinjaman Disetujui (Rp)" class="form-input text-xs" value="{{ $p->loan_request_amount }}" required>
                                <input type="text" name="warehouse_location" placeholder="Lokasi penyimpanan" class="form-input text-xs">
                                <button type="submit" class="btn-success btn-sm w-full">Konfirmasi Setuju</button>
                            </form>
                        </div>
                        <div x-show="showReject" class="mt-2 bg-red-50 p-3 rounded-xl border border-red-200" style="display:none">
                            <form method="POST" action="{{ route('pengurus.konfirmasi.pengajuan.reject', $p) }}" class="space-y-2">
                                @csrf
                                <textarea name="reason" class="form-input text-xs" rows="2" placeholder="Alasan penolakan..." required></textarea>
                                <button type="submit" class="btn-danger btn-sm w-full">Konfirmasi Tolak</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-8 text-mony-muted">Tidak ada pengajuan pending</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($pengajuan->hasPages()) <div class="px-4 py-3 border-t">{{ $pengajuan->links() }}</div> @endif
    </div>

@elseif($tab === 'pembayaran_gadai')
    <div class="card overflow-hidden">
        <table class="table-base">
            <thead><tr><th>Anggota</th><th>Ref Gadai</th><th>Tipe</th><th>Jumlah</th><th>Bukti</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($pembayaranGadai as $p)
                <tr>
                    <td class="font-medium">{{ $p->transaksi?->anggota?->name }}</td>
                    <td class="font-mono text-xs">{{ $p->transaksi?->reference_number }}</td>
                    <td><span class="badge-{{ $p->payment_type === 'tebus' ? 'info' : 'success' }}">{{ $p->payment_type_label }}</span></td>
                    <td>Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                    <td>
                        @if($p->transfer_proof_path)
                            <a href="{{ asset('storage/' . $p->transfer_proof_path) }}" target="_blank" class="text-primary text-xs hover:underline">Lihat</a>
                        @else <span class="text-mony-muted text-xs">-</span> @endif
                    </td>
                    <td>
                        <div class="flex gap-1" x-data="{ showReject: false }">
                            <form method="POST" action="{{ route('pengurus.konfirmasi.pembayaran.confirm', $p) }}">
                                @csrf <button class="btn-success btn-sm" onclick="return confirm('Konfirmasi pembayaran?')">Konfirmasi</button>
                            </form>
                            <div x-data="{ showR: false }">
                                <button @click="showR = !showR" class="btn-danger btn-sm">Tolak</button>
                                <div x-show="showR" class="mt-1" style="display:none">
                                    <form method="POST" action="{{ route('pengurus.konfirmasi.pembayaran.reject', $p) }}" class="space-y-1">
                                        @csrf
                                        <textarea name="reason" class="form-input text-xs" rows="2" placeholder="Alasan..." required></textarea>
                                        <button class="btn-danger btn-sm w-full">Tolak</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-mony-muted">Tidak ada pembayaran pending</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($pembayaranGadai->hasPages()) <div class="px-4 py-3 border-t">{{ $pembayaranGadai->links() }}</div> @endif
    </div>

@elseif($tab === 'simpanan')
    <div class="card overflow-hidden">
        <table class="table-base">
            <thead><tr><th>Anggota</th><th>Tipe</th><th>Periode</th><th>Jumlah</th><th>Bukti</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($simpanan as $s)
                <tr>
                    <td class="font-medium">{{ $s->anggota?->name }}</td>
                    <td><span class="badge-{{ $s->type === 'pokok' ? 'info' : 'primary' }}">{{ $s->type_label }}</span></td>
                    <td class="text-xs">{{ $s->period_label }}</td>
                    <td>Rp {{ number_format($s->amount, 0, ',', '.') }}</td>
                    <td>
                        @if($s->transfer_proof_path)
                            <a href="{{ asset('storage/' . $s->transfer_proof_path) }}" target="_blank" class="text-primary text-xs hover:underline">Lihat</a>
                        @else <span class="text-mony-muted text-xs">-</span> @endif
                    </td>
                    <td>
                        <div class="flex gap-1">
                            <form method="POST" action="{{ route('pengurus.konfirmasi.simpanan.confirm', $s) }}">
                                @csrf <button class="btn-success btn-sm" onclick="return confirm('Konfirmasi simpanan?')">Konfirmasi</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-mony-muted">Tidak ada simpanan pending</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($simpanan->hasPages()) <div class="px-4 py-3 border-t">{{ $simpanan->links() }}</div> @endif
    </div>

@else {{-- registrasi --}}
    <div class="card overflow-hidden">
        <table class="table-base">
            <thead><tr><th>Nama</th><th>Email</th><th>NIK</th><th>KYC</th><th>Tgl Daftar</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($registrasi as $user)
                <tr>
                    <td class="font-medium">{{ $user->name }}</td>
                    <td class="text-xs">{{ $user->email }}</td>
                    <td class="font-mono text-xs">{{ $user->nik ?? '-' }}</td>
                    <td>
                        <div class="flex gap-1">
                            @if($user->ktp_photo) <a href="{{ asset('storage/' . $user->ktp_photo) }}" target="_blank" class="text-xs text-primary hover:underline">KTP</a> @endif
                            @if($user->selfie_photo) <a href="{{ asset('storage/' . $user->selfie_photo) }}" target="_blank" class="text-xs text-primary hover:underline">Selfie</a> @endif
                        </div>
                    </td>
                    <td class="text-xs text-mony-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="flex gap-1">
                            <form method="POST" action="{{ route('pengurus.konfirmasi.registrasi.confirm', $user) }}">
                                @csrf <button class="btn-success btn-sm" onclick="return confirm('Setujui?')">Setujui</button>
                            </form>
                            <div x-data="{ showR: false }">
                                <button @click="showR = !showR" class="btn-danger btn-sm">Tolak</button>
                                <div x-show="showR" class="mt-1" style="display:none">
                                    <form method="POST" action="{{ route('pengurus.konfirmasi.registrasi.reject', $user) }}" class="space-y-1">
                                        @csrf
                                        <textarea name="reason" class="form-input text-xs" rows="2" placeholder="Alasan..." required></textarea>
                                        <button class="btn-danger btn-sm w-full">Tolak</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-mony-muted">Tidak ada registrasi pending</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($registrasi->hasPages()) <div class="px-4 py-3 border-t">{{ $registrasi->links() }}</div> @endif
    </div>
@endif
@endsection
