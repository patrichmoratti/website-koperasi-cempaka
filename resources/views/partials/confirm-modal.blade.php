{{--
    Global small confirmation popup — replaces native browser confirm().
    Triggered via window.confirmAction(event, 'Pesan...') from any onclick/onsubmit.
    Included once per layout (admin, pengurus, anggota).
--}}
<div x-data="{ show: false, message: '', confirmLabel: 'Ya, Lanjutkan', _onConfirm: null }"
     @open-confirm-modal.window="
        message = $event.detail.message;
        confirmLabel = $event.detail.confirmLabel || 'Ya, Lanjutkan';
        _onConfirm = $event.detail.onConfirm;
        show = true;
     "
     x-show="show" x-cloak
     @click.self="show = false"
     @keydown.escape.window="show = false"
     class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="background: rgba(15,35,25,0.6)">
    <div @click.stop
         x-show="show"
         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="relative w-full max-w-xs bg-white rounded-2xl p-5 text-center space-y-4 shadow-xl">
        <p class="text-sm text-mony-text" x-text="message"></p>
        <div class="flex gap-3">
            <button type="button" @click="show = false" class="btn-outline btn-sm flex-1">Batal</button>
            <button type="button" @click="show = false; if (_onConfirm) _onConfirm()" class="btn-success btn-sm flex-1" x-text="confirmLabel"></button>
        </div>
    </div>
</div>