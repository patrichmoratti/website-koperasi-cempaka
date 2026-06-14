{{--
    Reusable detail-popup for the "Pusat Konfirmasi" pages (admin & pengurus).
    Required: $type ('registrasi'|'pengajuan'|'pembayaran'|'simpanan'), $item, $routePrefix ('admin'|'pengurus')
--}}
@php
    $modalId = $type . '-' . $item->id;
    $titles = [
        'registrasi' => 'Registrasi Anggota',
        'pengajuan'  => 'Detail Pengajuan Gadai',
        'pembayaran' => 'Konfirmasi Pembayaran',
        'simpanan'   => 'Konfirmasi Simpanan',
    ];
    $headerTitle = $titles[$type] ?? 'Detail';
    if ($type === 'registrasi') {
        $anggotaName = $item->name;
        $headerSub   = 'Mendaftar ' . $item->created_at->format('d M Y');
    } elseif ($type === 'pengajuan') {
        $anggotaName = $item->anggota?->name ?? '-';
        $headerSub   = 'Diajukan ' . ($item->submitted_at?->format('d M Y') ?? '-');
    } elseif ($type === 'pembayaran') {
        $anggotaName = $item->transaksi?->anggota?->name ?? '-';
        $headerSub   = 'Diajukan ' . ($item->submitted_at?->format('d M Y') ?? '-');
    } else {
        $anggotaName = $item->anggota?->name ?? '-';
        $headerSub   = 'Diajukan ' . ($item->submitted_at?->format('d M Y') ?? '-');
    }
@endphp

