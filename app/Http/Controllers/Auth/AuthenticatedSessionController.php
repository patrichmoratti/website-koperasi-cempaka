<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->account_status === 'pending' && $user->role === 'anggota') {
            return redirect()->route('kyc.status');
        }
        if ($user->account_status === 'rejected') {
            return redirect()->route('kyc.status');
        }
        if ($user->account_status === 'suspended') {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun Anda telah disuspend.']);
        }

        return redirect(match($user->role) {
            'admin'    => '/admin/dashboard',
            'pengurus' => '/pengurus/dashboard',
            default    => '/anggota/dashboard',
        });
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
