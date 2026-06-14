@extends('layouts.pengurus')
@php $title = 'Manajemen Gadai'; @endphp
@section('content')

{{-- Page header --}}
<div x-data="{ createOpen: false }">
<div class="flex items-start justify-between mb-5">
    <div>
        <span class="badge-primary text-xs mb-1 inline-block">Pengurus</span>
        <h1 class="text-2xl font-bold text-mony-text tracking-tight">Manajemen Gadai</h1>
        <p class="text-sm mt-0.5 text-mony-muted">Penilaian barang dan monitoring transaksi gadai aktif.</p>
    </div>
    <button type="button" @click="createOpen = true"
            class="btn-primary btn-sm flex-shrink-0 mt-1">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Gadai Manual
    </button>
</div>
@include('pengurus.gadai._popup_create_manual')
</div>{{-- /createOpen x-data --}}

{{-- Tab navigation (no badges) --}}
<div class="flex gap-1 p-1 rounded-2xl mb-5" style="background: rgba(255,255,255,0.1);">
    @foreach(['penilaian'=>'Menunggu Penilaian','aktif'=>'Transaksi Aktif','selesai'=>'Selesai'] as $key => $label)
        <a href="{{ route('pengurus.gadai.index', ['tab' => $key]) }}"
           class="flex-1 text-center py-2 rounded-xl text-xs font-semibold transition-all"
           style="{{ $tab === $key ? 'background:white; color:var(--green); box-shadow:0 1px 6px rgba(0,0,0,0.15);' : 'color:rgba(255,255,255,0.55);' }}"
           onmouseover="{{ $tab !== $key ? "this.style.color='rgba(255,255,255,0.9)'" : '' }}"
           onmouseout="{{ $tab !== $key ? "this.style.color='rgba(255,255,255,0.55)'" : '' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

{{-- ══ TAB: MENUNGGU PENILAIAN ══ --}}
@if($tab === 'penilaian')

