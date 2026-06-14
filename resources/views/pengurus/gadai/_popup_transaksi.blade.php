{{--
    Nota Transaksi popup for pengurus Manajemen Gadai page.
    Accepts: $t (TransaksiGadai with jenisBarang, pengajuan, pembayaran loaded)
    Enclosing Alpine scope must expose: openModal (string|null)
--}}
@php
    $pPhotos           = !empty($t->pengajuan?->item_photo_paths) ? $t->pengajuan->item_photo_paths : ($t->item_photo_paths ?? []);
    $pVideo            = $t->pengajuan?->item_video_path ?? null;
    $pDocs             = $t->pengajuan?->supporting_doc_paths ?? [];
    $pMonths           = (int) $t->pawn_date->diffInMonths($t->due_date);
    $pSched            = $t->paymentSchedule();
    $pIsActive         = in_array($t->status, ['aktif', 'menunggu_lelang']);
    $confirmedPayments = $t->pembayaran->where('status', 'confirmed')->sortBy('confirmed_at')->values();
@endphp
<div x-show="openModal === 'transaksi-{{ $t->id }}'" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @click.self="openModal = null"
     @keydown.escape.window="if(!$store.lb?.show){ openModal = null }"
     style="background:rgba(8,20,12,0.72); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);"
     x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    <div @click.stop
         class="bg-white popup-sheet w-full shadow-2xl scrollbar-hide"
         style="max-width:640px; max-height:88vh; overflow-y:auto; border-radius:24px;"
         x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

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
                        <h2 class="text-base font-bold text-white leading-tight">Nota Transaksi Digital</h2>
                        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6);">
                            {{ $t->anggota?->name }} &mdash; {{ $t->pawn_date->format('d M Y') }}
                        </p>
                    </div>
                </div>
                <button type="button" @click="openModal = null"
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

        {{-- Photos --}}
        @if(count($pPhotos))
        <div class="grid grid-cols-3 gap-2 px-4 mt-4">
            @foreach(array_slice($pPhotos, 0, 6) as $photo)
            <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $photo) }}', type: 'image' }"
                 class="rounded-xl overflow-hidden cursor-pointer group" style="aspect-ratio:1;">
                <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover group-hover:opacity-85 transition-opacity">
            </div>
            @endforeach
        </div>
        @endif

        {{-- Video --}}
        @if($pVideo)
        <div class="px-4 mt-3">
            <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $pVideo) }}', type: 'video' }"
                 class="relative rounded-2xl overflow-hidden cursor-pointer group"
                 style="background:#111; aspect-ratio:16/9; max-height:180px;">
                <video src="{{ asset('storage/' . $pVideo) }}" class="w-full h-full object-contain" preload="metadata" muted></video>
                <div class="absolute inset-0 flex items-center justify-center" style="background:rgba(0,0,0,0.32);">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform"
                         style="background:rgba(255,255,255,0.92);">
                        <svg class="w-5 h-5 ml-0.5" style="color:var(--green)" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <p class="text-xs text-mony-muted mt-1.5 text-center">Video barang — klik untuk putar</p>
        </div>
        @endif

        {{-- Reference Number --}}
        <div class="mx-4 mt-4 py-2.5 rounded-xl text-center border-2 border-dashed" style="border-color:var(--green);">
            <p class="text-xs text-mony-muted mb-0.5">Nomor Referensi Transaksi</p>
            <p class="font-bold text-sm tracking-wide" style="color:var(--green);">{{ $t->reference_number }}</p>
        </div>

        {{-- Detail Barang --}}
        <div class="mx-4 mt-3 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
            <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                <p class="text-xs font-semibold text-mony-text">Detail Barang Jaminan</p>
            </div>
            @php
            $detailRows = [
                ['Anggota',          $t->anggota?->name ?? '-'],
                ['Jenis Barang',     $t->jenisBarang?->name ?? '-'],
                ['Merk / Tipe',      $t->pengajuan?->brand_name ?? $t->item_description ?? '-'],
                ['Kondisi',          $t->pengajuan?->condition ?? '-'],
                ['Lokasi Simpan',    $t->warehouse_location ?? '-'],
                ['Nilai Taksiran',   'Rp ' . number_format($t->appraisal_value, 0, ',', '.')],
                ['Nilai Pinjaman',   'Rp ' . number_format($t->loan_amount, 0, ',', '.')],
                ['Bunga / bln',      'Rp ' . number_format($t->monthlyInterest(), 0, ',', '.')],
                ['Mulai Gadai',      $t->pawn_date->format('d M Y')],
                ['Durasi Gadai',     $pMonths . ' bulan'],
                ['Jatuh Tempo',      $t->due_date->format('d M Y')],
                ['Status',           $t->status_label],
            ];
            @endphp
            @foreach($detailRows as $i => [$label, $val])
            <div class="flex justify-between items-center px-4 py-2 text-xs {{ $i < count($detailRows)-1 ? 'border-b' : '' }}"
                 style="{{ $i < count($detailRows)-1 ? 'border-color:#f0f7ee;' : '' }}">
                <span class="text-mony-muted flex-shrink-0">{{ $label }}</span>
                <span class="font-semibold text-mony-text text-right ml-4">{{ $val }}</span>
            </div>
            @endforeach
        </div>

        {{-- Ringkasan Grid --}}
        <div class="mx-4 mt-3 grid grid-cols-2 gap-2">
            @php
            $gridItems = [
                ['Jumlah Pinjaman',   'Rp ' . number_format($t->loan_amount / 1000000, 1) . ' jt'],
                ['Bunga per bulan',   $t->interest_rate . '%/bln'],
                ['Cicilan Bunga/bln', 'Rp ' . number_format($t->monthlyInterest() / 1000, 0, ',', '.') . ' rb'],
                ['Durasi Gadai',      $pMonths . ' bulan'],
            ];
            @endphp
            @foreach($gridItems as [$glabel, $gval])
            <div class="rounded-xl p-3" style="background:var(--green-light);">
                <p class="text-xs text-mony-muted mb-0.5">{{ $glabel }}</p>
                <p class="font-bold text-sm" style="color:var(--green);">{{ $gval }}</p>
            </div>
            @endforeach
        </div>

        {{-- Jadwal Pembayaran Bunga: hanya untuk transaksi aktif --}}
        @if($pIsActive && count($pSched))
        <div class="mx-4 mt-3">
            <p class="text-xs font-semibold text-mony-text mb-2 flex items-center gap-1.5">
                <span class="w-0.5 h-3.5 rounded-full inline-block" style="background:var(--green);"></span>
                Jadwal Pembayaran Bunga
            </p>
            <div class="rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="grid grid-cols-4 text-xs font-semibold text-mony-muted px-3 py-2" style="background:#f5faf3;">
                    <span>#</span><span>Tanggal</span><span class="text-right">Jumlah</span><span class="text-center">Status</span>
                </div>
                @foreach($pSched as $s)
                <div class="grid grid-cols-4 items-center px-3 py-2 text-xs border-t" style="border-color:#f0f7ee;">
                    <span class="w-5 h-5 rounded-full inline-flex items-center justify-center font-bold text-white"
                          style="background:{{ $s['lunas'] ? 'var(--green)' : '#d1d5db' }}; font-size:10px;">{{ $s['no'] }}</span>
                    <div><p>{{ $s['date'] }}</p><p class="text-gray-400">{{ $s['label'] }}</p></div>
                    <span class="text-right font-semibold text-mony-text">Rp {{ number_format($s['amount'], 0, ',', '.') }}</span>
                    <span class="text-center">
                        <span class="px-1.5 py-0.5 rounded-full text-xs font-semibold {{ $s['lunas'] ? 'badge-success' : 'badge-warning' }}">
                            {{ $s['lunas'] ? 'Lunas' : 'Tunggu' }}
                        </span>
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Riwayat Pembayaran Dikonfirmasi: semua pembayaran aktual (bunga + tebus) --}}
        @if($confirmedPayments->count())
        <div class="mx-4 mt-3">
            <p class="text-xs font-semibold text-mony-text mb-2 flex items-center gap-1.5">
                <span class="w-0.5 h-3.5 rounded-full inline-block" style="background:var(--green);"></span>
                Riwayat Pembayaran Dikonfirmasi
            </p>
            <div class="rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="grid text-xs font-semibold text-mony-muted px-3 py-2" style="background:#f5faf3; grid-template-columns:auto 1fr auto auto;">
                    <span class="pr-3">Tipe</span><span></span><span class="text-right pr-3">Jumlah</span><span class="text-right">Tgl. Konfirmasi</span>
                </div>
                @foreach($confirmedPayments as $pay)
                <div class="grid items-center px-3 py-2.5 text-xs border-t gap-2" style="border-color:#f0f7ee; grid-template-columns:auto 1fr auto auto;">
                    <span class="badge-{{ $pay->payment_type === 'tebus' ? 'info' : 'success' }} whitespace-nowrap">
                        {{ $pay->payment_type === 'tebus' ? 'Pelunasan' : 'Bunga' }}
                    </span>
                    <span></span>
                    <span class="text-right font-semibold text-mony-text whitespace-nowrap">
                        Rp {{ number_format($pay->amount, 0, ',', '.') }}
                    </span>
                    <span class="text-right text-mony-muted whitespace-nowrap">
                        {{ $pay->confirmed_at?->format('d M Y') ?? '-' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Dokumen Pendukung --}}
        @if(count($pDocs))
        <div class="mx-4 mt-3">
            <p class="text-xs font-semibold text-mony-text mb-2 flex items-center gap-1.5">
                <span class="w-0.5 h-3.5 rounded-full inline-block" style="background:var(--green);"></span>
                Dokumen Pendukung
            </p>
            <div class="flex flex-wrap gap-2">
                @foreach($pDocs as $doc)
                <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $doc) }}', type: 'doc' }"
                     class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium cursor-pointer transition-colors"
                     style="background:#f5faf3; border:1px solid #ddebd5; color:var(--green);"
                     onmouseover="this.style.background='#eaf3de'"
                     onmouseout="this.style.background='#f5faf3'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Dokumen {{ $loop->iteration }}
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Ketentuan Pelelangan: hanya untuk transaksi aktif --}}
        @if($pIsActive)
        <div class="mx-4 mt-3 p-4 rounded-2xl border border-amber-200 bg-amber-50">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-xs font-bold text-amber-700">Ketentuan Pelelangan</p>
            </div>
            <ol class="text-xs text-amber-700 space-y-1.5" style="padding-left:1rem; list-style:decimal;">
                <li>Bunga <strong>wajib dibayar</strong> setiap bulan.</li>
                <li>Setiap bunga dikonfirmasi, jatuh tempo otomatis <strong>diperpanjang 4 bulan</strong> dari tanggal konfirmasi.</li>
                <li>Jika <strong>tidak membayar bunga selama 4 bulan berturut-turut</strong> setelah pembayaran terakhir, barang akan diproses untuk dilelang.</li>
                <li>Ingin menebus barang: cukup bayar pokok + bunga bulan berjalan. Hubungi pengurus lebih awal.</li>
            </ol>
        </div>
        @endif

        {{-- Footer --}}
        <div class="px-4 pt-3 pb-5">
            <button type="button" @click="openModal = null" class="btn-primary w-full justify-center py-3 text-sm">
                Tutup
            </button>
        </div>

    </div>
</div>