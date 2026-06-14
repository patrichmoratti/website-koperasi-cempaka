{{--
    Create pengurus account popup for admin Manajemen Pengurus page.
    Enclosing Alpine scope must expose: createOpen (bool)
--}}
<div x-show="createOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @click.self="createOpen = false"
     @keydown.escape.window="createOpen = false"
     style="background:rgba(8,20,12,0.72); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);"
     x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    <div @click.stop
         class="bg-white popup-sheet w-full shadow-2xl scrollbar-hide"
         style="max-width:520px; max-height:90vh; overflow-y:auto; border-radius:24px;"
         x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

        {{-- Header --}}
        <div class="relative overflow-hidden rounded-t-3xl px-6 py-5"
             style="background:linear-gradient(135deg, var(--green) 0%, var(--green2) 100%);">
            <div class="absolute inset-0 opacity-[0.05]"
                 style="background-image:radial-gradient(circle,#fff 1px,transparent 1px); background-size:14px 14px;"></div>
            <div class="relative flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0"
                         style="background:rgba(255,255,255,0.15);">
                        <img src="{{ asset('images/logo-mony.png') }}" alt="MONY" class="w-7 h-7 object-contain">
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white leading-tight">Tambah Akun Pengurus</h2>
                        <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.6);">Buat akun staf operasional koperasi</p>
                    </div>
                </div>
                <button type="button" @click="createOpen = false"
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

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.pengurus.store') }}" x-data="{ showPwd: false }">
            @csrf

            <div class="px-5 py-5 space-y-4">

                {{-- Nama + Email --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-input" placeholder="Nama lengkap pengurus" required>
                    </div>
                    <div>
                        <label class="form-label">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="email@domain.com" required>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label class="form-label">Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input :type="showPwd ? 'text' : 'password'" name="password"
                               class="form-input pr-10" placeholder="Min. 6 karakter" required minlength="6">
                        <button type="button" @click="showPwd = !showPwd"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-mony-muted hover:text-mony-text transition-colors">
                            <svg x-show="!showPwd" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPwd" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-mony-muted mt-1">Pengurus bisa mengubah password setelah login.</p>
                </div>

                {{-- No. HP --}}
                <div>
                    <label class="form-label">No. HP</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="08XXXXXXXXXX">
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-input" rows="2"
                              placeholder="Alamat lengkap (opsional)">{{ old('address') }}</textarea>
                </div>

                {{-- Info --}}
                <div class="rounded-xl px-4 py-3 text-xs flex items-start gap-2.5"
                     style="background:var(--green-light); border:1px solid #c6e4a8; color:var(--green2);">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" style="color:var(--green)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Akun pengurus langsung berstatus <strong>Aktif</strong> dan dapat mengakses seluruh fitur operasional koperasi (Konfirmasi, Manajemen Gadai, Manajemen Anggota, Biaya, Pesan, Riwayat).</span>
                </div>

                @if($errors->any())
                <div class="rounded-xl px-4 py-3 text-xs bg-red-50 border border-red-200 text-red-700">
                    <ul class="space-y-0.5">
                        @foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach
                    </ul>
                </div>
                @endif

            </div>

            {{-- Footer --}}
            <div class="px-5 pb-5 flex gap-2">
                <button type="button" @click="createOpen = false" class="btn-secondary flex-1 justify-center py-3 text-sm">
                    Batal
                </button>
                <button type="submit" class="btn-success flex-1 justify-center py-3 text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Buat Akun Pengurus
                </button>
            </div>

        </form>
    </div>
</div>