<div x-data="{ openModal: null }" @keydown.escape.window="if(!$store.lb.show){ openModal = null }">

    @if($counts['penilaian'] > 0)
    <div class="rounded-xl p-4 mb-4 flex items-start gap-3" style="background:rgba(234,243,222,0.5); border:1px solid #c6e4a8;">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm" style="color:var(--green2);">Klik kartu barang untuk melihat detail dan melakukan penilaian. Input <strong>nilai taksiran</strong> dan <strong>pinjaman disetujui</strong> untuk membuat transaksi gadai aktif.</p>
    </div>
    @endif

    {{-- Card list --}}
    <div class="card overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm font-semibold text-mony-text">Barang Menunggu Penilaian</p>
            @if($counts['penilaian'] > 0)
                <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:var(--green-light); color:var(--green);">{{ $counts['penilaian'] }} item</span>
            @endif
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($menungguPenilaian as $p)
            <div @click="openModal = 'penilaian-{{ $p->id }}'"
                 class="p-4 cursor-pointer transition-colors group"
                 onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                <div class="flex items-start gap-3">
                    @php $thumb = !empty($p->item_photo_paths[0]) ? asset('storage/'.$p->item_photo_paths[0]) : null; @endphp
                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-100">
                        @if($thumb)
                            <img src="{{ $thumb }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center" style="background:var(--green-light)">
                                <svg class="w-6 h-6" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <p class="font-semibold text-sm text-mony-text">{{ $p->anggota?->name }}</p>
                                <p class="text-xs text-mony-muted">{{ $p->jenisBarang?->name }}{{ $p->brand_name ? ' · '.$p->brand_name : '' }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="badge-success text-xs">Diterima</span>
                                <span class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
                                      style="background:var(--green-light); color:var(--green);">Nilai Barang →</span>
                            </div>
                        </div>
                        <div class="flex gap-4 text-xs mt-1.5">
                            <span class="text-mony-muted">Est. nilai: <strong style="color:var(--green);">Rp {{ number_format($p->estimated_value, 0, ',', '.') }}</strong></span>
                            <span class="text-mony-muted">Ajuan: <strong style="color:var(--green);">Rp {{ number_format($p->loan_request_amount, 0, ',', '.') }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="py-12 text-center">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                <p class="text-sm font-medium text-mony-muted">Tidak ada barang menunggu penilaian</p>
                <p class="text-xs text-mony-muted mt-1">Barang yang pengajuannya diterima akan muncul di sini.</p>
            </div>
            @endforelse
        </div>
        @if($menungguPenilaian->hasPages())
            <div class="px-4 py-3 border-t">{{ $menungguPenilaian->links() }}</div>
        @endif
    </div>

    {{-- ── Popup Penilaian untuk setiap item ── --}}
    @foreach($menungguPenilaian as $p)
    <div x-show="openModal === 'penilaian-{{ $p->id }}'" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click.self="openModal = null"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(8,20,12,0.72); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);">

        <div @click.stop
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="popup-sheet scrollbar-hide bg-white w-full shadow-2xl"
             style="max-width:640px; max-height:88vh; overflow-y:auto; border-radius:24px;">

            {{-- Branded Header --}}
            <div class="relative overflow-hidden rounded-t-3xl px-6 py-5"
                 style="background:linear-gradient(135deg, var(--green) 0%, var(--green2) 100%);">
                <div class="absolute inset-0 opacity-[0.05]"
                     style="background-image:radial-gradient(circle,#fff 1px,transparent 1px); background-size:14px 14px;"></div>
                <div class="relative flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0"
                             style="background:rgba(255,255,255,0.15);">
                            <img src="{{ asset('images/logo-mony.png') }}" alt="MONY" class="w-7 h-7 object-contain">
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white leading-tight">Penilaian Barang</h2>
                            <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6);">{{ $p->anggota?->name }} &mdash; {{ $p->submitted_at?->format('d M Y') }}</p>
                        </div>
                    </div>
                    <button @click="openModal = null"
                            class="w-8 h-8 flex items-center justify-center rounded-full flex-shrink-0 transition-colors"
                            style="background:rgba(255,255,255,0.15);"
                            onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Foto barang --}}
            @if(!empty($p->item_photo_paths))
            <div class="grid grid-cols-3 gap-2 px-4 mt-4">
                @foreach(array_slice($p->item_photo_paths, 0, 3) as $photo)
                <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $photo) }}', type: 'image' }"
                     class="rounded-xl overflow-hidden cursor-pointer group" style="aspect-ratio:1;">
                    <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover group-hover:opacity-90 transition-opacity">
                </div>
                @endforeach
            </div>
            @endif

            {{-- Video barang --}}
            @if(!empty($p->item_video_path))
            <div class="px-4 mt-3">
                <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $p->item_video_path) }}', type: 'video' }"
                     class="relative rounded-2xl overflow-hidden cursor-pointer group" style="background:#111; aspect-ratio:16/9; max-height:180px;">
                    <video src="{{ asset('storage/' . $p->item_video_path) }}" class="w-full h-full object-contain" preload="metadata" muted></video>
                    <div class="absolute inset-0 flex items-center justify-center" style="background:rgba(0,0,0,0.32);">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform" style="background:rgba(255,255,255,0.92);">
                            <svg class="w-5 h-5 ml-0.5" style="color:var(--green)" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-mony-muted mt-1.5 text-center">Video barang — klik untuk putar</p>
            </div>
            @endif

            {{-- Nomor Referensi --}}
            <div class="mx-4 mt-4 py-2.5 rounded-xl text-center border-2 border-dashed" style="border-color:var(--green);">
                <p class="text-xs text-mony-muted mb-0.5">Nomor Referensi Pengajuan</p>
                <p class="font-bold text-sm tracking-wide" style="color:var(--green);">{{ $p->ref_number }}</p>
            </div>

            {{-- Detail pengajuan --}}
            @php
                $pRows = [
                    ['Jenis Barang',      $p->jenisBarang?->name ?? '-'],
                    ['Merk / Tipe',       $p->brand_name ?? '-'],
                    ['Kondisi',           $p->condition ?? '-'],
                    ['Berat / Jumlah',    $p->weight_or_quantity ?? '-'],
                    ['Estimasi Nilai',    'Rp ' . number_format($p->estimated_value, 0, ',', '.')],
                    ['Pinjaman Diajukan', 'Rp ' . number_format($p->loan_request_amount, 0, ',', '.')],
                    ['Tanggal Pengajuan', $p->submitted_at?->format('d M Y') ?? '-'],
                    ['Dikonfirmasi oleh', $p->processor?->name ?? '-'],
                ];
                $greenLabels = ['Estimasi Nilai', 'Pinjaman Diajukan'];
            @endphp
            <div class="mx-4 mt-3 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                    <p class="text-xs font-semibold text-mony-text">Detail Pengajuan</p>
                </div>
                @foreach($pRows as $i => [$label, $val])
                <div class="flex justify-between items-center px-4 py-2.5 text-xs {{ $i < count($pRows)-1 ? 'border-b' : '' }}"
                     style="{{ $i < count($pRows)-1 ? 'border-color:#f0f7ee;' : '' }}">
                    <span class="text-mony-muted flex-shrink-0">{{ $label }}</span>
                    <span class="font-semibold text-right ml-4 {{ in_array($label, $greenLabels) ? '' : 'text-mony-text' }}"
                          @if(in_array($label, $greenLabels)) style="color:var(--green);" @endif>{{ $val }}</span>
                </div>
                @endforeach
            </div>

            {{-- Deskripsi --}}
            @if($p->description)
            <div class="mx-4 mt-3 p-4 rounded-2xl" style="background:#f5faf3; border:1px solid #ddebd5;">
                <p class="text-xs font-semibold text-mony-muted mb-1">Deskripsi Barang</p>
                <p class="text-xs text-mony-text leading-relaxed">{{ $p->description }}</p>
            </div>
            @endif

            {{-- Dokumen pendukung --}}
            @if(!empty($p->supporting_doc_paths))
            <div class="mx-4 mt-3">
                <p class="text-xs font-semibold text-mony-muted mb-2">Dokumen Pendukung</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($p->supporting_doc_paths as $doc)
                    <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $doc) }}', type: 'doc' }"
                         class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium cursor-pointer transition-colors"
                         style="background:#f5faf3; border:1px solid #ddebd5; color:var(--green);"
                         onmouseover="this.style.background='#eaf3de'"
                         onmouseout="this.style.background='#f5faf3'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Dokumen {{ $loop->iteration }}
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Form Penilaian --}}
            <div class="mx-4 mt-4 mb-5"
                 x-data="{
                     loanRaw: {{ $p->loan_request_amount }},
                     fmt(n) { return n ? new Intl.NumberFormat('id-ID').format(n) : ''; },
                     handleLoan(e) { let d=e.target.value.replace(/\D/g,''); this.loanRaw=d?parseInt(d):0; e.target.value=this.fmt(this.loanRaw); }
                 }">

                <div class="p-4 rounded-2xl mb-3" style="background:#f5faf3; border:1px solid #ddebd5;">
                    <p class="text-xs font-bold text-mony-text mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Form Penilaian Barang
                    </p>
                    <form method="POST" action="{{ route('pengurus.gadai.pengajuan.nilai', $p) }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-mony-muted mb-1.5 block">Nilai Taksiran</label>
                                <div class="flex items-center rounded-xl overflow-hidden" style="border:1px solid #ddebd5; background:#f5faf3;">
                                    <span class="px-3 py-2.5 text-xs font-bold border-r flex-shrink-0"
                                          style="background:#eaf3de; border-color:#ddebd5; color:var(--green);">Rp</span>
                                    <span class="flex-1 px-3 py-2.5 text-xs font-semibold" style="color:var(--green2);">
                                        {{ number_format($p->estimated_value, 0, ',', '.') }}
                                    </span>
                                    <span class="px-2 text-xs" style="color:var(--green2);">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </span>
                                </div>
                                <p class="text-[10px] mt-1" style="color:var(--green2);">Otomatis dari nilai merk barang</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-mony-muted mb-1.5 block">Pinjaman Disetujui <span class="text-red-500">*</span></label>
                                <div class="flex items-center rounded-xl overflow-hidden" style="border:1px solid #ddebd5;">
                                    <span class="px-3 py-2.5 text-xs font-bold border-r flex-shrink-0"
                                          style="background:#eaf3de; border-color:#ddebd5; color:var(--green);">Rp</span>
                                    <input type="text"
                                           x-init="$el.value = fmt(loanRaw)"
                                           x-on:input="handleLoan($event)"
                                           class="flex-1 px-3 py-2.5 text-xs font-semibold outline-none border-0 bg-white"
                                           style="color:var(--green2);" required>
                                </div>
                                <input type="hidden" name="loan_amount" :value="loanRaw">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-mony-muted mb-1.5 block">Lokasi Penyimpanan</label>
                            <input type="text" name="warehouse_location" placeholder="Contoh: Rak A-01"
                                   class="form-input text-xs" style="border-color:#ddebd5;">
                        </div>
                        <div class="p-3 rounded-xl text-xs flex items-start gap-2" style="background:rgba(234,243,222,0.6); border:1px solid #c6e4a8; color:var(--green2);">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Setelah dikonfirmasi, transaksi gadai aktif dibuat. Bunga <strong>8%/bulan</strong>. Jatuh tempo awal <strong>4 bulan</strong> sejak gadai — otomatis diperpanjang <strong>4 bulan</strong> setiap kali anggota membayar bunga.</span>
                        </div>
                        <button type="submit" class="btn-success w-full justify-center"
                                onclick="return confirmAction(event, 'Konfirmasi penilaian dan buat transaksi gadai aktif?', 'Ya, Buat Transaksi')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Konfirmasi & Buat Transaksi
                        </button>
                    </form>
                </div>

            </div>

        </div>
    </div>
    @endforeach

