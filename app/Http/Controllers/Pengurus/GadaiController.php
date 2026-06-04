<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\PengajuanGadai;
use App\Models\TransaksiGadai;
use Illuminate\Http\Request;

class GadaiController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pengajuan');

        $pengajuan = PengajuanGadai::with(['anggota','jenisBarang'])
            ->where('status', $tab === 'pengajuan' ? 'proses' : ($tab === 'diterima' ? 'diterima' : 'ditolak'))
            ->latest()->paginate(15)->withQueryString();

        $transaksi = TransaksiGadai::with(['anggota','jenisBarang'])
            ->where('status','aktif')
            ->latest()->paginate(15)->withQueryString();

        return view('pengurus.gadai.index', compact('tab','pengajuan','transaksi'));
    }

    public function showPengajuan(PengajuanGadai $pengajuan)
    {
        $pengajuan->load(['anggota','jenisBarang']);
        return view('pengurus.gadai.pengajuan-detail', compact('pengajuan'));
    }

    public function showTransaksi(TransaksiGadai $transaksi)
    {
        $transaksi->load(['anggota','jenisBarang','pembayaran']);
        return view('pengurus.gadai.transaksi-detail', compact('transaksi'));
    }
}
