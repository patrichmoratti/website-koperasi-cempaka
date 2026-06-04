<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role','anggota');
        if ($request->search) {
            $q = $request->search;
            $query->where(fn($sq) => $sq->where('name','like',"%{$q}%")->orWhere('nik','like',"%{$q}%")->orWhere('email','like',"%{$q}%"));
        }
        $anggota = $query->latest()->paginate(15)->withQueryString();
        return view('pengurus.anggota.index', compact('anggota'));
    }

    public function show(User $user)
    {
        $user->load(['pengajuanGadai.jenisBarang','transaksiGadai','simpanan']);
        return view('pengurus.anggota.show', compact('user'));
    }
}
