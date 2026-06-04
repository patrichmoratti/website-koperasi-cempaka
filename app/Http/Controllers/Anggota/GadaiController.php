<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\JenisBarangGadai;
use App\Models\PengajuanGadai;
use App\Models\TransaksiGadai;
use App\Models\PembayaranGadai;
use App\Models\KoperasiInfo;
use Illuminate\Http\Request;

class GadaiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tab  = $request->get('tab', 'aktif');

        $transaksi = TransaksiGadai::where('anggota_id', $user->id)
            ->with('jenisBarang')
            ->when($tab === 'aktif',   fn($q) => $q->whereIn('status',['aktif','diproses','verifikasi']))
            ->when($tab === 'selesai', fn($q) => $q->whereIn('status',['selesai','ditebus','dilelang']))
            ->latest()->paginate(10)->withQueryString();

        $pengajuan = PengajuanGadai::where('anggota_id', $user->id)
            ->with('jenisBarang')
            ->when($tab === 'pending',  fn($q) => $q->where('status','proses'))
            ->when($tab === 'ditolak',  fn($q) => $q->where('status','ditolak'))
            ->latest()->paginate(10)->withQueryString();

        $counts = [
            'aktif'   => $user->transaksiGadai()->whereIn('status',['aktif','diproses','verifikasi'])->count(),
            'pending' => $user->pengajuanGadai()->where('status','proses')->count(),
            'selesai' => $user->transaksiGadai()->whereIn('status',['selesai','ditebus','dilelang'])->count(),
            'ditolak' => $user->pengajuanGadai()->where('status','ditolak')->count(),
        ];

        return view('anggota.gadai.index', compact('tab','transaksi','pengajuan','counts'));
    }

    public function createPengajuan()
    {
        $jenisBarang = JenisBarangGadai::active()->get();
        return view('anggota.gadai.pengajuan-wizard', compact('jenisBarang'));
    }

    public function storePengajuan(Request $request)
    {
        $request->validate([
            'jenis_barang_id'    => 'required|exists:jenis_barang_gadai,id',
            'description'        => 'required|string|min:10',
            'weight_or_quantity' => 'nullable|string|max:100',
            'condition'          => 'required|string|max:100',
            'estimated_value'    => 'required|numeric|min:100000',
            'loan_request_amount'=> 'required|numeric|min:100000',
            'item_photos'        => 'required|array|min:1',
            'item_photos.*'      => 'file|mimes:jpg,jpeg,png|max:2048',
            'supporting_docs'    => 'nullable|array',
            'supporting_docs.*'  => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $jenis = JenisBarangGadai::findOrFail($request->jenis_barang_id);
        $maxLoan = $jenis->maxLoanAmount($request->estimated_value);
        if ($request->loan_request_amount > $maxLoan) {
            return back()->withErrors(['loan_request_amount' => "Maksimal pinjaman: Rp " . number_format($maxLoan, 0, ',', '.')]);
        }

        $photoPaths = [];
        foreach ($request->file('item_photos') as $photo) {
            $photoPaths[] = $photo->store('gadai/photos', 'public');
        }

        $docPaths = [];
        if ($request->hasFile('supporting_docs')) {
            foreach ($request->file('supporting_docs') as $doc) {
                $docPaths[] = $doc->store('gadai/docs', 'public');
            }
        }

        PengajuanGadai::create([
            'anggota_id'          => auth()->id(),
            'jenis_barang_id'     => $request->jenis_barang_id,
            'description'         => $request->description,
            'weight_or_quantity'  => $request->weight_or_quantity,
            'condition'           => $request->condition,
            'estimated_value'     => $request->estimated_value,
            'loan_request_amount' => $request->loan_request_amount,
            'item_photo_paths'    => $photoPaths,
            'supporting_doc_paths'=> $docPaths,
        ]);

        return redirect()->route('anggota.gadai.index')->with('success', 'Pengajuan gadai berhasil diajukan. Tunggu konfirmasi pengurus.');
    }

    public function simulasi()
    {
        $jenisBarang = JenisBarangGadai::active()->get();
        return view('anggota.gadai.simulasi', compact('jenisBarang'));
    }

    public function detail(TransaksiGadai $transaksi)
    {
        abort_if($transaksi->anggota_id !== auth()->id(), 403);
        $transaksi->load(['jenisBarang','pembayaran','pengajuan']);
        return view('anggota.gadai.detail', compact('transaksi'));
    }

    public function bayarForm(TransaksiGadai $transaksi)
    {
        abort_if($transaksi->anggota_id !== auth()->id(), 403);
        abort_if(!in_array($transaksi->status, ['aktif']), 403);

        $info = KoperasiInfo::getInstance();
        $transaksi->load('pembayaran');
        return view('anggota.gadai.bayar', compact('transaksi','info'));
    }

    public function bayarStore(Request $request, TransaksiGadai $transaksi)
    {
        abort_if($transaksi->anggota_id !== auth()->id(), 403);

        $request->validate([
            'payment_type'   => 'required|in:bunga,tebus',
            'amount'         => 'required|numeric|min:1',
            'transfer_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'paid_months'    => 'nullable|array',
        ]);

        $proofPath = $request->file('transfer_proof')->store('bukti-transfer', 'public');

        PembayaranGadai::create([
            'transaksi_gadai_id' => $transaksi->id,
            'payment_type'       => $request->payment_type,
            'paid_months'        => $request->paid_months ?? [],
            'amount'             => $request->amount,
            'transfer_proof_path'=> $proofPath,
            'status'             => 'pending',
        ]);

        return redirect()->route('anggota.gadai.detail', $transaksi)->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu konfirmasi.');
    }
}
