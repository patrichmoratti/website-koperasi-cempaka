<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MONY — {{ $info->name }}</title>
    <meta name="description" content="Layanan gadai dan simpan pinjam terpercaya. Proses mudah, aman, dan transparan bersama {{ $info->name }}.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ── Landing-specific overrides ── */
        body { background-color: var(--cream); }

        /* Decorative noise overlay */
        .noise-overlay::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4'/%3E%3CfeColorMatrix type='saturate' values='0'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.025'/%3E%3C/svg%3E");
            pointer-events: none;
        }

        /* Hero gradient blob */
        .hero-blob {
            background: radial-gradient(ellipse 70% 60% at 70% 50%, rgba(201,168,76,0.12) 0%, transparent 70%),
                        radial-gradient(ellipse 50% 50% at 20% 80%, rgba(26,61,46,0.08) 0%, transparent 60%);
        }

        /* Green section decorative */
        .green-section {
            background: linear-gradient(135deg, #0f2a1e 0%, #1A3D2E 50%, #2a5a44 100%);
            position: relative;
            overflow: hidden;
        }
        .green-section::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 60%;
            height: 160%;
            background: radial-gradient(ellipse, rgba(201,168,76,0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Marquee strip */
        @keyframes marquee { from { transform: translateX(0) } to { transform: translateX(-50%) } }
        .marquee-inner { animation: marquee 20s linear infinite; }
        .marquee-inner:hover { animation-play-state: paused; }

        /* Gold accent line */
        .gold-line {
            height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold2), var(--gold));
            border-radius: 2px;
        }

        /* Floating card animation */
        @keyframes floatCard {
            0%,100% { transform: translateY(0px) rotate(0deg); }
            25%  { transform: translateY(-8px) rotate(0.5deg); }
            75%  { transform: translateY(-4px) rotate(-0.5deg); }
        }
        .float-card { animation: floatCard 5s ease-in-out infinite; }
        .float-card-slow { animation: floatCard 7s ease-in-out infinite; animation-delay: -2s; }
        .float-card-slower { animation: floatCard 9s ease-in-out infinite; animation-delay: -4s; }

        /* Number counter */
        @keyframes countPulse {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .counter-animate { animation: countPulse .6s ease-out both; }

        /* Navbar scroll state */
        .navbar-scrolled {
            background-color: rgba(245, 240, 232, 0.95) !important;
            backdrop-filter: blur(12px);
            box-shadow: 0 1px 20px rgb(0 0 0 / 0.08);
        }

        /* Step line */
        .step-line::after {
            content: '';
            position: absolute;
            left: 24px;
            top: 56px;
            bottom: -16px;
            width: 2px;
            background: linear-gradient(to bottom, var(--green) 0%, var(--green-light) 100%);
        }
        .step-line:last-child::after { display: none; }
    </style>
</head>

<body x-data="landingApp()" @scroll.window="handleScroll()" class="overflow-x-hidden">

<!-- ═══════════════════════════════════════════════════
     NAVBAR
════════════════════════════════════════════════════ -->
<nav id="navbar"
     class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
     :class="scrolled ? 'navbar-scrolled py-3' : 'py-5'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">

        <!-- Logo -->
        <a href="/" class="flex items-center gap-3 group">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-105"
                 style="background: var(--green)">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="font-display font-bold text-xl tracking-tight" style="color: var(--green)">MONY</span>
                <span class="hidden sm:block text-xs" style="color: var(--text-muted); margin-top: -2px;">KSP Cempaka</span>
            </div>
        </a>

        <!-- Desktop Nav Links -->
        <div class="hidden lg:flex items-center gap-7">
            @foreach(['#tentang' => 'Tentang', '#layanan' => 'Layanan', '#langkah' => 'Cara Gadai', '#barang' => 'Katalog'] as $href => $label)
            <a href="{{ $href }}"
               class="text-sm font-medium transition-colors duration-150"
               style="color: var(--text-muted)"
               onmouseover="this.style.color='var(--green)'"
               onmouseout="this.style.color='var(--text-muted)'">{{ $label }}</a>
            @endforeach
        </div>

        <!-- CTA Buttons -->
        <div class="hidden sm:flex items-center gap-3">
            <a href="{{ route('login') }}"
               class="btn btn-sm font-medium px-4 py-2 rounded-xl border-2 transition-all duration-200"
               style="color: var(--green); border-color: var(--green); background: transparent"
               onmouseover="this.style.background='var(--green)'; this.style.color='white'"
               onmouseout="this.style.background='transparent'; this.style.color='var(--green)'">
                Masuk
            </a>
            <a href="{{ route('register') }}"
               class="btn btn-sm font-semibold px-4 py-2 rounded-xl text-white transition-all duration-200"
               style="background: var(--gold); box-shadow: 0 3px 12px rgba(201,168,76,0.35)"
               onmouseover="this.style.background='var(--gold2)'; this.style.transform='translateY(-1px)'"
               onmouseout="this.style.background='var(--gold)'; this.style.transform='translateY(0)'">
                Daftar Gratis
            </a>
        </div>

        <!-- Mobile Menu Toggle -->
        <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 rounded-lg transition-colors"
                style="color: var(--green)"
                :style="mobileMenu ? 'background: var(--green-light)' : ''">
            <svg x-show="!mobileMenu" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg x-show="mobileMenu" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="lg:hidden absolute top-full left-0 right-0 border-t"
         style="background: var(--cream); border-color: rgba(26,61,46,0.1); box-shadow: 0 8px 24px rgb(0 0 0 / 0.08)"
         style="display:none">
        <div class="max-w-7xl mx-auto px-4 py-4 space-y-1">
            @foreach(['#tentang' => 'Tentang', '#layanan' => 'Layanan', '#langkah' => 'Cara Gadai', '#barang' => 'Katalog'] as $href => $label)
            <a href="{{ $href }}" @click="mobileMenu = false"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors"
               style="color: var(--text)"
               onmouseover="this.style.background='var(--green-light)'; this.style.color='var(--green)'"
               onmouseout="this.style.background='transparent'; this.style.color='var(--text)'">{{ $label }}</a>
            @endforeach
            <div class="flex gap-3 pt-3 border-t" style="border-color: rgba(26,61,46,0.1)">
                <a href="{{ route('login') }}"
                   class="flex-1 btn text-center py-3 rounded-xl text-sm font-medium border-2 transition-all"
                   style="color: var(--green); border-color: var(--green)">Masuk</a>
                <a href="{{ route('register') }}"
                   class="flex-1 btn text-center py-3 rounded-xl text-sm font-semibold text-white transition-all"
                   style="background: var(--gold)">Daftar</a>
            </div>
        </div>
    </div>
</nav>


<!-- ═══════════════════════════════════════════════════
     HERO SECTION
════════════════════════════════════════════════════ -->
<section id="hero" class="hero-blob min-h-screen flex items-center pt-20 relative overflow-hidden">

    <!-- Background circles -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full opacity-5"
             style="background: var(--green)"></div>
        <div class="absolute -bottom-12 -left-12 w-64 h-64 rounded-full opacity-10"
             style="background: var(--gold)"></div>
        <div class="absolute top-1/4 right-1/4 w-2 h-2 rounded-full" style="background: var(--gold); opacity: 0.6"></div>
        <div class="absolute top-1/2 right-1/3 w-1 h-1 rounded-full" style="background: var(--green); opacity: 0.4"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center py-16 lg:py-24">

            <!-- Left: Text -->
            <div class="space-y-8">
                <div class="reveal" data-delay="0">
                    <span class="section-tag">
                        <span class="w-1.5 h-1.5 rounded-full" style="background: var(--gold)"></span>
                        Koperasi Simpan Pinjam Terpercaya
                    </span>
                </div>

                <h1 class="reveal text-4xl sm:text-5xl lg:text-6xl font-display font-bold leading-tight text-balance"
                    data-delay="100"
                    style="color: var(--text)">
                    Gadai Mudah,<br>
                    <span class="gradient-text-gold">Masa Depan</span><br>
                    Lebih Cerah.
                </h1>

                <p class="reveal text-lg leading-relaxed max-w-lg"
                   data-delay="200"
                   style="color: var(--text-muted)">
                    Solusi keuangan terpercaya bagi anggota koperasi. Gadaikan aset berharga Anda dengan proses transparan, cepat, dan bunga kompetitif.
                </p>

                <!-- CTA Buttons -->
                <div class="reveal flex flex-wrap gap-4" data-delay="300">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 px-7 py-3.5 rounded-2xl text-base font-semibold text-white transition-all duration-200"
                       style="background: var(--green); box-shadow: 0 6px 20px rgba(26,61,46,0.3)"
                       onmouseover="this.style.background='var(--green2)'; this.style.transform='translateY(-2px)'"
                       onmouseout="this.style.background='var(--green)'; this.style.transform='translateY(0)'">
                        Daftar Sekarang
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 px-7 py-3.5 rounded-2xl text-base font-medium border-2 transition-all duration-200"
                       style="color: var(--text); border-color: rgba(28,28,26,0.2); background: white"
                       onmouseover="this.style.borderColor='var(--green)'; this.style.color='var(--green)'"
                       onmouseout="this.style.borderColor='rgba(28,28,26,0.2)'; this.style.color='var(--text)'">
                        Sudah punya akun? Masuk
                    </a>
                </div>

                <!-- Trust signals -->
                <div class="reveal flex items-center gap-6 pt-2" data-delay="400">
                    <div class="flex -space-x-2">
                        @for($i=0; $i<5; $i++)
                        <div class="w-8 h-8 rounded-full border-2 border-white flex items-center justify-center text-xs font-bold text-white"
                             style="background: hsl({{ 140 + $i*20 }}, 40%, 35%)">
                            {{ chr(65 + $i) }}
                        </div>
                        @endfor
                    </div>
                    <p class="text-sm" style="color: var(--text-muted)">
                        <span class="font-semibold currency" style="color: var(--green)">{{ number_format($stats['anggota']) }}+</span>
                        anggota aktif telah bergabung
                    </p>
                </div>
            </div>

            <!-- Right: Floating Cards Illustration -->
            <div class="relative hidden lg:flex items-center justify-center h-[520px]">

                <!-- Main card -->
                <div class="float-card absolute w-72 rounded-3xl p-6 shadow-2xl z-20"
                     style="background: var(--green); top: 10%; left: 5%">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs font-medium" style="color: rgba(255,255,255,0.6)">Gadai Aktif</p>
                            <p class="text-2xl font-bold text-white font-mono mt-0.5">{{ number_format($stats['gadai']) }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                             style="background: rgba(201,168,76,0.2)">
                            <svg class="w-6 h-6" style="color: var(--gold)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="h-1 rounded-full" style="background: rgba(255,255,255,0.2)">
                        <div class="h-1 rounded-full w-3/4" style="background: var(--gold)"></div>
                    </div>
                    <p class="text-xs mt-2" style="color: rgba(255,255,255,0.5)">Transaksi terlayani</p>
                </div>

                <!-- Savings card -->
                <div class="float-card-slow absolute w-64 rounded-3xl p-5 shadow-xl z-10"
                     style="background: var(--cream-white); border: 1px solid rgba(26,61,46,0.1); top: 45%; right: 0%">
                    <p class="text-xs font-medium" style="color: var(--text-muted)">Total Simpanan</p>
                    <p class="text-xl font-bold mt-1 font-mono" style="color: var(--green)">
                        Rp {{ number_format($stats['simpanan'] / 1000000, 1) }}jt
                    </p>
                    <div class="flex items-center gap-2 mt-3">
                        <div class="flex gap-1">
                            @for($i=0;$i<5;$i++)
                            <div class="w-6 h-1.5 rounded-full" style="background: {{ $i < 4 ? 'var(--green)' : 'var(--green-light)' }}"></div>
                            @endfor
                        </div>
                        <span class="text-xs" style="color: var(--text-muted)">Terkumpul</span>
                    </div>
                </div>

                <!-- Jenis barang card -->
                <div class="float-card-slower absolute w-56 rounded-3xl p-5 shadow-xl z-30"
                     style="background: var(--gold); bottom: 8%; left: 15%">
                    <p class="text-xs font-medium text-white/70">Jenis Barang Gadai</p>
                    <p class="text-2xl font-bold text-white font-mono mt-1">{{ $stats['jenis'] }}</p>
                    <p class="text-xs text-white/60 mt-1">Kategori tersedia</p>
                    <div class="flex gap-1 mt-3">
                        @foreach($katalog->take(4) as $k)
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold text-white"
                             style="background: rgba(255,255,255,0.25)">
                            {{ strtoupper(substr($k->name, 0, 1)) }}
                        </div>
                        @endforeach
                        @if($katalog->count() > 4)
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold text-white"
                             style="background: rgba(255,255,255,0.15)">+{{ $katalog->count() - 4 }}</div>
                        @endif
                    </div>
                </div>

                <!-- Decorative dots grid -->
                <div class="absolute top-8 right-8 grid grid-cols-5 gap-2 opacity-20">
                    @for($i=0; $i<25; $i++)
                    <div class="w-1.5 h-1.5 rounded-full" style="background: var(--green)"></div>
                    @endfor
                </div>

                <!-- Gold ring -->
                <div class="absolute w-80 h-80 rounded-full border-2 opacity-10 animate-spin-slow"
                     style="border-color: var(--gold); border-style: dashed; top: 50%; left: 50%; transform: translate(-50%, -50%)">
                </div>
            </div>
        </div>
    </div>

    <!-- Stats bar -->
    <div class="absolute bottom-0 left-0 right-0 border-t" style="border-color: rgba(26,61,46,0.1); background: rgba(245,240,232,0.7); backdrop-filter: blur(8px)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 sm:grid-cols-4 divide-x" style="divide-color: rgba(26,61,46,0.1)">
                @foreach([
                    ['label' => 'Anggota Aktif', 'value' => number_format($stats['anggota']), 'suffix' => ''],
                    ['label' => 'Transaksi Gadai', 'value' => number_format($stats['gadai']), 'suffix' => '+'],
                    ['label' => 'Simpanan Terkumpul', 'value' => 'Rp ' . number_format($stats['simpanan']/1000000, 0), 'suffix' => 'jt'],
                    ['label' => 'Jenis Barang', 'value' => $stats['jenis'], 'suffix' => ' Kategori'],
                ] as $stat)
                <div class="px-6 py-4 text-center">
                    <p class="text-xl font-bold font-mono" style="color: var(--green)">{{ $stat['value'] }}{{ $stat['suffix'] }}</p>
                    <p class="text-xs mt-0.5" style="color: var(--text-muted)">{{ $stat['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════
     MARQUEE STRIP — Feature Tags
════════════════════════════════════════════════════ -->
<div class="py-4 overflow-hidden border-y" style="background: var(--green); border-color: rgba(255,255,255,0.1)">
    <div class="marquee-inner whitespace-nowrap inline-flex gap-8">
        @foreach(['Proses Cepat', 'Bunga Kompetitif 8%/bln', 'Aman & Terjamin', 'Mudah & Transparan', 'Bebas Biaya Admin', 'Notifikasi Real-time', 'Proses Digital', 'SHU Tahunan', 'Proses Cepat', 'Bunga Kompetitif 8%/bln', 'Aman & Terjamin', 'Mudah & Transparan', 'Bebas Biaya Admin', 'Notifikasi Real-time', 'Proses Digital', 'SHU Tahunan'] as $tag)
        <span class="inline-flex items-center gap-2 text-sm font-medium" style="color: rgba(255,255,255,0.8)">
            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background: var(--gold)"></span>
            {{ $tag }}
        </span>
        @endforeach
    </div>
</div>


<!-- ═══════════════════════════════════════════════════
     TENTANG / PROFIL KOPERASI
════════════════════════════════════════════════════ -->
<section id="tentang" class="py-24 relative" style="background: var(--cream-white)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            <!-- Left: Green decorative card -->
            <div class="reveal-left relative">
                <div class="relative rounded-3xl overflow-hidden" style="background: var(--green)">
                    <!-- Dots pattern -->
                    <div class="absolute top-6 right-6 grid grid-cols-6 gap-1.5 opacity-20">
                        @for($i=0;$i<36;$i++)
                        <div class="w-1 h-1 rounded-full bg-white"></div>
                        @endfor
                    </div>

                    <div class="relative p-10">
                        <!-- Logo -->
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(201,168,76,0.2)">
                                <svg class="w-5 h-5" style="color: var(--gold)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-white font-display font-bold text-xl">MONY</span>
                        </div>

                        <h3 class="text-white font-display text-2xl font-bold mb-4 leading-tight">
                            {{ $info->name }}
                        </h3>

                        <p class="text-sm leading-relaxed mb-8" style="color: rgba(255,255,255,0.65)">
                            Koperasi simpan pinjam yang berdiri dengan komitmen melayani kebutuhan finansial anggota secara adil, transparan, dan profesional.
                        </p>

                        <!-- Info grid -->
                        <div class="grid grid-cols-2 gap-4">
                            @foreach([
                                ['label' => 'Nomor Rekening', 'value' => $info->bank_account_number ?? '-'],
                                ['label' => 'Bank', 'value' => $info->bank_name ?? '-'],
                                ['label' => 'Telepon', 'value' => $info->phone ?? '-'],
                                ['label' => 'Email', 'value' => Str::limit($info->email ?? '-', 20)],
                            ] as $item)
                            <div class="rounded-2xl p-4" style="background: rgba(255,255,255,0.06)">
                                <p class="text-xs mb-1" style="color: rgba(255,255,255,0.45)">{{ $item['label'] }}</p>
                                <p class="text-sm font-medium text-white font-mono">{{ $item['value'] }}</p>
                            </div>
                            @endforeach
                        </div>

                        <!-- Gold line accent -->
                        <div class="mt-8 h-0.5 rounded-full opacity-30" style="background: linear-gradient(90deg, var(--gold), transparent)"></div>
                        <p class="text-xs mt-3" style="color: rgba(255,255,255,0.35)">
                            {{ $info->address ?? 'Jl. Cempaka Indah, Bandung' }}
                        </p>
                    </div>
                </div>

                <!-- Floating badge -->
                <div class="absolute -bottom-5 -right-5 rounded-2xl p-4 shadow-xl"
                     style="background: var(--gold); min-width: 130px">
                    <p class="text-xs font-medium text-white/70">Berdiri sejak</p>
                    <p class="text-2xl font-bold text-white font-mono">2010</p>
                    <p class="text-xs text-white/60">Melayani anggota</p>
                </div>
            </div>

            <!-- Right: Text content -->
            <div class="reveal-right space-y-6">
                <span class="section-tag">Profil Koperasi</span>

                <h2 class="text-4xl font-display font-bold leading-tight" style="color: var(--text)">
                    Dipercaya Ribuan<br>Anggota Sejak Lama
                </h2>

                <div class="gold-line w-16"></div>

                <p class="text-base leading-relaxed" style="color: var(--text-muted)">
                    {{ $info->name }} hadir sebagai mitra keuangan terpercaya bagi anggota. Dengan sistem yang modern dan pengurus yang berpengalaman, kami memastikan setiap transaksi berjalan dengan aman dan transparan.
                </p>

                <div class="space-y-4">
                    @foreach([
                        ['icon' => '🏆', 'title' => 'Terakreditasi & Terpercaya', 'desc' => 'Beroperasi secara legal dengan pengawasan penuh sesuai regulasi koperasi Indonesia'],
                        ['icon' => '🔒', 'title' => 'Keamanan Barang Terjamin', 'desc' => 'Barang gadai tersimpan di gudang aman dengan monitoring 24 jam'],
                        ['icon' => '💡', 'title' => 'Teknologi Digital Modern', 'desc' => 'Kelola gadai dan simpanan dari mana saja melalui platform MONY'],
                    ] as $item)
                    <div class="reveal flex items-start gap-4 p-4 rounded-2xl transition-all duration-200"
                         style="background: var(--green-light)"
                         onmouseover="this.style.background='var(--cream)'; this.style.boxShadow='0 4px 16px rgba(26,61,46,0.08)'"
                         onmouseout="this.style.background='var(--green-light)'; this.style.boxShadow='none'">
                        <span class="text-2xl flex-shrink-0">{{ $item['icon'] }}</span>
                        <div>
                            <p class="font-semibold text-sm" style="color: var(--green)">{{ $item['title'] }}</p>
                            <p class="text-sm mt-0.5" style="color: var(--text-muted)">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════
     VISI & MISI
════════════════════════════════════════════════════ -->
<section class="py-24" style="background: var(--cream)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="text-center mb-16 reveal">
            <span class="section-tag">Nilai Kami</span>
            <h2 class="mt-4 text-4xl font-display font-bold" style="color: var(--text)">Visi & Misi</h2>
            <p class="mt-3 text-base max-w-xl mx-auto" style="color: var(--text-muted)">
                Landasan yang memandu kami dalam melayani setiap anggota koperasi
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Visi -->
            <div class="reveal delay-100 rounded-3xl p-10 relative overflow-hidden" style="background: var(--green)">
                <!-- Decorative background -->
                <div class="absolute top-0 right-0 w-56 h-56 rounded-full opacity-10 translate-x-16 -translate-y-16"
                     style="background: var(--gold)"></div>

                <div class="relative">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mb-6"
                         style="background: rgba(201,168,76,0.15)">
                        <svg class="w-7 h-7" style="color: var(--gold)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-display font-bold text-white mb-4">Visi</h3>
                    <div class="w-12 h-0.5 rounded mb-6" style="background: var(--gold)"></div>

                    <p class="text-base leading-relaxed" style="color: rgba(255,255,255,0.8)">
                        {{ $info->vision ?? 'Menjadi koperasi simpan pinjam yang terpercaya, profesional, dan memberikan manfaat nyata bagi seluruh anggota dalam mewujudkan kesejahteraan bersama.' }}
                    </p>
                </div>
            </div>

            <!-- Misi -->
            <div class="reveal delay-200 rounded-3xl p-10 border" style="background: var(--cream-white); border-color: rgba(26,61,46,0.1)">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mb-6"
                     style="background: var(--green-light)">
                    <svg class="w-7 h-7" style="color: var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>

                <h3 class="text-2xl font-display font-bold mb-4" style="color: var(--green)">Misi</h3>
                <div class="w-12 h-0.5 rounded mb-6" style="background: var(--gold)"></div>

                @php
                    $misiLines = $info->mission
                        ? array_filter(array_map('trim', preg_split('/[\n\r]+/', $info->mission)))
                        : ['Memberikan layanan simpan pinjam yang mudah dan terjangkau', 'Meningkatkan kesejahteraan anggota melalui pengelolaan keuangan yang baik', 'Menjalankan usaha gadai dengan transparan dan adil', 'Mendistribusikan SHU secara merata kepada seluruh anggota aktif'];
                @endphp

                <ul class="space-y-4">
                    @foreach(array_values($misiLines) as $i => $line)
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold text-white flex-shrink-0 mt-0.5"
                              style="background: var(--green)">{{ $i + 1 }}</span>
                        <p class="text-sm leading-relaxed" style="color: var(--text-muted)">
                            {{ preg_replace('/^\d+\.\s*/', '', $line) }}
                        </p>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════
     LAYANAN
════════════════════════════════════════════════════ -->
<section id="layanan" class="py-24" style="background: var(--cream-white)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-16 reveal">
            <span class="section-tag">Apa Yang Kami Tawarkan</span>
            <h2 class="mt-4 text-4xl font-display font-bold" style="color: var(--text)">Layanan Unggulan</h2>
            <p class="mt-3 text-base max-w-xl mx-auto" style="color: var(--text-muted)">
                Dirancang untuk memenuhi kebutuhan finansial Anda dengan cara yang mudah dan efisien
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                [
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                    'title' => 'Gadai Barang',
                    'desc' => 'Gadaikan laptop, HP, TV, emas, motor, dan berbagai barang elektronik dengan penilaian cepat dan fair.',
                    'tag' => '8%/bulan',
                    'color' => 'var(--green)',
                ],
                [
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
                    'title' => 'Simpanan Anggota',
                    'desc' => 'Tabung secara rutin dengan simpanan pokok dan wajib bulanan. Aman dan terkelola dengan baik.',
                    'tag' => 'Bulanan',
                    'color' => '#1E40AF',
                ],
                [
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>',
                    'title' => 'Bagi Hasil SHU',
                    'desc' => 'Nikmati Sisa Hasil Usaha (SHU) tahunan berdasarkan proporsi simpanan dan aktivitas gadai Anda.',
                    'tag' => 'Tahunan',
                    'color' => 'var(--gold-dark)',
                ],
                [
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>',
                    'title' => 'Simulasi Gadai',
                    'desc' => 'Hitung estimasi pinjaman, bunga, dan jadwal pembayaran sebelum mengajukan gadai resmi.',
                    'tag' => 'Gratis',
                    'color' => '#065F46',
                ],
                [
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
                    'title' => 'Notifikasi Real-time',
                    'desc' => 'Terima notifikasi otomatis untuk status gadai, pengingat jatuh tempo, dan konfirmasi pembayaran.',
                    'tag' => 'Otomatis',
                    'color' => '#5B21B6',
                ],
                [
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>',
                    'title' => 'Konsultasi Langsung',
                    'desc' => 'Chat langsung dengan pengurus koperasi untuk pertanyaan seputar layanan gadai dan simpanan.',
                    'tag' => '24/7',
                    'color' => '#92400E',
                ],
            ] as $i => $service)
            <div class="reveal feature-card" style="transition-delay: {{ $i * 80 }}ms">
                <div class="feature-icon">
                    <svg class="w-6 h-6" style="color: {{ $service['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $service['icon'] !!}
                    </svg>
                </div>
                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-semibold text-base" style="color: var(--text)">{{ $service['title'] }}</h3>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full flex-shrink-0 ml-2"
                          style="background: var(--green-light); color: var(--green)">{{ $service['tag'] }}</span>
                </div>
                <p class="text-sm leading-relaxed" style="color: var(--text-muted)">{{ $service['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════
     LANGKAH GADAI — How it works
════════════════════════════════════════════════════ -->
<section id="langkah" class="py-24" style="background: var(--cream)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">

            <!-- Left: Header + CTA -->
            <div class="lg:sticky lg:top-28 space-y-6 reveal-left">
                <span class="section-tag">Cara Kerja</span>
                <h2 class="text-4xl font-display font-bold leading-tight" style="color: var(--text)">
                    5 Langkah Mudah<br>Gadai Barang Anda
                </h2>
                <div class="gold-line w-16"></div>
                <p class="text-base leading-relaxed" style="color: var(--text-muted)">
                    Proses gadai di MONY dirancang sesederhana mungkin. Dari registrasi hingga dana cair, semua bisa dilakukan secara digital.
                </p>

                <div class="rounded-2xl p-6" style="background: var(--green-light); border: 1px solid rgba(26,61,46,0.1)">
                    <p class="text-sm font-semibold mb-3" style="color: var(--green)">Keunggulan Gadai MONY</p>
                    <ul class="space-y-2 text-sm" style="color: var(--text-muted)">
                        @foreach(['Penilaian barang langsung oleh penaksir berpengalaman', 'Bunga tetap 8% per bulan, tidak ada biaya tersembunyi', 'Tenor hingga 4 bulan, bisa diperpanjang', 'Barang aman tersimpan di gudang ber-AC & berkeamanan', 'Pelunasan bisa dilakukan kapan saja'] as $item)
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl font-semibold text-white transition-all duration-200"
                   style="background: var(--gold); box-shadow: 0 4px 16px rgba(201,168,76,0.3)"
                   onmouseover="this.style.background='var(--gold2)'; this.style.transform='translateY(-2px)'"
                   onmouseout="this.style.background='var(--gold)'; this.style.transform='translateY(0)'">
                    Mulai Gadai Sekarang
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>

            <!-- Right: Steps timeline -->
            <div class="space-y-0">
                @foreach([
                    ['num' => '01', 'title' => 'Daftar & Verifikasi KYC', 'desc' => 'Buat akun MONY dengan data diri lengkap. Upload foto KTP dan selfie untuk verifikasi identitas (KYC). Proses verifikasi berlangsung 1–2 hari kerja.', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
                    ['num' => '02', 'title' => 'Pilih Barang & Ajukan', 'desc' => 'Pilih kategori barang dari katalog. Isi deskripsi, kondisi, dan estimasi nilai barang. Upload foto barang dari berbagai sudut.', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
                    ['num' => '03', 'title' => 'Penilaian & Persetujuan', 'desc' => 'Pengurus melakukan penilaian (taksiran) terhadap barang Anda. Jika disetujui, nilai pinjaman dan jadwal bayar bunga akan ditetapkan.', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'],
                    ['num' => '04', 'title' => 'Antar Barang & Dana Cair', 'desc' => 'Antarkan barang gadai ke kantor koperasi. Setelah verifikasi fisik, dana pinjaman langsung ditransfer ke rekening Anda.', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                    ['num' => '05', 'title' => 'Bayar Bunga / Tebus', 'desc' => 'Bayar bunga setiap bulan via transfer bank dan upload bukti. Saat ingin menebus, bayar pokok + bunga dan barang dikembalikan.', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>'],
                ] as $i => $step)
                <div class="reveal step-line relative flex gap-5 pb-8" style="transition-delay: {{ $i * 100 }}ms">
                    <!-- Number badge -->
                    <div class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center font-display font-bold text-white text-lg z-10"
                         style="background: var(--green)">{{ $step['num'] }}</div>

                    <!-- Content -->
                    <div class="flex-1 pb-2 pt-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h4 class="font-semibold text-base" style="color: var(--text)">{{ $step['title'] }}</h4>
                        </div>
                        <p class="text-sm leading-relaxed" style="color: var(--text-muted)">{{ $step['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════
     BARANG GADAI — Katalog
════════════════════════════════════════════════════ -->
<section id="barang" class="py-24" style="background: var(--cream-white)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14 reveal">
            <span class="section-tag">Katalog</span>
            <h2 class="mt-4 text-4xl font-display font-bold" style="color: var(--text)">
                Barang yang Bisa Digadai
            </h2>
            <p class="mt-3 text-base max-w-xl mx-auto" style="color: var(--text-muted)">
                Kami menerima berbagai jenis barang dengan taksiran kompetitif hingga {{ $katalog->max('max_loan_percentage') ?? 85 }}% dari nilai pasar
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($katalog as $i => $item)
            <div class="reveal group cursor-pointer rounded-2xl p-5 text-center border transition-all duration-300"
                 style="background: white; border-color: rgba(26,61,46,0.08); transition-delay: {{ ($i % 5) * 60 }}ms"
                 onmouseover="this.style.borderColor='var(--green)'; this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 28px rgba(26,61,46,0.12)'"
                 onmouseout="this.style.borderColor='rgba(26,61,46,0.08)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">

                <!-- Icon / Image -->
                <div class="w-16 h-16 rounded-2xl mx-auto mb-3 flex items-center justify-center overflow-hidden transition-all duration-300"
                     style="background: var(--green-light)">
                    @if($item->image_path)
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}"
                             class="w-full h-full object-cover">
                    @else
                        @php
                            $icons = [
                                'Laptop' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                                'Smartphone' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
                                'TV' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                                'Motor' => 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193',
                                'Emas' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                'default' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                            ];
                            $iconPath = $icons['default'];
                            foreach($icons as $key => $path) {
                                if(str_contains($item->name, $key)) { $iconPath = $path; break; }
                            }
                        @endphp
                        <svg class="w-8 h-8 transition-colors" style="color: var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $iconPath }}"/>
                        </svg>
                    @endif
                </div>

                <p class="font-semibold text-sm leading-tight mb-1" style="color: var(--text)">{{ $item->name }}</p>
                <p class="text-xs mb-2" style="color: var(--text-muted)">{{ $item->category }}</p>

                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                     style="background: var(--gold-light); color: var(--gold-dark)">
                    Max {{ $item->max_loan_percentage }}%
                </div>
            </div>
            @endforeach
        </div>

        <!-- Info footnote -->
        <div class="mt-10 reveal rounded-2xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
             style="background: var(--green-light); border: 1px solid rgba(26,61,46,0.1)">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm" style="color: var(--green)">
                    Nilai pinjaman maksimal <strong>80–85%</strong> dari nilai taksir. Kondisi, merek, dan tahun produksi mempengaruhi nilai taksiran akhir.
                </p>
            </div>
            <a href="{{ route('login') }}"
               class="flex-shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all duration-200"
               style="background: var(--green)"
               onmouseover="this.style.background='var(--green2)'"
               onmouseout="this.style.background='var(--green)'">
                Cek Taksiran Sekarang
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════
     WHY CHOOSE US
════════════════════════════════════════════════════ -->
<section class="py-24 green-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">

        <div class="text-center mb-14 reveal">
            <span class="section-tag-green" style="background: rgba(201,168,76,0.15); color: var(--gold)">
                Keunggulan
            </span>
            <h2 class="mt-4 text-4xl font-display font-bold text-white">Kenapa Memilih MONY?</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach([
                ['num' => '01', 'title' => 'Transparan & Jujur', 'desc' => 'Semua biaya sudah jelas di awal. Tidak ada bunga tersembunyi atau pemotongan sepihak. Anda tahu persis berapa yang harus dibayar.'],
                ['num' => '02', 'title' => 'Proses Digital 100%', 'desc' => 'Dari pendaftaran hingga pembayaran, semua dilakukan secara digital. Pantau status gadai real-time dari mana saja.'],
                ['num' => '03', 'title' => 'Anggota Adalah Pemilik', 'desc' => 'Sebagai anggota koperasi, Anda ikut memiliki dan mendapatkan Sisa Hasil Usaha (SHU) setiap tahun sesuai kontribusi Anda.'],
            ] as $i => $item)
            <div class="reveal rounded-3xl p-8 transition-all duration-300"
                 style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); transition-delay: {{ $i * 100 }}ms"
                 onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.transform='translateY(-4px)'"
                 onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.transform='translateY(0)'">
                <div class="font-display text-5xl font-bold mb-4 opacity-30" style="color: var(--gold)">{{ $item['num'] }}</div>
                <h3 class="text-xl font-semibold text-white mb-3">{{ $item['title'] }}</h3>
                <p class="text-sm leading-relaxed" style="color: rgba(255,255,255,0.6)">{{ $item['desc'] }}</p>
            </div>
            @endforeach
        </div>

        <!-- Gold divider -->
        <div class="mt-16 gold-line w-full opacity-30"></div>

        <!-- Testimonial -->
        <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach([
                ['text' => '"Proses gadai di MONY sangat mudah. Upload foto barang, tunggu konfirmasi, dan dana langsung cair. Recommended!"', 'name' => 'Sari W.', 'role' => 'Anggota sejak 2021'],
                ['text' => '"Bunganya kompetitif dan tidak ada biaya tersembunyi. Pengurus sangat responsif dan komunikatif."', 'name' => 'Dedi K.', 'role' => 'Anggota sejak 2020'],
                ['text' => '"SHU yang saya terima setiap tahun lumayan. Selain gadai, saya juga rutin menabung simpanan wajib."', 'name' => 'Rina S.', 'role' => 'Anggota sejak 2022'],
            ] as $t)
            <div class="reveal rounded-2xl p-6" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08)">
                <svg class="w-6 h-6 mb-4 opacity-40" style="color: var(--gold)" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                </svg>
                <p class="text-sm leading-relaxed italic mb-4" style="color: rgba(255,255,255,0.7)">{{ $t['text'] }}</p>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                         style="background: rgba(201,168,76,0.2); color: var(--gold)">
                        {{ strtoupper(substr($t['name'], 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white">{{ $t['name'] }}</p>
                        <p class="text-xs" style="color: rgba(255,255,255,0.4)">{{ $t['role'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════
     CTA SECTION
════════════════════════════════════════════════════ -->
<section class="py-24" style="background: var(--cream)">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <div class="reveal rounded-3xl overflow-hidden relative" style="background: var(--cream-white); border: 1px solid rgba(26,61,46,0.1); box-shadow: 0 16px 48px rgba(26,61,46,0.08)">
            <!-- Decorative gradient -->
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-0 left-0 right-0 h-1 gradient-gold"></div>
            </div>

            <div class="p-12 sm:p-16 relative">
                <span class="section-tag mb-6 inline-block">Bergabung Sekarang</span>
                <h2 class="text-4xl sm:text-5xl font-display font-bold mb-5 text-balance" style="color: var(--text)">
                    Siap Mulai Perjalanan<br>Finansial Anda?
                </h2>
                <p class="text-base mb-10 max-w-lg mx-auto" style="color: var(--text-muted)">
                    Daftar gratis dan mulai nikmati layanan gadai digital yang mudah, aman, dan transparan bersama ribuan anggota MONY lainnya.
                </p>

                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl text-base font-semibold text-white transition-all duration-200"
                       style="background: var(--green); box-shadow: 0 6px 24px rgba(26,61,46,0.25)"
                       onmouseover="this.style.background='var(--green2)'; this.style.transform='translateY(-2px)'"
                       onmouseout="this.style.background='var(--green)'; this.style.transform='translateY(0)'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Daftar Gratis Sekarang
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl text-base font-semibold border-2 transition-all duration-200"
                       style="color: var(--gold-dark); border-color: var(--gold); background: transparent"
                       onmouseover="this.style.background='var(--gold)'; this.style.color='white'"
                       onmouseout="this.style.background='transparent'; this.style.color='var(--gold-dark)'">
                        Masuk ke Akun
                    </a>
                </div>

                <p class="mt-6 text-xs" style="color: var(--text-muted)">
                    Bergabung gratis &middot; Tidak ada biaya registrasi &middot; Data aman &amp; terenkripsi
                </p>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════
     FOOTER
════════════════════════════════════════════════════ -->
<footer class="border-t" style="background: var(--green); border-color: rgba(255,255,255,0.08)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top footer -->
        <div class="py-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

            <!-- Brand -->
            <div class="sm:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: rgba(201,168,76,0.2)">
                        <svg class="w-5 h-5" style="color: var(--gold)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="font-display font-bold text-xl text-white">MONY</span>
                </div>
                <p class="text-sm leading-relaxed mb-5" style="color: rgba(255,255,255,0.5)">
                    {{ $info->name }}<br>
                    Solusi gadai dan simpan pinjam terpercaya untuk kesejahteraan anggota.
                </p>
                <div class="flex gap-3">
                    @foreach(['M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z', 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z', 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z'] as $svgPath)
                    <a href="#" class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200"
                       style="background: rgba(255,255,255,0.08)"
                       onmouseover="this.style.background='rgba(201,168,76,0.2)'"
                       onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                        <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $svgPath }}"/>
                        </svg>
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Links: Layanan -->
            <div>
                <p class="font-semibold text-white mb-5">Layanan</p>
                <ul class="space-y-3">
                    @foreach(['Gadai Barang' => '#barang', 'Simpanan Anggota' => '#layanan', 'Simulasi Gadai' => route('login'), 'SHU Tahunan' => '#layanan', 'Konsultasi' => '#layanan'] as $label => $href)
                    <li>
                        <a href="{{ $href }}" class="text-sm transition-colors"
                           style="color: rgba(255,255,255,0.5)"
                           onmouseover="this.style.color='var(--gold)'"
                           onmouseout="this.style.color='rgba(255,255,255,0.5)'">{{ $label }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- Links: Info -->
            <div>
                <p class="font-semibold text-white mb-5">Informasi</p>
                <ul class="space-y-3">
                    @foreach(['Tentang Kami' => '#tentang', 'Visi & Misi' => '#tentang', 'Cara Gadai' => '#langkah', 'Syarat & Ketentuan' => '#', 'Kebijakan Privasi' => '#'] as $label => $href)
                    <li>
                        <a href="{{ $href }}" class="text-sm transition-colors"
                           style="color: rgba(255,255,255,0.5)"
                           onmouseover="this.style.color='var(--gold)'"
                           onmouseout="this.style.color='rgba(255,255,255,0.5)'">{{ $label }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <p class="font-semibold text-white mb-5">Hubungi Kami</p>
                <ul class="space-y-4">
                    @if($info->address)
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--gold)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-sm leading-relaxed" style="color: rgba(255,255,255,0.5)">{{ $info->address }}</span>
                    </li>
                    @endif
                    @if($info->phone)
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--gold)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span class="text-sm font-mono" style="color: rgba(255,255,255,0.5)">{{ $info->phone }}</span>
                    </li>
                    @endif
                    @if($info->email)
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--gold)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm" style="color: rgba(255,255,255,0.5)">{{ $info->email }}</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Bottom footer -->
        <div class="py-5 border-t flex flex-col sm:flex-row items-center justify-between gap-3"
             style="border-color: rgba(255,255,255,0.08)">
            <p class="text-xs" style="color: rgba(255,255,255,0.3)">
                &copy; {{ date('Y') }} {{ $info->name }}. Seluruh hak dilindungi undang-undang.
            </p>
            <p class="text-xs" style="color: rgba(255,255,255,0.2)">
                Dibangun dengan <span style="color: var(--gold)">♥</span> menggunakan platform MONY
            </p>
        </div>
    </div>
</footer>


<!-- ═══════════════════════════════════════════════════
     SCRIPTS
════════════════════════════════════════════════════ -->
<script>
function landingApp() {
    return {
        scrolled: false,
        mobileMenu: false,
        handleScroll() {
            this.scrolled = window.scrollY > 40;
        },
        init() {
            this.handleScroll();
            this.initScrollReveal();
            this.initCounters();
        },
        initScrollReveal() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

            document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(el => {
                const delay = el.dataset.delay ? parseInt(el.dataset.delay) : 0;
                el.style.transitionDelay = delay + 'ms';
                observer.observe(el);
            });
        },
        initCounters() {
            const counters = document.querySelectorAll('[data-counter]');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    const el  = entry.target;
                    const end = parseInt(el.dataset.counter);
                    const dur = 1500;
                    const start = performance.now();
                    const update = (now) => {
                        const elapsed = now - start;
                        const progress = Math.min(elapsed / dur, 1);
                        const eased = 1 - Math.pow(1 - progress, 3);
                        el.textContent = Math.round(eased * end).toLocaleString('id-ID');
                        if (progress < 1) requestAnimationFrame(update);
                    };
                    requestAnimationFrame(update);
                    observer.unobserve(el);
                });
            }, { threshold: 0.5 });

            counters.forEach(c => observer.observe(c));
        }
    }
}
</script>

</body>
</html>
