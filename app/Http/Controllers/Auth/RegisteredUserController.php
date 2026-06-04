<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'        => ['required', 'string', 'min:10', 'max:15', 'regex:/^(08|\+62)/'],
            'nik'          => ['required', 'digits:16', 'unique:users'],
            'address'      => ['required', 'string', 'min:10'],
            'password'     => ['required', 'confirmed', Rules\Password::min(8)->mixedCase()->numbers()],
            'ktp_photo'    => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'selfie_photo' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'phone.regex' => 'Nomor HP harus diawali 08 atau +62.',
            'nik.digits'  => 'NIK harus 16 digit.',
        ]);

        $ktpPath    = $request->file('ktp_photo')->store('kyc/ktp', 'public');
        $selfiePath = $request->file('selfie_photo')->store('kyc/selfie', 'public');

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'nik'            => $request->nik,
            'address'        => $request->address,
            'password'       => Hash::make($request->password),
            'role'           => 'anggota',
            'account_status' => 'pending',
            'ktp_photo'      => $ktpPath,
            'selfie_photo'   => $selfiePath,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('kyc.status');
    }
}
