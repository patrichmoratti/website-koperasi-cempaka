<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfilController extends Controller
{
    public function index()
    {
        return view('anggota.profil', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|min:10|max:15|regex:/^(08|\+62)/',
            'address' => 'required|string|min:10',
        ]);
        $user->update($request->only('name','phone','address'));
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'current_password' => ['required', function($attr, $val, $fail) use ($user) {
                if (!Hash::check($val, $user->password)) $fail('Password saat ini salah.');
            }],
            'password' => ['required', 'confirmed', Rules\Password::min(8)->mixedCase()->numbers()],
        ]);
        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password berhasil diubah.');
    }
}
