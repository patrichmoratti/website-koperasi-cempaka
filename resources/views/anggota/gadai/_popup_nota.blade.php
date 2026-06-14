{{--
    "Nota Transaksi Digital" popup — branded gadai detail sheet shared by
    Gadai Saya and Dashboard. Expects:
      $t     (TransaksiGadai, with jenisBarang & pengajuan loaded)
    Enclosing scope must expose Alpine boolean `open`.
--}}
@php
    $tPhotos  = !empty($t->pengajuan->item_photo_paths) ? $t->pengajuan->item_photo_paths : ($t->item_photo_paths ?? []);
    $tMonths  = (int) $t->pawn_date->diffInMonths($t->due_date);
    $schedule = $t->paymentSchedule();
@endphp
<div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @click.self="open = false" @keydown.escape.window="if(!$store.lb.show){ open = false }"
     style="background:rgba(8,20,12,0.72); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);"
     x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
<div class="bg-white popup-sheet w-full shadow-2xl scrollbar-hide" style="max-width:620px; max-height:88vh; overflow-y:auto; border-radius:24px;"
             @click.stop x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        {{-- Header --}}
        <div class="relative overflow-hidden rounded-t-3xl px-6 py-5" style="background:linear-gradient(135deg, var(--green) 0%, var(--green2) 100%);">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:rgba(255,255,255,0.15);">
                        <img src="{{ asset('images/logo-mony.png') }}" alt="MONY" class="w-7 h-7 object-contain">
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white leading-tight">Nota Transaksi Digital</h2>
                        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6);">Gadai aktif sejak {{ $t->pawn_date->format('d M Y') }}</p>
                    </div>
                </div>
                <button @click="open = false" class="w-8 h-8 flex items-center justify-center rounded-full flex-shrink-0 transition-colors" style="background:rgba(255,255,255,0.15);" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        @if(count($tPhotos))
        <div class="grid grid-cols-3 gap-2 px-4 mt-4">
            @foreach(array_slice($tPhotos, 0, 3) as $photo)
            <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/'.$photo) }}', type: 'image' }"
                 class="rounded-xl overflow-hidden cursor-pointer group" style="aspect-ratio:1">
                <img src="{{ asset('storage/'.$photo) }}" class="w-full h-full object-cover group-hover:opacity-85 transition-opacity">
            </div>
            @endforeach
        </div>
        @endif
        @if(!empty($t->pengajuan?->item_video_path))
        <div class="px-4 mt-3">
            <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/'.$t->pengajuan->item_video_path) }}', type: 'video' }"
                 class="relative rounded-2xl overflow-hidden cursor-pointer group" style="background:#111; aspect-ratio:16/9; max-height:170px;">
                <video src="{{ asset('storage/'.$t->pengajuan->item_video_path) }}" class="w-full h-full object-contain" preload="metadata" muted></video>
                <div class="absolute inset-0 flex items-center justify-center" style="background:rgba(0,0,0,0.32);">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform" style="background:rgba(255,255,255,0.92);">
                        <svg class="w-5 h-5 ml-0.5" style="color:var(--green)" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>
            </div>
            <p class="text-xs text-mony-muted mt-1.5 text-center">Video barang — klik untuk putar</p>
        </div>
        @endif
        <div class="mx-4 mt-4 py-2.5 rounded-xl text-center border-2 border-dashed" style="border-color:var(--green);">
            <p class="text-xs text-mony-muted mb-0.5">Nomor Referensi</p>
            <p class="font-bold text-sm tracking-wide" style="color:var(--green);">{{ $t->reference_number }}</p>
        </div>
        {{-- Detail Barang --}}
        <div class="mx-4 mt-3 rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
            <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;"><p class="text-xs font-semibold text-mony-text">Detail Barang Jaminan</p></div>
            @php $tRows = [['Jenis Barang',$t->jenisBarang?->name??'-'],['Merk Barang',$t->pengajuan?->brand_name??$t->item_description??'-'],['Nilai Taksiran','Rp '.number_format($t->appraisal_value,0,',','.')],['Nilai Pinjaman','Rp '.number_format($t->loan_amount,0,',','.')],['Bunga/bln','Rp '.number_format($t->monthlyInterest(),0,',','.')],['Mulai Gadai',$t->pawn_date->format('d M Y')],['Durasi Gadai',$tMonths.' bulan'],['Tanggal Penebusan',$t->due_date->format('d M Y')]]; @endphp
            @foreach($tRows as $i=>[$label,$val])
            <div class="flex justify-between items-center px-4 py-2 text-xs {{ $i < count($tRows)-1 ? 'border-b' : '' }}" style="{{ $i < count($tRows)-1 ? 'border-color:#f0f7ee;' : '' }}">
                <span class="text-mony-muted">{{ $label }}</span>
                <span class="font-semibold text-mony-text">{{ $val }}</span>
            </div>
            @endforeach
        </div>
        {{-- Ringkasan Pinjaman Grid --}}
        <div class="mx-4 mt-3 grid grid-cols-2 gap-2">
            @php $gridItems = [['Jumlah Pinjaman','Rp '.number_format($t->loan_amount/1000000,1).' jt'],['Bunga per bulan',$t->interest_rate.'%/bln'],['Cicilan Bunga/bln','Rp '.number_format($t->monthlyInterest()/1000,0,',','.').' rb'],['Durasi Gadai',$tMonths.' bulan']]; @endphp
            @foreach($gridItems as [$glabel, $gval])
            <div class="rounded-xl p-3" style="background:var(--green-light);">
                <p class="text-xs text-mony-muted mb-0.5">{{ $glabel }}</p>
                <p class="font-bold text-sm" style="color:var(--green);">{{ $gval }}</p>
            </div>
            @endforeach
        </div>
        {{-- Jadwal Pembayaran --}}
        @if(count($schedule))
        <div class="mx-4 mt-3">
            <p class="text-xs font-semibold text-mony-text mb-2 flex items-center gap-1.5">
                <span class="w-0.5 h-3.5 rounded-full inline-block" style="background:var(--green);"></span>
                Jadwal Pembayaran Bunga
            </p>
            <div class="rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="grid grid-cols-4 text-xs font-semibold text-mony-muted px-3 py-2" style="background:#f5faf3;">
                    <span>#</span><span>Tanggal</span><span class="text-right">Jumlah</span><span class="text-center">Status</span>
                </div>
                @foreach($schedule as $s)
                <div class="grid grid-cols-4 items-center px-3 py-2 text-xs border-t" style="border-color:#f0f7ee;">
                    <span class="w-5 h-5 rounded-full inline-flex items-center justify-center font-bold text-white" style="background:{{ $s['lunas'] ? 'var(--green)' : '#d1d5db' }}; font-size:10px;">{{ $s['no'] }}</span>
                    <div><p>{{ $s['date'] }}</p><p class="text-gray-400">{{ $s['label'] }}</p></div>
                    <span class="text-right font-semibold text-mony-text">Rp {{ number_format($s['amount'],0,',','.') }}</span>
                    <span class="text-center"><span class="px-1.5 py-0.5 rounded-full text-xs font-semibold {{ $s['lunas'] ? 'badge-success' : 'badge-warning' }}">{{ $s['lunas'] ? 'Lunas' : 'Tunggu' }}</span></span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        {{-- Ketentuan --}}
        <div class="mx-4 mt-3 mb-3 p-4 rounded-2xl border border-amber-200 bg-amber-50">
            <div class="flex items-center gap-2 mb-2"><svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg><p class="text-xs font-bold text-amber-700">Ketentuan Pelelangan</p></div>
            <ol class="text-xs text-amber-700 space-y-1.5" style="padding-left:1rem; list-style:decimal;">
                <li>Bunga <strong>wajib dibayar</strong> setiap bulan.</li>
                <li>Setiap bunga dikonfirmasi, jatuh tempo otomatis <strong>diperpanjang 4 bulan</strong> dari tanggal konfirmasi.</li>
                <li>Jika <strong>tidak membayar bunga selama 4 bulan berturut-turut</strong> setelah pembayaran terakhir, barang akan diproses untuk dilelang. Contoh: bayar bunga Februari → jika tidak ada pembayaran hingga Juni, barang dilelang.</li>
                <li>Ingin menebus barang: cukup bayar pokok + bunga bulan berjalan. Hubungi pengurus lebih awal.</li>
            </ol>
        </div>
        @if($t->status === 'aktif')
        <div class="px-4 pb-5">
            <a href="{{ route('anggota.gadai.bayar', $t) }}" class="btn-primary w-full flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Bayar Gadai
            </a>
        </div>
        @else
        <div class="pb-5"></div>
        @endif
    </div>
</div>