@extends('layouts.pengurus')
@php
$title = 'Konfirmasi';
@endphp
@section('content')
{{-- Page header --}}
<div class="mb-5">
    <span class="badge-primary text-xs mb-1 inline-block">Pengurus</span>
    <h1 class="text-2xl font-bold text-mony-text tracking-tight">Pusat Konfirmasi</h1>
    <p class="text-sm mt-0.5 text-mony-muted">Tinjau dan setujui pengajuan, pembayaran, simpanan, serta registrasi anggota.</p>
</div>

{{-- Tab navigation --}}
<div class="flex gap-1 p-1 rounded-2xl mb-5" style="background: rgba(255,255,255,0.1);">
    @foreach(['pengajuan'=>'Pengajuan Gadai','pembayaran_gadai'=>'Pembayaran','simpanan'=>'Simpanan','registrasi'=>'Registrasi'] as $key => $label)
        <a href="{{ route('pengurus.konfirmasi.index', ['tab' => $key]) }}"
           class="flex-1 text-center py-2 rounded-xl text-xs font-semibold transition-all relative inline-flex items-center justify-center gap-1.5"
           style="{{ $tab === $key ? 'background:white; color:var(--green); box-shadow:0 1px 6px rgba(0,0,0,0.15);' : 'color:rgba(255,255,255,0.55);' }}"
           onmouseover="{{ $tab !== $key ? "this.style.color='rgba(255,255,255,0.9)'" : '' }}"
           onmouseout="{{ $tab !== $key ? "this.style.color='rgba(255,255,255,0.55)'" : '' }}">
            <span>{{ $label }}</span>
            @if(($counts[$key] ?? 0) > 0)
            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center leading-none"
                  style="{{ $tab === $key ? 'background:var(--green-light); color:var(--green);' : 'background:rgba(255,255,255,0.2); color:#fff;' }}">
                {{ $counts[$key] }}
            </span>
            @endif
        </a>
    @endforeach
</div>

@if($tab === 'pengajuan')
    <div x-data="{ openModal: null }">
    <div class="card overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm font-semibold text-mony-text">Pengajuan Masuk</p>
            @if($counts['pengajuan'] > 0)<span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:var(--green-light); color:var(--green);">{{ $counts['pengajuan'] }} menunggu</span>@endif
        </div>
        <table class="table-base">
            <thead><tr><th>Anggota</th><th>Barang</th><th>Pinjaman</th><th>Estimasi</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($pengajuan as $p)
                <tr @click="openModal = 'pengajuan-{{ $p->id }}'" class="cursor-pointer">
                    <td>
                        <p class="font-medium text-sm">{{ $p->anggota?->name }}</p>
                        <p class="text-xs text-mony-muted">{{ $p->submitted_at->diffForHumans() }}</p>
                    </td>
                    <td>{{ $p->jenisBarang?->name }}<br><span class="text-xs text-mony-muted">{{ $p->condition }}</span></td>
                    <td class="font-medium">Rp {{ number_format($p->loan_request_amount, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($p->estimated_value, 0, ',', '.') }}</td>
                    <td @click.stop>
                        <div class="flex gap-1">
                            <form method="POST" action="{{ route('pengurus.konfirmasi.pengajuan.approve', $p) }}">
                                @csrf
                                <button class="btn-success btn-sm"
                                        onclick="return confirmAction(event, 'Terima pengajuan {{ addslashes($p->jenisBarang?->name) }} dari {{ addslashes($p->anggota?->name) }}? Anggota akan dihubungi untuk membawa barang ke koperasi.', 'Ya, Terima')">Terima</button>
                            </form>
                            <button type="button" class="btn-danger btn-sm"
                                    onclick="openRejectModal('Alasan penolakan pengajuan gadai {{ addslashes($p->jenisBarang?->name) }} dari {{ addslashes($p->anggota?->name) }}', '{{ route('pengurus.konfirmasi.pengajuan.reject', $p) }}')">Tolak</button>
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
    @foreach($pengajuan as $p)
        @include('partials.konfirmasi-modal', ['type' => 'pengajuan', 'item' => $p, 'routePrefix' => 'pengurus'])
    @endforeach
    </div>

@elseif($tab === 'pembayaran_gadai')
    <div x-data="{ openModal: null }">
    <div class="card overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm font-semibold text-mony-text">Konfirmasi Pembayaran</p>
            @if($counts['pembayaran_gadai'] > 0)<span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:var(--green-light); color:var(--green);">{{ $counts['pembayaran_gadai'] }} menunggu</span>@endif
        </div>
        <table class="table-base">
            <thead><tr><th>Anggota</th><th>Ref Gadai</th><th>Tipe</th><th>Jumlah</th><th>Bukti</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($pembayaranGadai as $p)
                <tr @click="openModal = 'pembayaran-{{ $p->id }}'" class="cursor-pointer">
                    <td class="font-medium">{{ $p->transaksi?->anggota?->name }}</td>
                    <td class="font-mono text-xs">{{ $p->transaksi?->reference_number }}</td>
                    <td><span class="badge-{{ $p->payment_type === 'tebus' ? 'info' : 'success' }}">{{ $p->payment_type_label }}</span></td>
                    <td>Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                    <td @click.stop>
                        @if($p->transfer_proof_path)
                            <button type="button" @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $p->transfer_proof_path) }}', type: 'image' }" class="text-primary text-xs hover:underline">Lihat</button>
                        @else <span class="text-mony-muted text-xs">-</span> @endif
                    </td>
                    <td @click.stop>
                        <div class="flex gap-1">
                            <form method="POST" action="{{ route('pengurus.konfirmasi.pembayaran.confirm', $p) }}">
                                @csrf <button class="btn-success btn-sm" onclick="return confirmAction(event, 'Konfirmasi pembayaran ini?', 'Ya, Konfirmasi')">Konfirmasi</button>
                            </form>
                            <button type="button" class="btn-danger btn-sm"
                                    onclick="openRejectModal('Alasan penolakan pembayaran {{ addslashes($p->transaksi?->anggota?->name) }}', '{{ route('pengurus.konfirmasi.pembayaran.reject', $p) }}')">Tolak</button>
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
    @foreach($pembayaranGadai as $p)
        @include('partials.konfirmasi-modal', ['type' => 'pembayaran', 'item' => $p, 'routePrefix' => 'pengurus'])
    @endforeach
    </div>

