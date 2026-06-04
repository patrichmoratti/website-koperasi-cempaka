<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountStatusMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->account_status === 'suspended') {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['email' => 'Akun Anda telah disuspend. Hubungi pengurus.']);
        }

        if ($user->account_status === 'rejected') {
            return redirect()->route('kyc.status');
        }

        if ($user->account_status === 'pending' && $user->role === 'anggota') {
            return redirect()->route('kyc.status');
        }

        return $next($request);
    }
}
