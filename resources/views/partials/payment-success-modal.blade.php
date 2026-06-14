{{--
    Branded "payment submitted" popup — shown after a simpanan/gadai payment form
    is sent via AJAX. Expects the enclosing x-data scope to expose:
      successOpen  (boolean)
      successData  ({ title, subtitle, rows: [[label, value], ...] })
--}}
<div x-show="successOpen" x-cloak
     class="fixed inset-0 z-[60] flex items-center justify-center p-4"
     @click.self="successOpen = false"
     @keydown.escape.window="if(!$store.lb?.show){ successOpen = false }"
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
                        <h2 class="text-base font-bold text-white leading-tight" x-text="successData.title"></h2>
                        <p class="text-xs mt-0.5 truncate" style="color:rgba(255,255,255,0.65)" x-text="successData.subtitle"></p>
                    </div>
                </div>
                <button type="button" @click="successOpen = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full flex-shrink-0"
                        style="background:rgba(255,255,255,0.15)"
                        onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div class="px-6 pt-7 pb-6 space-y-4">
            {{-- Success icon + headline --}}
            <div class="flex flex-col items-center text-center pt-1 pb-1">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4" style="background:var(--green-light)">
                    <svg class="w-8 h-8" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="text-sm font-semibold text-mony-text">Bukti pembayaran berhasil dikirim</p>
                <p class="text-xs text-mony-muted mt-1.5 max-w-[290px] leading-relaxed">Tim koperasi akan memverifikasi dalam 1&times;24 jam kerja</p>
            </div>

            {{-- Summary table --}}
            <div class="rounded-2xl overflow-hidden" style="border:1px solid #ddebd5">
                <div class="px-4 py-2.5" style="background:#f5faf3;border-bottom:1px solid #ddebd5">
                    <p class="text-xs font-semibold text-mony-text">Ringkasan Pembayaran</p>
                </div>
                <template x-for="(row, ri) in successData.rows" :key="ri">
                    <div class="flex justify-between items-center px-4 py-2.5 text-xs"
                         :style="ri < successData.rows.length - 1 ? 'border-bottom:1px solid #f0f7ee' : ''">
                        <span class="text-mony-muted" x-text="row[0]"></span>
                        <span class="font-semibold text-mony-text text-right" x-text="row[1]"></span>
                    </div>
                </template>
            </div>

            {{-- Status notice --}}
            <div class="rounded-xl px-4 py-3.5 text-xs flex items-start gap-3" style="background:#fff7ed;border:1px solid #fed7aa;color:#9a3412">
                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0" style="background:rgba(194,65,12,0.12)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="leading-relaxed pt-0.5">Status saat ini: <strong>Menunggu Konfirmasi</strong>. Pantau perkembangannya melalui menu &ldquo;Riwayat Pembayaran&rdquo; pada Keuangan Saya.</span>
            </div>

            <button type="button" @click="successOpen = false" class="btn-primary w-full py-3 text-sm">
                Mengerti
            </button>
        </div>
    </div>
</div>