</div>{{-- /x-data openModal --}}

{{-- ══ TAB: TRANSAKSI AKTIF ══ --}}
@elseif($tab === 'aktif')
<div x-data="{ openModal: null }" @keydown.escape.window="if(!$store.lb?.show){ openModal = null }">
    <div class="card overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm font-semibold text-mony-text">Transaksi Gadai Aktif</p>
            @if($counts['aktif'] > 0)
                <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:var(--green-light); color:var(--green);">{{ $counts['aktif'] }} aktif</span>
            @endif
        </div>
        <table class="table-base">
            <thead><tr>
                <th>No. Ref</th><th>Anggota</th><th>Barang</th>
                <th>Pinjaman</th><th>Jatuh Tempo</th><th>Status</th><th></th>
            </tr></thead>
            <tbody>
                @forelse($transaksiAktif as $t)
                <tr @click="openModal = 'transaksi-{{ $t->id }}'" class="cursor-pointer">
                    <td class="font-mono text-xs">{{ $t->reference_number }}</td>
                    <td class="font-medium">{{ $t->anggota?->name }}</td>
                    <td>{{ $t->jenisBarang?->name }}</td>
                    <td class="font-medium">Rp {{ number_format($t->loan_amount, 0, ',', '.') }}</td>
                    <td class="{{ $t->isOverdue() ? 'text-red-600 font-medium' : 'text-mony-muted text-xs' }}">
                        {{ $t->due_date->format('d/m/Y') }}
                        @if($t->isOverdue())<span class="text-xs"> (Lewat!)</span>@endif
                    </td>
                    <td><span class="badge-{{ $t->status_color }}">{{ $t->status_label }}</span></td>
                    <td @click.stop>
                        <button type="button" @click.stop="openModal = 'transaksi-{{ $t->id }}'"
                                class="btn-primary btn-sm">Detail</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-mony-muted">Tidak ada transaksi aktif</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($transaksiAktif->hasPages())
            <div class="px-4 py-3 border-t">{{ $transaksiAktif->links() }}</div>
        @endif
    </div>

    @foreach($transaksiAktif as $t)
        @include('pengurus.gadai._popup_transaksi', ['t' => $t])
    @endforeach
