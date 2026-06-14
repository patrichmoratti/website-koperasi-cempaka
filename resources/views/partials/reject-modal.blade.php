{{--
    Global small "Tolak" (reject-with-reason) popup — replaces inline textarea panels.
    Triggered via window.openRejectModal('Alasan penolakan untuk ...', actionUrl).
    Included once per layout (admin, pengurus).
--}}
<div x-data="{ show: false, message: '', actionUrl: '' }"
     @open-reject-modal.window="
        message = $event.detail.message;
        actionUrl = $event.detail.actionUrl;
        show = true;
        $nextTick(() => $refs.rejectReason && $refs.rejectReason.focus());
     "
     x-show="show" x-cloak
     @click.self="show = false"
     @keydown.escape.window="show = false"
     class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="background: rgba(15,35,25,0.6)">
    <div @click.stop
         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="relative w-full max-w-sm bg-white rounded-2xl p-5 space-y-3 shadow-xl text-left">
        <p class="text-sm font-medium text-mony-text" x-text="message"></p>
        <form method="POST" :action="actionUrl" @submit="show = false">
            @csrf
            <textarea x-ref="rejectReason" name="reason" rows="3" class="form-input text-sm mb-3" placeholder="Tuliskan alasan penolakan..." required></textarea>
            <div class="flex gap-3">
                <button type="button" @click="show = false" class="btn-outline btn-sm flex-1">Batal</button>
                <button type="submit" class="btn-danger btn-sm flex-1">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>