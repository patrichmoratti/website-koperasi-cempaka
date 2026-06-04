<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} &mdash; MONY KSP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans" style="background: var(--cream)"
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

    <div x-show="sidebarOpen && window.innerWidth < 1024"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/40 z-30 lg:hidden" style="display:none"></div>

    <aside class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col transition-transform duration-200 border-r"
           style="background: white; border-color: rgba(26,61,46,0.08)"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="flex items-center gap-3 px-5 py-4 border-b" style="border-color: rgba(26,61,46,0.08)">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background: var(--green)">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-display font-bold text-sm" style="color: var(--green)">MONY</p>
                <p class="text-xs" style="color: var(--text-muted)">KSP Cempaka</p>
            </div>
        </div>

        <div class="px-5 py-2.5 border-b" style="background: var(--green-light); border-color: rgba(26,61,46,0.08)">
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full text-white" style="background: var(--green)">Admin</span>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5 scrollbar-hide">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-2" style="color: var(--text-muted)">Utama</p>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.anggota.index') }}" class="sidebar-link {{ request()->routeIs('admin.anggota.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Anggota
            </a>

            <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-2 mt-4" style="color: var(--text-muted)">Gadai</p>
            <a href="{{ route('admin.gadai.index') }}" class="sidebar-link {{ request()->routeIs('admin.gadai.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Manajemen Gadai
            </a>
            <a href="{{ route('admin.konfirmasi.index') }}" class="sidebar-link {{ request()->routeIs('admin.konfirmasi.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Konfirmasi
                @php $pc = \App\Models\PengajuanGadai::where('status','proses')->count() + \App\Models\PembayaranGadai::where('status','pending')->count() + \App\Models\Simpanan::where('status','pending')->count() + \App\Models\User::where('account_status','pending')->where('role','anggota')->count() @endphp
                @if($pc > 0)<span class="ml-auto badge-danger text-xs">{{ $pc }}</span>@endif
            </a>
            <a href="{{ route('admin.katalog.index') }}" class="sidebar-link {{ request()->routeIs('admin.katalog.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Katalog Barang
            </a>

            <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-2 mt-4" style="color: var(--text-muted)">Keuangan</p>
            <a href="{{ route('admin.simpanan.index') }}" class="sidebar-link {{ request()->routeIs('admin.simpanan.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Simpanan
            </a>
            <a href="{{ route('admin.biaya.index') }}" class="sidebar-link {{ request()->routeIs('admin.biaya.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                Biaya Operasional
            </a>
            <a href="{{ route('admin.laporan.keuangan') }}" class="sidebar-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan
            </a>
            <a href="{{ route('admin.shu.index') }}" class="sidebar-link {{ request()->routeIs('admin.shu.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                SHU
            </a>

            <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-2 mt-4" style="color: var(--text-muted)">Pengaturan</p>
            <a href="{{ route('admin.notifikasi.kirim') }}" class="sidebar-link {{ request()->routeIs('admin.notifikasi.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Kirim Notifikasi
            </a>
            <a href="{{ route('admin.pengaturan') }}" class="sidebar-link {{ request()->routeIs('admin.pengaturan') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan
            </a>
        </nav>

        <div class="px-4 py-3 border-t" style="border-color: rgba(26,61,46,0.08)">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0" style="background: var(--green-light); color: var(--green)">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold truncate" style="color: var(--text)">{{ auth()->user()->name }}</p>
                    <p class="text-xs truncate" style="color: var(--text-muted)">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg transition-colors" style="color: var(--text-muted)"
                            onmouseover="this.style.background='#FEE2E2'; this.style.color='#991B1B'"
                            onmouseout="this.style.background='transparent'; this.style.color='var(--text-muted)'"
                            title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="transition-all duration-200" :class="sidebarOpen ? 'lg:ml-64' : 'ml-0'">
        <header class="sticky top-0 z-20 backdrop-blur px-4 py-3 flex items-center justify-between border-b"
                style="background: rgba(245,240,232,0.9); border-color: rgba(26,61,46,0.08)">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-xl transition-colors" style="color: var(--text-muted)"
                        onmouseover="this.style.background='var(--green-light)'; this.style.color='var(--green)'"
                        onmouseout="this.style.background='transparent'; this.style.color='var(--text-muted)'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                @isset($breadcrumbs)
                    <nav class="hidden sm:flex items-center gap-1 text-sm">
                        @foreach($breadcrumbs as $crumb)
                            @if(!$loop->last)
                                <a href="{{ $crumb['url'] ?? '#' }}" class="transition-colors" style="color: var(--text-muted)" onmouseover="this.style.color='var(--green)'" onmouseout="this.style.color='var(--text-muted)'">{{ $crumb['label'] }}</a>
                                <svg class="w-3 h-3" style="color: var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            @else
                                <span class="font-medium" style="color: var(--text)">{{ $crumb['label'] }}</span>
                            @endif
                        @endforeach
                    </nav>
                @endisset
            </div>
            <span class="hidden sm:block text-sm font-medium" style="color: var(--text)">{{ auth()->user()->name }}</span>
        </header>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mx-4 mt-4 px-4 py-3 rounded-2xl text-sm flex items-center justify-between animate-fade-in"
                 style="background: var(--green-light); color: var(--green); border: 1px solid rgba(26,61,46,0.15)">
                <div class="flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>{{ session('success') }}</span></div>
                <button @click="show = false" class="ml-4 opacity-60 hover:opacity-100 text-sm">&#215;</button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="mx-4 mt-4 px-4 py-3 rounded-2xl text-sm flex items-center justify-between animate-fade-in"
                 style="background: #FEE2E2; color: #991B1B; border: 1px solid rgba(153,27,27,0.15)">
                <span>{{ session('error') }}</span>
                <button @click="show = false" class="ml-4 opacity-60 hover:opacity-100 text-sm">&#215;</button>
            </div>
        @endif

        <main class="p-4 sm:p-6">
            @yield('content')
        </main>
    </div>

</body>
</html>
