{{--
    Branded "payment detail" popup — shown when a riwayat pembayaran card
    (simpanan or gadai) is clicked. Expects the enclosing x-data scope to expose:
      detailOpen  (boolean)
      detailData  ({ title, subtitle, status, statusLabel, rows: [[label, value], ...], note })
--}}
<div x-show="detailOpen" x-cloak
     class="fixed inset-0 z-[60] flex items-center justify-center p-4"
     @click.self="detailOpen = false"
     @keydown.escape.window="if(!$store.lb?.show){ detailOpen = false }"
     style="background:rgba(8,20,12,0.72);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);"
     x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="popup-sheet bg-white w-full shadow-2xl scrollbar-hide"
         style="max-width:480px;max-height:90vh;overflow-y:auto;border-radius:24px;"
         @click.stop
         x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

        {{-- Branded header --}}
        <div class="overflow-hidden rounded-t-3xl px-6 py-5" style="background:linear-gradient(135deg,var(--green) 0%,var(--green2) 100%);">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:rgba(255,255,255,0.18)">
                        <img src="{{ asset('images/logo-mony.png') }}" alt="MONY" class="w-7 h-7 object-contain">
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-white leading-tight" x-text="detailData.title"></h2>
                        <p class="text-xs mt-0.5 truncate" style="color:rgba(255,255,255,0.65)" x-text="detailData.subtitle"></p>
                    </div>
                </div>
                <button type="button" @click="detailOpen = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full flex-shrink-0"
                        style="background:rgba(255,255,255,0.15)"
                        onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div class="px-6 pt-7 pb-6 space-y-4">
            {{-- Status icon + headline --}}
            <div class="flex flex-col items-center text-center pt-1 pb-1">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4"
                     :style="detailData.status === 'confirmed' ? 'background:var(--green-light)' : (detailData.status === 'rejected' ? 'background:#fee2e2' : (detailData.status === 'pending' ? 'background:#ffedd5' : 'background:var(--green-light)'))">
                    {{-- confirmed: check --}}
                    <svg x-show="detailData.status === 'confirmed'" class="w-8 h-8" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    {{-- rejected: x --}}
                    <svg x-show="detailData.status === 'rejected'" class="w-8 h-8" style="color:#dc2626" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    {{-- pending: clock --}}
                    <svg x-show="detailData.status === 'pending'" class="w-8 h-8" style="color:#c2410c" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{-- other (e.g. gadai status): info/document --}}
                    <svg x-show="!['confirmed','rejected','pending'].includes(detailData.status)" class="w-8 h-8" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-mony-text" x-text="detailData.statusLabel"></p>
                <p class="text-xs text-mony-muted mt-1.5 max-w-[290px] leading-relaxed">Detail riwayat transaksi kamu</p>
            </div>

            {{-- Summary table --}}
            <div class="rounded-2xl overflow-hidden" style="border:1px solid #ddebd5">
                <div class="px-4 py-2.5" style="background:#f5faf3;border-bottom:1px solid #ddebd5">
                    <p class="text-xs font-semibold text-mony-text">Ringkasan</p>
                </div>
                <template x-for="(row, ri) in detailData.rows" :key="ri">
                    <div class="flex justify-between items-center px-4 py-2.5 text-xs"
                         :style="ri < detailData.rows.length - 1 ? 'border-bottom:1px solid #f0f7ee' : ''">
                        <span class="text-mony-muted" x-text="row[0]"></span>
                        <span class="font-semibold text-mony-text text-right" x-text="row[1]"></span>
                    </div>
                </template>
            </div>

            {{-- Status notice (only for pending/confirmed/rejected — gadai transaction statuses use the link below instead) --}}
            <div x-show="['pending','confirmed','rejected'].includes(detailData.status)"
                 class="rounded-xl px-4 py-3.5 text-xs flex items-start gap-3"
                 :style="detailData.status === 'confirmed'
                    ? 'background:#f0fdf4;border:1px solid #bbf7d0;color:#166534'
                    : (detailData.status === 'rejected'
                        ? 'background:#fef2f2;border:1px solid #fecaca;color:#b91c1c'
                        : 'background:#fff7ed;border:1px solid #fed7aa;color:#9a3412')">
                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                     :style="detailData.status === 'confirmed'
                        ? 'background:rgba(22,101,52,0.12)'
                        : (detailData.status === 'rejected' ? 'background:rgba(185,28,28,0.12)' : 'background:rgba(194,65,12,0.12)')">
                    <svg x-show="detailData.status === 'confirmed'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <svg x-show="detailData.status === 'rejected'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <svg x-show="detailData.status === 'pending'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="leading-relaxed pt-0.5">
                    <template x-if="detailData.status === 'confirmed'">
                        <span>Pembayaran ini telah <strong>dikonfirmasi</strong> oleh pengurus.<template x-if="detailData.note"> <span x-text="detailData.note"></span>.</template></span>
                    </template>
                    <template x-if="detailData.status === 'rejected'">
                        <span>Pembayaran ini <strong>ditolak</strong> oleh pengurus.<template x-if="detailData.note"> Alasan: <span x-text="detailData.note"></span>.</template></span>
                    </template>
                    <template x-if="detailData.status === 'pending'">
                        <span>Status saat ini: <strong>Menunggu Konfirmasi</strong>. Pantau perkembangannya melalui menu &ldquo;Riwayat Pembayaran&rdquo; pada Keuangan Saya.</span>
                    </template>
                </span>
            </div>

            <div class="flex gap-3">
                <button type="button" @click="detailOpen = false" class="btn-secondary flex-1 py-3 text-sm" x-show="detailData.link">
                    Tutup
                </button>
                <a :href="detailData.link" x-show="detailData.link" class="btn-primary flex-1 py-3 text-sm justify-center">
                    Lihat Detail Lengkap
                </a>
                <button type="button" @click="detailOpen = false" class="btn-primary w-full py-3 text-sm" x-show="!detailData.link">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>