@elseif($tab === 'simpanan')
    <div x-data="{ openModal: null }">
    <div class="card overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm font-semibold text-mony-text">Konfirmasi Simpanan</p>
            @if($counts['simpanan'] > 0)<span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:var(--green-light); color:var(--green);">{{ $counts['simpanan'] }} menunggu</span>@endif
        </div>
        <table class="table-base">
            <thead><tr><th>Anggota</th><th>Tipe</th><th>Periode</th><th>Jumlah</th><th>Bukti</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($simpanan as $s)
                <tr @click="openModal = 'simpanan-{{ $s->id }}'" class="cursor-pointer">
                    <td class="font-medium">{{ $s->anggota?->name }}</td>
                    <td><span class="badge-{{ $s->type === 'pokok' ? 'info' : 'primary' }}">{{ $s->type_label }}</span></td>
                    <td class="text-xs">{{ $s->period_label }}</td>
                    <td>Rp {{ number_format($s->amount, 0, ',', '.') }}</td>
                    <td @click.stop>
                        @if($s->transfer_proof_path)
                            <a href="{{ asset('storage/' . $s->transfer_proof_path) }}" target="_blank" class="text-primary text-xs hover:underline">Lihat</a>
                        @else <span class="text-mony-muted text-xs">-</span> @endif
                    </td>
                    <td @click.stop>
                        <div class="flex gap-1">
                            <form method="POST" action="{{ route('pengurus.konfirmasi.simpanan.confirm', $s) }}">
                                @csrf <button class="btn-success btn-sm" onclick="return confirmAction(event, 'Konfirmasi simpanan ini?', 'Ya, Konfirmasi')">Konfirmasi</button>
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
    @foreach($simpanan as $s)
        @include('partials.konfirmasi-modal', ['type' => 'simpanan', 'item' => $s, 'routePrefix' => 'pengurus'])
    @endforeach
    </div>

@else {{-- registrasi --}}
    <div x-data="{ openModal: null }">
    <div class="card overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm font-semibold text-mony-text">Registrasi Anggota Baru</p>
            @if($counts['registrasi'] > 0)<span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:var(--green-light); color:var(--green);">{{ $counts['registrasi'] }} menunggu</span>@endif
        </div>
        <table class="table-base">
            <thead><tr><th>Nama</th><th>Email</th><th>NIK</th><th>KYC</th><th>Tgl Daftar</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($registrasi as $user)
                <tr @click="openModal = 'registrasi-{{ $user->id }}'" class="cursor-pointer">
                    <td class="font-medium">{{ $user->name }}</td>
                    <td class="text-xs">{{ $user->email }}</td>
                    <td class="font-mono text-xs">{{ $user->nik ?? '-' }}</td>
                    <td @click.stop>
                        <div class="flex gap-1">
                            @if($user->ktp_photo) <button type="button" @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $user->ktp_photo) }}', type: 'image' }" class="text-xs text-primary hover:underline">KTP</button> @endif
                            @if($user->selfie_photo) <button type="button" @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $user->selfie_photo) }}', type: 'image' }" class="text-xs text-primary hover:underline">Selfie</button> @endif
                        </div>
                    </td>
                    <td class="text-xs text-mony-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td @click.stop>
                        <div class="flex gap-1">
                            <form method="POST" action="{{ route('pengurus.konfirmasi.registrasi.confirm', $user) }}">
                                @csrf <button class="btn-success btn-sm" onclick="return confirmAction(event, 'Setujui registrasi {{ addslashes($user->name) }}?', 'Ya, Setujui')">Setujui</button>
                            </form>
                            <button type="button" class="btn-danger btn-sm"
                                    onclick="openRejectModal('Alasan penolakan registrasi {{ addslashes($user->name) }}', '{{ route('pengurus.konfirmasi.registrasi.reject', $user) }}')">Tolak</button>
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
    @foreach($registrasi as $user)
        @include('partials.konfirmasi-modal', ['type' => 'registrasi', 'item' => $user, 'routePrefix' => 'pengurus'])
    @endforeach
    </div>
@endif
@endsection