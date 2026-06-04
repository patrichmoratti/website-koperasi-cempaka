<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Anggota' }} â€” MONY KSP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-mony-bg font-sans"
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

    <div x-show="sidebarOpen && window.innerWidth < 1024"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/40 z-30 lg:hidden" style="display:none"></div>

    <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-100 shadow-lg flex flex-col transition-transform duration-200"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-mony-text text-sm">MONY</p>
                <p class="text-xs text-mony-muted">KSP Cempaka</p>
            </div>
        </div>

        <div class="px-5 py-3 border-b border-gray-100">
            <span class="badge-success text-xs px-3 py-1">Anggota</span>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5 scrollbar-hide">

            <a href="{{ route('anggota.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('anggota.dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                Dashboard
            </a>

            <p class="px-3 text-xs font-semibold text-mony-muted uppercase tracking-wider mb-2 mt-4">Gadai</p>

            <a href="{{ route('anggota.gadai.index') }}"
               class="sidebar-link {{ request()->routeIs('anggota.gadai.index') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Gadai Saya
            </a>

            <a href="{{ route('anggota.gadai.pengajuan.create') }}"
               class="sidebar-link {{ request()->routeIs('anggota.gadai.pengajuan.create') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Ajukan Gadai
            </a>

            <a href="{{ route('anggota.gadai.simulasi') }}"
               class="sidebar-link {{ request()->routeIs('anggota.gadai.simulasi') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Simulasi Gadai
            </a>

            <p class="px-3 text-xs font-semibold text-mony-muted uppercase tracking-wider mb-2 mt-4">Keuangan</p>

            <a href="{{ route('anggota.simpanan.index') }}"
               class="sidebar-link {{ request()->routeIs('anggota.simpanan.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Simpanan
            </a>

            <a href="{{ route('anggota.shu.index') }}"
               class="sidebar-link {{ request()->routeIs('anggota.shu.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                </svg>
                SHU Saya
            </a>

            <a href="{{ route('anggota.riwayat') }}"
               class="sidebar-link {{ request()->routeIs('anggota.riwayat') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat
            </a>

            <p class="px-3 text-xs font-semibold text-mony-muted uppercase tracking-wider mb-2 mt-4">Lainnya</p>

            <a href="{{ route('anggota.notifikasi.index') }}"
               class="sidebar-link {{ request()->routeIs('anggota.notifikasi.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Notifikasi
                @php $unread = auth()->user()->unreadNotifikasi()->count() @endphp
                @if($unread > 0) <span class="ml-auto badge-danger text-xs">{{ $unread }}</span> @endif
            </a>

            <a href="{{ route('anggota.pesan.index') }}"
               class="sidebar-link {{ request()->routeIs('anggota.pesan.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                Pesan
            </a>

            <a href="{{ route('anggota.profil') }}"
               class="sidebar-link {{ request()->routeIs('anggota.profil') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profil
            </a>
        </nav>

        <div class="px-4 py-3 border-t border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary font-semibold text-sm flex-shrink-0">
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
        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur border-b border-gray-100 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-lg text-mony-muted hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                @isset($breadcrumbs)
                    <nav class="hidden sm:flex items-center gap-1 text-sm text-mony-muted">
                        @foreach($breadcrumbs as $crumb)
                            @if(!$loop->last)
                                <a href="{{ $crumb['url'] ?? '#' }}" class="hover:text-primary">{{ $crumb['label'] }}</a>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @else
                                <span class="text-mony-text font-medium">{{ $crumb['label'] }}</span>
                            @endif
                        @endforeach
                    </nav>
                @endisset
            </div>
            <div class="flex items-center gap-3">
                @php $unread = auth()->user()->unreadNotifikasi()->count() @endphp
                <a href="{{ route('anggota.notifikasi.index') }}" class="relative p-2 rounded-lg text-mony-muted hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unread > 0)
                        <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">{{ $unread > 9 ? '9+' : $unread }}</span>
                    @endif
                </a>
                <span class="hidden sm:block text-sm font-medium text-mony-text">{{ auth()->user()->name }}</span>
            </div>
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

</body>
</html>

