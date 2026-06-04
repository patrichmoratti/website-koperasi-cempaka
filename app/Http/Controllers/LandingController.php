<?php

namespace App\Http\Controllers;

use App\Models\KoperasiInfo;
use App\Models\JenisBarangGadai;
use App\Models\User;
use App\Models\TransaksiGadai;
use App\Models\Simpanan;

class LandingController extends Controller
{
    public function index()
    {
        $info       = KoperasiInfo::getInstance();
        $katalog    = JenisBarangGadai::active()->get();
        $stats      = [
            'anggota'  => User::where('role', 'anggota')->where('account_status', 'active')->count(),
            'gadai'    => TransaksiGadai::whereIn('status', ['aktif', 'ditebus', 'selesai'])->count(),
            'simpanan' => Simpanan::where('status', 'confirmed')->sum('amount'),
            'jenis'    => $katalog->count(),
        ];

        return view('landing', compact('info', 'katalog', 'stats'));
    }
}
