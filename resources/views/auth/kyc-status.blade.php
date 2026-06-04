<x-guest-layout>
    @if($user->account_status === 'pending')
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-mony-text mb-2">Menunggu Verifikasi</h2>
            <p class="text-sm text-mony-muted mb-6">
                Pendaftaran Anda sedang dalam proses verifikasi oleh pengurus koperasi.
                Anda akan mendapat notifikasi setelah akun disetujui.
            </p>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800 text-left mb-6">
                <p class="font-medium mb-1">Data yang sedang diverifikasi:</p>
                <ul class="space-y-1 text-yellow-700">
                    <li>✓ Data diri (NIK, nama, alamat)</li>
                    <li>✓ Foto KTP</li>
                    <li>✓ Foto Selfie dengan KTP</li>
                </ul>
            </div>
            <p class="text-xs text-mony-muted mb-4">Proses verifikasi berlangsung 1–2 hari kerja.</p>
        </div>

    @elseif($user->account_status === 'rejected')
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-mony-text mb-2">Pendaftaran Ditolak</h2>
            <p class="text-sm text-mony-muted mb-4">
                Maaf, pendaftaran Anda tidak dapat disetujui.
            </p>
            @if($user->rejection_reason)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-800 text-left mb-6">
                    <p class="font-medium mb-1">Alasan penolakan:</p>
                    <p class="text-red-700">{{ $user->rejection_reason }}</p>
                </div>
            @endif
            <p class="text-xs text-mony-muted mb-4">
                Hubungi pengurus koperasi untuk informasi lebih lanjut.
            </p>
        </div>
    @endif

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-outline w-full">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Keluar
        </button>
    </form>
</x-guest-layout>
