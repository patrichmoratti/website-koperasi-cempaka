{{-- Global lightbox overlay — uses Alpine.store('lb'). Mounted once per layout. --}}
<div x-data x-show="$store.lb.show" x-cloak
     @click="$store.lb.show = false"
     @keydown.escape.window="$store.lb.show = false"
     x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
     style="background:rgba(0,0,0,0.96);">
    <button @click.stop="$store.lb.show = false"
            class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center rounded-full text-white z-10 transition-colors"
            style="background:rgba(255,255,255,0.12);"
            onmouseover="this.style.background='rgba(255,255,255,0.24)'"
            onmouseout="this.style.background='rgba(255,255,255,0.12)'">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <template x-if="$store.lb.type === 'image'">
        <img :src="$store.lb.src" alt="" @click.stop
             class="max-w-full max-h-[92vh] rounded-2xl shadow-2xl select-none"
             style="object-fit:contain; background:#111;">
    </template>
    <template x-if="$store.lb.type === 'video'">
        <video :src="$store.lb.src" controls autoplay playsinline @click.stop
               class="max-h-[88vh] rounded-2xl shadow-2xl"
               style="max-width:min(880px,96vw);"></video>
    </template>
    <template x-if="$store.lb.type === 'doc'">
        <div @click.stop class="rounded-2xl overflow-hidden shadow-2xl"
             style="width:min(780px,96vw); height:85vh; background:#fff;">
            <iframe :src="$store.lb.src" class="w-full h-full border-0"></iframe>
        </div>
    </template>
</div>