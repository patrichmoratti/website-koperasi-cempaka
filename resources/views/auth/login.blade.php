<x-guest-layout>
    <div class="mb-7">
        <h2 class="font-display font-bold text-2xl mb-1" style="color: var(--text)">Selamat Datang Kembali</h2>
        <p class="text-sm" style="color: var(--text-muted)">Masuk ke akun MONY Anda untuk melanjutkan</p>
    </div>

    @if (session('status'))
        <div class="mb-5 px-4 py-3 rounded-2xl text-sm" style="background: var(--green-light); color: var(--green)">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" x-data="{ showPass: false }">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-input @error('email') !border-red-400 @enderror"
                   placeholder="email@contoh.com" required autofocus autocomplete="username">
            @error('email') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="mb-5">
            <label for="password" class="form-label">Password</label>
            <div class="relative">
                <input id="password" :type="showPass ? 'text' : 'password'" name="password"
                       class="form-input pr-10 @error('password') !border-red-400 @enderror"
                       placeholder="••••••••" required autocomplete="current-password">
                <button type="button" @click="showPass = !showPass"
                        class="absolute right-3 top-1/2 -translate-y-1/2 transition-colors"
                        style="color: var(--text-muted)"
                        onmouseover="this.style.color='var(--green)'" onmouseout="this.style.color='var(--text-muted)'">
                    <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between mb-7">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember"
                       class="rounded border-gray-300 transition-colors"
                       style="accent-color: var(--green)">
                <span class="text-sm" style="color: var(--text-muted)">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium transition-colors"
                   style="color: var(--green)"
                   onmouseover="this.style.color='var(--green2)'" onmouseout="this.style.color='var(--green)'">
                    Lupa password?
                </a>
            @endif
        </div>

        <button type="submit" class="w-full py-3 rounded-2xl text-sm font-semibold text-white transition-all duration-200"
                style="background: var(--green); box-shadow: 0 4px 16px rgba(26,61,46,0.25)"
                onmouseover="this.style.background='var(--green2)'; this.style.transform='translateY(-1px)'"
                onmouseout="this.style.background='var(--green)'; this.style.transform='translateY(0)'">
            Masuk ke Akun
        </button>

        <div class="flex items-center gap-4 my-6">
            <div class="flex-1 h-px" style="background: rgba(26,61,46,0.1)"></div>
            <span class="text-xs" style="color: var(--text-muted)">atau</span>
            <div class="flex-1 h-px" style="background: rgba(26,61,46,0.1)"></div>
        </div>

        <a href="{{ route('register') }}"
           class="flex items-center justify-center w-full py-3 rounded-2xl text-sm font-medium border-2 transition-all duration-200"
           style="color: var(--text); border-color: rgba(26,61,46,0.15); background: transparent"
           onmouseover="this.style.borderColor='var(--green)'; this.style.color='var(--green)'"
           onmouseout="this.style.borderColor='rgba(26,61,46,0.15)'; this.style.color='var(--text)'">
            Belum punya akun? <strong class="ml-1" style="color: var(--gold-dark)">Daftar Gratis</strong>
        </a>
    </form>
</x-guest-layout>
