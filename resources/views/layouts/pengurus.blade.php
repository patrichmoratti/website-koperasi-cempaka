<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Pengurus' }} â€” MONY KSP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans app-layout" style="background: var(--green)"
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

    <div x-show="sidebarOpen && window.innerWidth < 1024"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/40 z-30 lg:hidden" style="display:none"></div>

    <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-100 shadow-lg flex flex-col transition-transform duration-200"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <img src="{{ asset('images/logo-mony.png') }}" alt="Logo MONY" class="w-9 h-9 rounded-xl object-contain flex-shrink-0">
            <div>
                <p class="font-bold text-mony-text text-sm">MONY</p>
                <p class="text-xs text-mony-muted">KSP Cempaka</p>
            </div>
        </div>

        <div class="px-5 py-3 border-b border-gray-100">
            <span class="badge-warning text-xs px-3 py-1">Pengurus</span>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5 scrollbar-hide">
            <a href="{{ route('pengurus.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('pengurus.dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('pengurus.konfirmasi.index') }}"
               class="sidebar-link {{ request()->routeIs('pengurus.konfirmasi.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Konfirmasi
                @php $pc = \App\Models\PengajuanGadai::where('status','proses')->count() + \App\Models\PembayaranGadai::where('status','pending')->count() + \App\Models\Simpanan::where('status','pending')->count() + \App\Models\User::where('account_status','pending')->where('role','anggota')->count() @endphp
                @if($pc > 0) <span class="ml-auto badge-danger text-xs">{{ $pc }}</span> @endif
            </a>

            <a href="{{ route('pengurus.gadai.index') }}"
               class="sidebar-link {{ request()->routeIs('pengurus.gadai.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Gadai
            </a>

            <a href="{{ route('pengurus.anggota.index') }}"
               class="sidebar-link {{ request()->routeIs('pengurus.anggota.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Anggota
            </a>

            <a href="{{ route('pengurus.riwayat.index') }}"
               class="sidebar-link {{ request()->routeIs('pengurus.riwayat.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat
            </a>

<a href="{{ route('pengurus.pesan.index') }}"
               class="sidebar-link {{ request()->routeIs('pengurus.pesan.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                Pesan
            </a>
        </nav>

        <div class="px-4 py-3 border-t border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-secondary/20 flex items-center justify-center text-secondary font-semibold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-mony-text truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-mony-muted truncate">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-mony-muted hover:text-red-500 transition-colors" title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="transition-all duration-200" :class="sidebarOpen ? 'lg:ml-64' : 'ml-0'">
        <header class="sticky top-0 z-20 backdrop-blur px-4 py-3 flex items-center justify-between border-b"
                style="background: rgba(15,42,30,0.92); border-color: rgba(255,255,255,0.1)">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-lg transition-colors" style="color: rgba(255,255,255,0.7)"
                        onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.color='white'"
                        onmouseout="this.style.background='transparent'; this.style.color='rgba(255,255,255,0.7)'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                @isset($breadcrumbs)
                    <nav class="hidden sm:flex items-center gap-1 text-sm">
                        @foreach($breadcrumbs as $crumb)
                            @if(!$loop->last)
                                <a href="{{ $crumb['url'] ?? '#' }}" class="transition-colors" style="color: rgba(255,255,255,0.55)" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">{{ $crumb['label'] }}</a>
                                <svg class="w-3 h-3" style="color: rgba(255,255,255,0.4)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @else
                                <span class="font-medium" style="color: white">{{ $crumb['label'] }}</span>
                            @endif
                        @endforeach
                    </nav>
                @endisset
            </div>
            <span class="hidden sm:block text-sm font-medium" style="color: white">{{ auth()->user()->name }}</span>
        </header>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mx-4 mt-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center justify-between animate-fade-in">
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="text-green-500 hover:text-green-700 ml-4">âœ•</button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="mx-4 mt-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 flex items-center justify-between animate-fade-in">
                <span>{{ session('error') }}</span>
                <button @click="show = false" class="text-red-500 hover:text-red-700 ml-4">âœ•</button>
            </div>
        @endif

        <main class="p-4 sm:p-6">
            @yield('content')
        </main>
    </div>

    @include('partials.confirm-modal')
    @include('partials.reject-modal')
    @include('partials.lightbox')
</body>
</html>