<div x-show="openModal === '{{ $modalId }}'" x-cloak
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click.self="openModal = null" @keydown.escape.window="if(!$store.lb.show){ openModal = null }"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(8,20,12,0.72); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);">

    <div @click.stop
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="popup-sheet scrollbar-hide bg-white w-full shadow-2xl"
         style="max-width:640px; max-height:88vh; overflow-y:auto; border-radius:24px;">

        {{-- Branded Header --}}
        <div class="relative overflow-hidden rounded-t-3xl px-6 py-5" style="background:linear-gradient(135deg, var(--green) 0%, var(--green2) 100%);">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:rgba(255,255,255,0.15);">
                        <img src="{{ asset('images/logo-mony.png') }}" alt="MONY" class="w-7 h-7 object-contain">
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white leading-tight">{{ $headerTitle }}</h2>
                        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6);">{{ $anggotaName }} &mdash; {{ $headerSub }}</p>
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

        {{-- ─── REGISTRASI ─── --}}
        @if($type === 'registrasi')
            @php
                $regRows = [
                    ['Nama Lengkap', $item->name],
                    ['Email',        $item->email],
                    ['No. Telepon',  $item->phone ?? '-'],
                    ['NIK',          $item->nik ?? '-'],
                    ['Alamat',       $item->address ?? '-'],
                    ['Tgl Daftar',   $item->created_at->format('d F Y')],
                ];
            @endphp
            <div class="mx-4 mt-4 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                    <p class="text-xs font-semibold text-mony-text">Data Pendaftar</p>
                </div>
                @foreach($regRows as $i => [$label, $val])
                <div class="flex justify-between items-center px-4 py-2.5 text-xs {{ $i < count($regRows)-1 ? 'border-b' : '' }}" style="{{ $i < count($regRows)-1 ? 'border-color:#f0f7ee;' : '' }}">
                    <span class="text-mony-muted flex-shrink-0">{{ $label }}</span>
                    <span class="font-semibold text-mony-text text-right max-w-xs ml-4">{{ $val }}</span>
                </div>
                @endforeach
            </div>

            <div class="mx-4 mt-3 grid grid-cols-2 gap-3">
                <div>
                    <p class="text-xs font-semibold text-mony-muted mb-1.5">Foto KTP</p>
                    @if($item->ktp_photo)
                        <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $item->ktp_photo) }}', type: 'image' }"
                             class="w-full h-36 rounded-xl overflow-hidden cursor-pointer group" style="border:1px solid #ddebd5;">
                            <img src="{{ asset('storage/' . $item->ktp_photo) }}" alt="KTP" class="w-full h-full object-cover group-hover:opacity-90 transition-opacity">
                        </div>
                    @else
                        <div class="w-full h-36 rounded-xl flex items-center justify-center text-xs text-mony-muted" style="background:#f5faf3; border:1px solid #ddebd5;">Belum diunggah</div>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-semibold text-mony-muted mb-1.5">Selfie + KTP</p>
                    @if($item->selfie_photo)
                        <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $item->selfie_photo) }}', type: 'image' }"
                             class="w-full h-36 rounded-xl overflow-hidden cursor-pointer group" style="border:1px solid #ddebd5;">
                            <img src="{{ asset('storage/' . $item->selfie_photo) }}" alt="Selfie" class="w-full h-full object-cover group-hover:opacity-90 transition-opacity">
                        </div>
                    @else
                        <div class="w-full h-36 rounded-xl flex items-center justify-center text-xs text-mony-muted" style="background:#f5faf3; border:1px solid #ddebd5;">Belum diunggah</div>
                    @endif
                </div>
            </div>

            <div class="mx-4 mt-4 mb-5 flex gap-3">
                <button type="button" class="btn-danger flex-1 justify-center"
                        onclick="openRejectModal('Alasan penolakan registrasi {{ addslashes($item->name) }}', '{{ route($routePrefix . '.konfirmasi.registrasi.reject', $item) }}')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Tolak
                </button>
                <form method="POST" action="{{ route($routePrefix . '.konfirmasi.registrasi.confirm', $item) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="btn-success w-full justify-center" onclick="return confirmAction(event, 'Setujui registrasi {{ addslashes($item->name) }}?', 'Ya, Setujui')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Verifikasi & Setujui
                    </button>
                </form>
            </div>

        {{-- ─── PENGAJUAN GADAI ─── --}}
        @elseif($type === 'pengajuan')
            {{-- Foto barang --}}
            @if(!empty($item->item_photo_paths))
            <div class="grid grid-cols-3 gap-2 px-4 mt-4">
                @foreach(array_slice($item->item_photo_paths, 0, 3) as $path)
                <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $path) }}', type: 'image' }"
                     class="rounded-xl overflow-hidden cursor-pointer group" style="aspect-ratio:1">
                    <img src="{{ asset('storage/' . $path) }}" class="w-full h-full object-cover group-hover:opacity-90 transition-opacity">
                </div>
                @endforeach
            </div>
            @endif

            {{-- Video barang --}}
            @if(!empty($item->item_video_path))
            <div class="px-4 mt-3">
                <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $item->item_video_path) }}', type: 'video' }"
                     class="relative rounded-2xl overflow-hidden cursor-pointer group" style="background:#111; aspect-ratio:16/9; max-height:180px;">
                    <video src="{{ asset('storage/' . $item->item_video_path) }}" class="w-full h-full object-contain" preload="metadata" muted></video>
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
                <p class="text-xs text-mony-muted mb-0.5">Nomor Referensi</p>
                <p class="font-bold text-sm tracking-wide" style="color:var(--green);">{{ $item->ref_number }}</p>
            </div>

            @php
                $pRows = [
                    ['Jenis Barang',      $item->jenisBarang?->name ?? '-'],
                    ['Merk / Tipe',       $item->brand_name ?? '-'],
                    ['Kondisi',           $item->condition ?? '-'],
                    ['Berat / Jumlah',    $item->weight_or_quantity ?? '-'],
                    ['Estimasi Nilai',    'Rp ' . number_format($item->estimated_value, 0, ',', '.')],
                    ['Pinjaman Diajukan', 'Rp ' . number_format($item->loan_request_amount, 0, ',', '.')],
                    ['Tanggal Pengajuan', $item->submitted_at?->format('d M Y') ?? '-'],
                ];
                $greenLabels = ['Estimasi Nilai', 'Pinjaman Diajukan'];
            @endphp
            <div class="mx-4 mt-3 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                    <p class="text-xs font-semibold text-mony-text">Detail Pengajuan</p>
                </div>
                @foreach($pRows as $i => [$label, $val])
                <div class="flex justify-between items-center px-4 py-2.5 text-xs {{ $i < count($pRows)-1 ? 'border-b' : '' }}" style="{{ $i < count($pRows)-1 ? 'border-color:#f0f7ee;' : '' }}">
                    <span class="text-mony-muted flex-shrink-0">{{ $label }}</span>
                    <span class="font-semibold text-right ml-4 {{ in_array($label, $greenLabels) ? '' : 'text-mony-text' }}"
                          @if(in_array($label, $greenLabels)) style="color:var(--green);" @endif>{{ $val }}</span>
                </div>
                @endforeach
            </div>

            @if($item->description)
            <div class="mx-4 mt-3 p-4 rounded-2xl" style="background:#f5faf3; border:1px solid #ddebd5;">
                <p class="text-xs font-semibold text-mony-muted mb-1">Deskripsi Barang</p>
                <p class="text-xs text-mony-text leading-relaxed">{{ $item->description }}</p>
            </div>
            @endif

            @if(!empty($item->supporting_doc_paths))
            <div class="mx-4 mt-3">
                <p class="text-xs font-semibold text-mony-muted mb-2">Dokumen Pendukung</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($item->supporting_doc_paths as $path)
                    <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $path) }}', type: 'doc' }"
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

            <div class="mx-4 mt-4 mb-5">
                <div class="rounded-xl p-4 mb-3 text-xs" style="background:rgba(234,243,222,0.6); border:1px solid #c6e4a8;">
                    <p class="font-semibold mb-1" style="color:var(--green);">Langkah selanjutnya setelah diterima:</p>
                    <p style="color:var(--green2);">Anggota membawa barang ke koperasi → Pengurus menilai barang → Transaksi gadai dibuat di halaman <strong>Manajemen Gadai</strong>.</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" class="btn-danger flex-1 justify-center"
                            onclick="openRejectModal('Alasan penolakan pengajuan gadai {{ addslashes($item->jenisBarang?->name) }} dari {{ addslashes($item->anggota?->name) }}', '{{ route($routePrefix . '.konfirmasi.pengajuan.reject', $item) }}')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Tolak
                    </button>
                    <form method="POST" action="{{ route($routePrefix . '.konfirmasi.pengajuan.approve', $item) }}" class="flex-1">
                        @csrf
                        <button type="submit" class="btn-success w-full justify-center"
                                onclick="return confirmAction(event, 'Terima pengajuan ini? Anggota akan dihubungi untuk membawa barang ke koperasi.', 'Ya, Terima')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Terima Pengajuan
                        </button>
                    </form>
                </div>
            </div>

        {{-- ─── PEMBAYARAN ─── --}}
        @elseif($type === 'pembayaran')
            @if($item->transfer_proof_path)
            <div class="px-4 mt-4">
                <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $item->transfer_proof_path) }}', type: 'image' }"
                     class="cursor-pointer group rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                    <img src="{{ asset('storage/' . $item->transfer_proof_path) }}" alt="Bukti transfer"
                         class="w-full max-h-56 object-contain group-hover:opacity-90 transition-opacity"
                         style="background:#f5faf3;">
                </div>
                <p class="text-xs text-mony-muted mt-1.5 text-center">Bukti transfer — klik untuk perbesar</p>
            </div>
            @endif

            @php
                $gadaiRows = [
                    ['No. Referensi', $item->transaksi?->reference_number ?? '-'],
                    ['Jenis Barang',  $item->transaksi?->jenisBarang?->name ?? '-'],
                    ['Pokok Pinjaman','Rp ' . number_format($item->transaksi?->loan_amount ?? 0, 0, ',', '.')],
                    ['Bunga / Bulan', ($item->transaksi?->interest_rate ?? '-') . '%'],
                    ['Mulai Gadai',   $item->transaksi?->pawn_date?->format('d M Y') ?? '-'],
                    ['Jatuh Tempo',   $item->transaksi?->due_date?->format('d M Y') ?? '-'],
                ];
                $bayarRows = [
                    ['Jenis Pembayaran', $item->payment_type_label],
                    ['Bulan Dibayar',    $item->month_covered ?? (is_array($item->paid_months) ? implode(', ', $item->paid_months) : '-')],
                    ['Tgl Pengajuan',    $item->submitted_at?->format('d M Y, H:i') ?? '-'],
                    ['Jumlah Dibayar',   'Rp ' . number_format($item->amount, 0, ',', '.')],
                ];
            @endphp
            <div class="mx-4 mt-4 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                    <p class="text-xs font-semibold text-mony-text">Detail Gadai</p>
                </div>
                @foreach($gadaiRows as $i => [$label, $val])
                <div class="flex justify-between items-center px-4 py-2.5 text-xs {{ $i < count($gadaiRows)-1 ? 'border-b' : '' }}" style="{{ $i < count($gadaiRows)-1 ? 'border-color:#f0f7ee;' : '' }}">
                    <span class="text-mony-muted flex-shrink-0">{{ $label }}</span>
                    <span class="font-semibold text-mony-text text-right ml-4">{{ $val }}</span>
                </div>
                @endforeach
            </div>
            <div class="mx-4 mt-3 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                    <p class="text-xs font-semibold text-mony-text">Detail Pembayaran</p>
                </div>
                @foreach($bayarRows as $i => [$label, $val])
                <div class="flex justify-between items-center px-4 py-2.5 text-xs {{ $i < count($bayarRows)-1 ? 'border-b' : '' }}" style="{{ $i < count($bayarRows)-1 ? 'border-color:#f0f7ee;' : '' }}">
                    <span class="text-mony-muted flex-shrink-0">{{ $label }}</span>
                    <span class="font-semibold text-right ml-4 {{ $label === 'Jumlah Dibayar' ? '' : 'text-mony-text' }}"
                          @if($label === 'Jumlah Dibayar') style="color:var(--green);" @endif>{{ $val }}</span>
                </div>
                @endforeach
            </div>

            <div class="mx-4 mt-4 mb-5 flex gap-3">
                <button type="button" class="btn-danger flex-1 justify-center"
                        onclick="openRejectModal('Alasan penolakan pembayaran {{ addslashes($item->transaksi?->anggota?->name) }}', '{{ route($routePrefix . '.konfirmasi.pembayaran.reject', $item) }}')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Tolak
                </button>
                <form method="POST" action="{{ route($routePrefix . '.konfirmasi.pembayaran.confirm', $item) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="btn-success w-full justify-center" onclick="return confirmAction(event, 'Konfirmasi pembayaran ini?', 'Ya, Konfirmasi')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Verifikasi & Konfirmasi
                    </button>
                </form>
            </div>

        {{-- ─── SIMPANAN ─── --}}
        @else
            @if($item->transfer_proof_path)
            <div class="px-4 mt-4">
                <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $item->transfer_proof_path) }}', type: 'image' }"
                     class="cursor-pointer group rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                    <img src="{{ asset('storage/' . $item->transfer_proof_path) }}" alt="Bukti transfer"
                         class="w-full max-h-56 object-contain group-hover:opacity-90 transition-opacity"
                         style="background:#f5faf3;">
                </div>
                <p class="text-xs text-mony-muted mt-1.5 text-center">Bukti transfer — klik untuk perbesar</p>
            </div>
            @endif

            @php
                $simpRows = [
                    ['Tipe',           $item->type_label],
                    ['Periode',        $item->period_label],
                    ['Tgl Pengajuan',  $item->submitted_at?->format('d M Y, H:i') ?? '-'],
                    ['Jumlah',         'Rp ' . number_format($item->amount, 0, ',', '.')],
                ];
            @endphp
            <div class="mx-4 mt-4 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                    <p class="text-xs font-semibold text-mony-text">Detail Simpanan</p>
                </div>
                @foreach($simpRows as $i => [$label, $val])
                <div class="flex justify-between items-center px-4 py-2.5 text-xs {{ $i < count($simpRows)-1 ? 'border-b' : '' }}" style="{{ $i < count($simpRows)-1 ? 'border-color:#f0f7ee;' : '' }}">
                    <span class="text-mony-muted flex-shrink-0">{{ $label }}</span>
                    <span class="font-semibold text-right ml-4 {{ $label === 'Jumlah' ? '' : 'text-mony-text' }}"
                          @if($label === 'Jumlah') style="color:var(--green);" @endif>{{ $val }}</span>
                </div>
                @endforeach
            </div>

            <div class="mx-4 mt-4 mb-5 flex gap-3">
                <button type="button" class="btn-danger flex-1 justify-center"
                        onclick="openRejectModal('Alasan penolakan simpanan {{ addslashes($item->anggota?->name) }}', '{{ route($routePrefix . '.konfirmasi.simpanan.reject', $item) }}')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Tolak
                </button>
                <form method="POST" action="{{ route($routePrefix . '.konfirmasi.simpanan.confirm', $item) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="btn-success w-full justify-center" onclick="return confirmAction(event, 'Konfirmasi simpanan ini?', 'Ya, Konfirmasi')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Verifikasi & Konfirmasi
                    </button>
                </form>
            </div>
        @endif

    </div>
</div>