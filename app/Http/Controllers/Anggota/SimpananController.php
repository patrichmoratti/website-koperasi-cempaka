<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Simpanan;
use App\Models\KoperasiInfo;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $simpanan = $user->simpanan()->latest()->paginate(20);
        $summary  = [
            'pokok' => $user->totalSimpananPokok(),
            'wajib' => $user->totalSimpananWajib(),
            'total' => $user->totalSimpanan(),
        ];
        return view('anggota.simpanan.index', compact('simpanan','summary'));
    }

    public function bayarForm()
    {
        $info = KoperasiInfo::getInstance();
        return view('anggota.simpanan.bayar', compact('info'));
    }

    public function bayarStore(Request $request)
    {
        $request->validate([
            'type'           => 'required|in:pokok,wajib',
            'period_month'   => 'required_if:type,wajib|nullable|integer|min:1|max:12',
            'period_year'    => 'required_if:type,wajib|nullable|integer|min:2020|max:2099',
            'amount'         => 'required|numeric|min:1',
            'transfer_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $proofPath = $request->file('transfer_proof')->store('bukti-simpanan', 'public');

        Simpanan::create([
            'anggota_id'         => auth()->id(),
            'type'               => $request->type,
            'period_month'       => $request->period_month,
            'period_year'        => $request->period_year,
            'amount'             => $request->amount,
            'transfer_proof_path'=> $proofPath,
            'status'             => 'pending',
        ]);

        return redirect()->route('anggota.simpanan.index')->with('success', 'Bukti simpanan berhasil dikirim. Menunggu konfirmasi.');
    }
}
