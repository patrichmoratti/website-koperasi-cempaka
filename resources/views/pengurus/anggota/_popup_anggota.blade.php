{{--
    Detail anggota popup for pengurus Daftar Anggota page.
    Accepts: $user (User with transaksiGadai & simpanan loaded)
    Enclosing Alpine scope must expose: openModal (string|null)
--}}
@php
    $statusColors = ['pending'=>'warning','active'=>'success','rejected'=>'danger','suspended'=>'gray'];
    $gadaiAktif   = $user->transaksiGadai->whereIn('status', ['aktif','menunggu_lelang']);
    $totalPinjaman = $gadaiAktif->sum('loan_amount');
@endphp
<div x-show="openModal === 'anggota-{{ $user->id }}'" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @click.self="openModal = null"
     @keydown.escape.window="if(!$store.lb?.show){ openModal = null }"
     style="background:rgba(8,20,12,0.72); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);"
     x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    <div @click.stop
         class="bg-white popup-sheet w-full shadow-2xl scrollbar-hide"
         style="max-width:520px; max-height:88vh; overflow-y:auto; border-radius:24px;"
         x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

        {{-- Branded Header --}}
        <div class="relative overflow-hidden rounded-t-3xl px-6 py-5"
             style="background:linear-gradient(135deg, var(--green) 0%, var(--green2) 100%);">
            <div class="absolute inset-0 opacity-[0.05]"
                 style="background-image:radial-gradient(circle,#fff 1px,transparent 1px); background-size:14px 14px;"></div>
            <div class="relative flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 text-xl font-bold text-white"
                         style="background:rgba(255,255,255,0.2);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white leading-tight">{{ $user->name }}</h2>
                        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6);">
                            Bergabung {{ $user->created_at->format('d M Y') }}
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

        <div class="px-5 pt-5 pb-2 space-y-4">

            {{-- Status Badge --}}
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-mony-muted">Status Akun</span>
                <span class="badge-{{ $statusColors[$user->account_status] ?? 'gray' }}">
                    {{ ucfirst($user->account_status) }}
                </span>
            </div>

            {{-- Data Diri --}}
            <div class="rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                    <p class="text-xs font-semibold text-mony-text">Data Diri</p>
                </div>
                @php
                $dataRows = [
                    ['Nama Lengkap', $user->name],
                    ['NIK',          $user->nik ?? '-'],
                    ['Email',        $user->email],
                    ['No. HP',       $user->phone ?? '-'],
                    ['Alamat',       $user->address ?? '-'],
                ];
                @endphp
                @foreach($dataRows as $i => [$label, $val])
                <div class="flex justify-between items-start px-4 py-2.5 text-xs {{ $i < count($dataRows)-1 ? 'border-b' : '' }}"
                     style="{{ $i < count($dataRows)-1 ? 'border-color:#f0f7ee;' : '' }}">
                    <span class="text-mony-muted flex-shrink-0 mr-4">{{ $label }}</span>
                    <span class="font-semibold text-mony-text text-right {{ $label === 'NIK' ? 'font-mono' : '' }}">{{ $val }}</span>
                </div>
                @endforeach
            </div>

            {{-- Foto KYC --}}
            @if($user->ktp_photo || $user->selfie_photo)
            <div class="rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                    <p class="text-xs font-semibold text-mony-text">Foto KYC</p>
                </div>
                <div class="p-3 grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-mony-muted mb-1.5">KTP</p>
                        @if($user->ktp_photo)
                            <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $user->ktp_photo) }}', type: 'image' }"
                                 class="rounded-xl overflow-hidden cursor-pointer group" style="aspect-ratio:16/10;">
                                <img src="{{ asset('storage/' . $user->ktp_photo) }}"
                                     class="w-full h-full object-cover group-hover:opacity-85 transition-opacity">
                            </div>
                        @else
                            <div class="rounded-xl bg-gray-100 flex items-center justify-center text-xs text-mony-muted" style="aspect-ratio:16/10;">Belum ada</div>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-mony-muted mb-1.5">Selfie</p>
                        @if($user->selfie_photo)
                            <div @click.stop="$store.lb = { show: true, src: '{{ asset('storage/' . $user->selfie_photo) }}', type: 'image' }"
                                 class="rounded-xl overflow-hidden cursor-pointer group" style="aspect-ratio:16/10;">
                                <img src="{{ asset('storage/' . $user->selfie_photo) }}"
                                     class="w-full h-full object-cover group-hover:opacity-85 transition-opacity">
                            </div>
                        @else
                            <div class="rounded-xl bg-gray-100 flex items-center justify-center text-xs text-mony-muted" style="aspect-ratio:16/10;">Belum ada</div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Ringkasan Gadai & Simpanan --}}
            <div class="rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                    <p class="text-xs font-semibold text-mony-text">Ringkasan Gadai & Simpanan</p>
                </div>
                @php
                $ringkasanRows = [
                    ['Gadai Aktif',          $gadaiAktif->count() . ' transaksi'],
                    ['Total Pinjaman Aktif',  'Rp ' . number_format($totalPinjaman, 0, ',', '.')],
                    ['Simpanan Pokok',        'Rp ' . number_format($user->totalSimpananPokok(), 0, ',', '.')],
                    ['Simpanan Wajib',        'Rp ' . number_format($user->totalSimpananWajib(), 0, ',', '.')],
                    ['Total Simpanan',        'Rp ' . number_format($user->totalSimpanan(), 0, ',', '.')],
                ];
                @endphp
                @foreach($ringkasanRows as $i => [$label, $val])
                <div class="flex justify-between items-center px-4 py-2.5 text-xs {{ $i < count($ringkasanRows)-1 ? 'border-b' : '' }}"
                     style="{{ $i < count($ringkasanRows)-1 ? 'border-color:#f0f7ee;' : '' }}">
                    <span class="text-mony-muted">{{ $label }}</span>
                    <span class="font-semibold {{ $label === 'Total Simpanan' ? '' : 'text-mony-text' }}"
                          @if($label === 'Total Simpanan') style="color:var(--green);" @endif>{{ $val }}</span>
                </div>
                @endforeach
            </div>

            {{-- Gadai Aktif list --}}
            @if($gadaiAktif->count())
            <div class="rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                    <p class="text-xs font-semibold text-mony-text">Transaksi Gadai Aktif</p>
                </div>
                @foreach($gadaiAktif as $trx)
                <div class="flex items-center justify-between px-4 py-2.5 text-xs {{ !$loop->last ? 'border-b' : '' }}"
                     style="{{ !$loop->last ? 'border-color:#f0f7ee;' : '' }}">
                    <div>
                        <p class="font-semibold text-mony-text font-mono">{{ $trx->reference_number }}</p>
                        <p class="text-mony-muted">{{ $trx->jenisBarang?->name ?? '-' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-mony-text">Rp {{ number_format($trx->loan_amount, 0, ',', '.') }}</p>
                        <span class="badge-{{ $trx->status_color }}">{{ $trx->status_label }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>

        {{-- Footer --}}
        <div class="px-5 pt-2 pb-5">
            <button type="button" @click="openModal = null" class="btn-primary w-full justify-center py-3 text-sm">
                Tutup
            </button>
        </div>

    </div>
</div>