</div>

{{-- ══ TAB: SELESAI ══ --}}
@else
<div x-data="{ openModal: null }" @keydown.escape.window="if(!$store.lb?.show){ openModal = null }">
    <div class="card overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm font-semibold text-mony-text">Transaksi Selesai</p>
        </div>
        <table class="table-base">
            <thead><tr>
                <th>No. Ref</th><th>Anggota</th><th>Barang</th>
                <th>Pinjaman</th><th>Tanggal</th><th>Status</th><th></th>
            </tr></thead>
            <tbody>
                @forelse($transaksiSelesai as $t)
                <tr @click="openModal = 'transaksi-{{ $t->id }}'" class="cursor-pointer">
                    <td class="font-mono text-xs">{{ $t->reference_number }}</td>
                    <td class="font-medium">{{ $t->anggota?->name }}</td>
                    <td>{{ $t->jenisBarang?->name }}</td>
                    <td>Rp {{ number_format($t->loan_amount, 0, ',', '.') }}</td>
                    <td class="text-xs text-mony-muted">{{ $t->pawn_date->format('d/m/Y') }}</td>
                    <td><span class="badge-{{ $t->status_color }}">{{ $t->status_label }}</span></td>
                    <td @click.stop>
                        <button type="button" @click.stop="openModal = 'transaksi-{{ $t->id }}'"
                                class="btn-primary btn-sm">Detail</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-mony-muted">Tidak ada transaksi selesai</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($transaksiSelesai->hasPages())
            <div class="px-4 py-3 border-t">{{ $transaksiSelesai->links() }}</div>
        @endif
    </div>

    @foreach($transaksiSelesai as $t)
        @include('pengurus.gadai._popup_transaksi', ['t' => $t])
    @endforeach
</div>
@endif

@endsection