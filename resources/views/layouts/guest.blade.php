<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MONY') }} — {{ $title ?? 'KSP Cempaka' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex" style="background: var(--cream)">

    {{-- Left panel (decorative, hidden on mobile) --}}
    <div class="hidden lg:flex lg:w-2/5 xl:w-1/2 flex-col justify-between p-10 relative overflow-hidden"
         style="background: var(--green); min-height: 100vh">

        {{-- Decorative blobs --}}
        <div class="absolute top-0 right-0 w-80 h-80 rounded-full opacity-10 translate-x-16 -translate-y-16"
             style="background: var(--gold)"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full opacity-10 -translate-x-16 translate-y-16"
             style="background: var(--gold)"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full opacity-5"
             style="background: white"></div>

        {{-- Top: Logo --}}
        <div class="relative flex items-center gap-3">
            <img src="{{ asset('images/logo-mony.png') }}" alt="Logo MONY" class="w-9 h-9 rounded-xl object-contain">
            <div>
                <span class="font-display font-bold text-xl text-white">MONY</span>
                <p class="text-xs" style="color: rgba(255,255,255,0.4)">KSP Cempaka</p>
            </div>
        </div>

        {{-- Middle: Content --}}
        <div class="relative space-y-6 max-w-xs">
            <h2 class="font-display font-bold text-3xl text-white leading-snug">
                Gadai Mudah,<br>
                <span class="gradient-text-gold">Aman & Transparan</span>
            </h2>
            <div class="w-12 h-0.5 rounded" style="background: var(--gold); opacity: 0.6"></div>
            <p class="text-sm leading-relaxed" style="color: rgba(255,255,255,0.6)">
                Platform gadai digital koperasi simpan pinjam yang modern. Proses cepat, bunga kompetitif, dan barang Anda terjamin aman.
            </p>

            <div class="space-y-3">
                @foreach(['Taksiran hingga 85% nilai barang', 'Bunga tetap 8% per bulan', 'Notifikasi otomatis & real-time', 'Simpanan anggota terkelola rapi'] as $item)
                <div class="flex items-center gap-3">
                    <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0"
                         style="background: rgba(201,168,76,0.2)">
                        <svg class="w-3 h-3" style="color: var(--gold)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-sm" style="color: rgba(255,255,255,0.65)">{{ $item }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Bottom: Back link --}}
        <div class="relative">
            <a href="{{ route('landing') }}"
               class="inline-flex items-center gap-2 text-sm transition-colors"
               style="color: rgba(255,255,255,0.4)"
               onmouseover="this.style.color='var(--gold)'"
               onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>

    {{-- Right panel: Form --}}
    <div class="flex-1 flex flex-col items-center justify-center p-6 sm:p-10 overflow-y-auto"
         style="background: var(--cream)">

        {{-- Mobile logo --}}
        <div class="lg:hidden text-center mb-8 w-full">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-sm mb-4"
               style="color: var(--text-muted)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Beranda
            </a>
            <div class="flex items-center justify-center gap-3">
                <img src="{{ asset('images/logo-mony.png') }}" alt="Logo MONY" class="w-9 h-9 rounded-xl object-contain">
                <div class="text-left">
                    <p class="font-display font-bold text-xl" style="color: var(--green)">MONY</p>
                    <p class="text-xs" style="color: var(--text-muted)">KSP Cempaka</p>
                </div>
            </div>
        </div>

        {{-- Form card --}}
        <div class="w-full max-w-md">
            <div class="rounded-3xl p-8 sm:p-10"
                 style="background: white; box-shadow: 0 8px 40px rgba(26,61,46,0.08); border: 1px solid rgba(26,61,46,0.06)">
                {{ $slot }}
            </div>
        </div>

        <p class="mt-6 text-center text-xs" style="color: var(--text-muted)">
            &copy; {{ date('Y') }} KSP Cempaka. Semua hak dilindungi.
        </p>
    </div>

</body>
</html>
