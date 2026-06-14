{{--
    Detail pengurus popup for admin Manajemen Pengurus page.
    Accepts: $user (User with role=pengurus)
    Enclosing Alpine scope must expose: openModal (string|null)
--}}
<div x-show="openModal === 'pengurus-{{ $user->id }}'" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @click.self="openModal = null"
     @keydown.escape.window="openModal = null"
     style="background:rgba(8,20,12,0.72); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);"
     x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    <div @click.stop
         class="bg-white popup-sheet w-full shadow-2xl scrollbar-hide"
         style="max-width:480px; max-height:88vh; overflow-y:auto; border-radius:24px;"
         x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

        {{-- Branded Header --}}
        <div class="relative overflow-hidden rounded-t-3xl px-6 py-5"
             style="background:linear-gradient(135deg, var(--green) 0%, var(--green2) 100%);">
            <div class="absolute inset-0 opacity-[0.05]"
                 style="background-image:radial-gradient(circle,#fff 1px,transparent 1px); background-size:14px 14px;"></div>
            <div class="relative flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 text-xl font-bold text-white"
                         style="background:rgba(255,255,255,0.2);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white leading-tight">{{ $user->name }}</h2>
                        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6);">
                            Bergabung {{ $user->created_at->format('d M Y') }}
                        </p>
                    </div>
                </div>
                <button type="button" @click="openModal = null"
                        class="w-8 h-8 flex items-center justify-center rounded-full flex-shrink-0 transition-colors"
                        style="background:rgba(255,255,255,0.15);"
                        onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="px-5 pt-5 pb-2 space-y-4">

            {{-- Status Badge --}}
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-mony-muted">Status Akun</span>
                <span class="badge-{{ $user->account_status === 'active' ? 'success' : 'gray' }}">
                    {{ $user->account_status === 'active' ? 'Aktif' : 'Disuspend' }}
                </span>
            </div>

            {{-- Data Diri --}}
            <div class="rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                    <p class="text-xs font-semibold text-mony-text">Data Diri</p>
                </div>
                @php
                $dataRows = [
                    ['Nama Lengkap', $user->name],
                    ['Email',        $user->email],
                    ['No. HP',       $user->phone ?? '-'],
                    ['Alamat',       $user->address ?? '-'],
                ];
                @endphp
                @foreach($dataRows as $i => [$label, $val])
                <div class="flex justify-between items-start px-4 py-2.5 text-xs {{ $i < count($dataRows)-1 ? 'border-b' : '' }}"
                     style="{{ $i < count($dataRows)-1 ? 'border-color:#f0f7ee;' : '' }}">
                    <span class="text-mony-muted flex-shrink-0 mr-4">{{ $label }}</span>
                    <span class="font-semibold text-mony-text text-right break-all">{{ $val }}</span>
                </div>
                @endforeach
            </div>

            {{-- Akses Sistem --}}
            <div class="rounded-2xl overflow-hidden" style="border:1px solid #ddebd5;">
                <div class="px-4 py-2.5" style="background:#f5faf3; border-bottom:1px solid #ddebd5;">
                    <p class="text-xs font-semibold text-mony-text">Akses Sistem</p>
                </div>
                <div class="px-4 py-3 flex items-start gap-2.5 text-xs">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-mony-muted">
                        Akun ini memiliki peran <strong class="text-mony-text">Pengurus</strong> — dapat mengakses
                        Dashboard Pengurus, Konfirmasi, Manajemen Gadai, Manajemen Anggota, Biaya, Pesan, dan Riwayat.
                    </span>
                </div>
            </div>

        </div>

        {{-- Footer Actions --}}
        <div class="px-5 pt-2 pb-5 flex gap-2">
            <form method="POST" action="{{ route('admin.pengurus.reset-password', $user) }}" class="flex-1"
                  onsubmit="return confirmAction(event, 'Reset password {{ addslashes($user->name) }}? Password baru akan ditampilkan setelah reset.', 'Ya, Reset')">
                @csrf
                <button type="submit" class="btn-outline w-full justify-center py-3 text-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Reset Password
                </button>
            </form>
            <form method="POST" action="{{ route('admin.pengurus.toggle-status', $user) }}" class="flex-1"
                  onsubmit="return confirmAction(event, '{{ $user->account_status === 'active' ? 'Suspend' : 'Aktifkan' }} akun {{ addslashes($user->name) }}?', 'Ya, Lanjutkan')">
                @csrf @method('PATCH')
                @if($user->account_status === 'active')
                    <button type="submit" class="btn-danger w-full justify-center py-3 text-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Suspend Akun
                    </button>
                @else
                    <button type="submit" class="btn-success w-full justify-center py-3 text-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Aktifkan Akun
                    </button>
                @endif
            </form>
        </div>

    </div>
</div>