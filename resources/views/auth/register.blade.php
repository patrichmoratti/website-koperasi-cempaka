<x-guest-layout>
    <h2 class="text-xl font-semibold text-mony-text mb-1">Daftar Anggota</h2>
    <p class="text-sm text-mony-muted mb-6">Isi data lengkap Anda untuk bergabung</p>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
          x-data="{ showPass: false, ktpPreview: null, selfiePreview: null }">
        @csrf

        <div class="grid grid-cols-1 gap-4">

            <div>
                <label for="name" class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name') }}"
                       class="form-input @error('name') border-red-400 @enderror"
                       placeholder="Sesuai KTP" required>
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="nik" class="form-label">NIK (16 digit) <span class="text-red-500">*</span></label>
                <input id="nik" type="text" name="nik" value="{{ old('nik') }}"
                       class="form-input @error('nik') border-red-400 @enderror"
                       placeholder="3201234567890001" maxlength="16" required>
                @error('nik') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="form-label">Email <span class="text-red-500">*</span></label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="form-input @error('email') border-red-400 @enderror"
                       placeholder="email@contoh.com" required>
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="phone" class="form-label">No. HP <span class="text-red-500">*</span></label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                       class="form-input @error('phone') border-red-400 @enderror"
                       placeholder="08123456789" required>
                @error('phone') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="address" class="form-label">Alamat Lengkap <span class="text-red-500">*</span></label>
                <textarea id="address" name="address" rows="2"
                          class="form-input @error('address') border-red-400 @enderror"
                          placeholder="Jl. Contoh No. 1, Kel. ..., Kec. ..., Kota ..."
                          required>{{ old('address') }}</textarea>
                @error('address') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="form-label">Password <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input id="password" :type="showPass ? 'text' : 'password'" name="password"
                           class="form-input pr-10 @error('password') border-red-400 @enderror"
                           placeholder="Min. 8 karakter, huruf besar & angka" required>
                    <button type="button" @click="showPass = !showPass"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-mony-muted hover:text-mony-text">
                        <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('password') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-red-500">*</span></label>
                <input id="password_confirmation" :type="showPass ? 'text' : 'password'" name="password_confirmation"
                       class="form-input" placeholder="Ulangi password" required>
            </div>

            {{-- KTP Photo --}}
            <div>
                <label class="form-label">Foto KTP <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-primary transition-colors"
                     @dragover.prevent @drop.prevent="
                        const f = $event.dataTransfer.files[0];
                        if(f) { ktpPreview = URL.createObjectURL(f); $refs.ktpInput.files = $event.dataTransfer.files; }
                     ">
                    <input type="file" name="ktp_photo" accept="image/*" class="hidden" x-ref="ktpInput"
                           @change="ktpPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                    <template x-if="!ktpPreview">
                        <div @click="$refs.ktpInput.click()" class="cursor-pointer">
                            <svg class="w-8 h-8 text-mony-muted mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-mony-muted">Klik atau drag foto KTP</p>
                            <p class="text-xs text-mony-muted">JPG/PNG, maks. 2MB</p>
                        </div>
                    </template>
                    <template x-if="ktpPreview">
                        <div class="relative">
                            <img :src="ktpPreview" class="w-full h-32 object-cover rounded-lg">
                            <button type="button" @click="ktpPreview = null; $refs.ktpInput.value = ''"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">×</button>
                        </div>
                    </template>
                </div>
                @error('ktp_photo') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Selfie Photo --}}
            <div>
                <label class="form-label">Foto Selfie dengan KTP <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-primary transition-colors"
                     @dragover.prevent @drop.prevent="
                        const f = $event.dataTransfer.files[0];
                        if(f) { selfiePreview = URL.createObjectURL(f); $refs.selfieInput.files = $event.dataTransfer.files; }
                     ">
                    <input type="file" name="selfie_photo" accept="image/*" class="hidden" x-ref="selfieInput"
                           @change="selfiePreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                    <template x-if="!selfiePreview">
                        <div @click="$refs.selfieInput.click()" class="cursor-pointer">
                            <svg class="w-8 h-8 text-mony-muted mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <p class="text-sm text-mony-muted">Klik atau drag foto selfie + KTP</p>
                            <p class="text-xs text-mony-muted">JPG/PNG, maks. 2MB</p>
                        </div>
                    </template>
                    <template x-if="selfiePreview">
                        <div class="relative">
                            <img :src="selfiePreview" class="w-full h-32 object-cover rounded-lg">
                            <button type="button" @click="selfiePreview = null; $refs.selfieInput.value = ''"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">×</button>
                        </div>
                    </template>
                </div>
                @error('selfie_photo') <p class="form-error">{{ $message }}</p> @enderror
            </div>

        </div>

        <button type="submit" class="btn-primary w-full py-2.5 mt-6">
            Daftar Sekarang
        </button>

        <p class="text-center text-sm text-mony-muted mt-4">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-primary font-medium hover:text-primary-dark">Masuk di sini</a>
        </p>
    </form>
</x-guest-layout>
