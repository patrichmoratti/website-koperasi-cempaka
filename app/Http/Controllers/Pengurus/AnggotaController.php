<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role','anggota');
        if ($request->search) {
            $q = $request->search;
            $query->where(fn($sq) => $sq->where('name','like',"%{$q}%")->orWhere('nik','like',"%{$q}%")->orWhere('email','like',"%{$q}%"));
        }
        $anggota = $query->with(['transaksiGadai.jenisBarang','simpanan'])->latest()->paginate(15)->withQueryString();
        return view('pengurus.anggota.index', compact('anggota'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6',
            'phone'        => 'required|string|max:20',
            'nik'          => 'required|string|size:16|unique:users,nik',
            'address'      => 'required|string|max:500',
            'ktp_photo'    => 'nullable|image|max:5120',
            'selfie_photo' => 'nullable|image|max:5120',
        ]);

        $ktpPath    = $request->file('ktp_photo')?->store('kyc/ktp', 'public');
        $selfiePath = $request->file('selfie_photo')?->store('kyc/selfie', 'public');

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'phone'          => $request->phone,
            'nik'            => $request->nik,
            'address'        => $request->address,
            'role'           => 'anggota',
            'account_status' => 'active',
            'ktp_photo'      => $ktpPath,
            'selfie_photo'   => $selfiePath,
        ]);

        return redirect()->route('pengurus.anggota.index')
            ->with('success', "Anggota {$user->name} berhasil didaftarkan.");
    }

    public function show(User $user)
    {
        $user->load(['pengajuanGadai.jenisBarang','transaksiGadai','simpanan']);
        return view('pengurus.anggota.show', compact('user'));
    }
}