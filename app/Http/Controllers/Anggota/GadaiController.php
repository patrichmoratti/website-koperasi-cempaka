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
        $tab  = $request->get('tab', 'proses');

        $pengajuanProses = PengajuanGadai::where('anggota_id', $user->id)
            ->where('status', 'proses')
            ->with('jenisBarang')
            ->latest('submitted_at')->get();

        $pengajuanVerifikasi = PengajuanGadai::where('anggota_id', $user->id)
            ->where(function ($q) {
                $q->where('status', 'ditolak')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'diterima')->doesntHave('transaksi');
                  });
            })
            ->with(['jenisBarang', 'processor'])
            ->latest('processed_at')->get();

        $transaksiAktif = TransaksiGadai::where('anggota_id', $user->id)
            ->whereIn('status', ['aktif', 'diproses', 'verifikasi', 'menunggu_lelang'])
            ->with(['jenisBarang', 'pembayaran', 'pengajuan'])
            ->latest()->get();

        $transaksiSelesai = TransaksiGadai::where('anggota_id', $user->id)
            ->whereIn('status', ['selesai', 'ditebus', 'dilelang'])
            ->with(['jenisBarang', 'pembayaran', 'pengajuan', 'lelang'])
            ->latest()->get();

        $counts = [
            'proses'     => $pengajuanProses->count(),
            'verifikasi' => $pengajuanVerifikasi->count(),
            'aktif'      => $transaksiAktif->count(),
            'selesai'    => $transaksiSelesai->count(),
        ];

        return view('anggota.gadai.index', compact(
            'tab', 'user', 'counts',
            'pengajuanProses', 'pengajuanVerifikasi',
            'transaksiAktif', 'transaksiSelesai'
        ));
    }

    public function cancelPengajuan(PengajuanGadai $pengajuan)
    {
        abort_if($pengajuan->anggota_id !== auth()->id(), 403);
        abort_if($pengajuan->status !== 'proses', 422);

        $pengajuan->update(['status' => 'dibatalkan']);

        return redirect()->route('anggota.gadai.index', ['tab' => 'proses'])
            ->with('success', 'Pengajuan gadai berhasil dibatalkan.');
    }

    public function createPengajuan()
    {
        $jenisBarang = JenisBarangGadai::active()->get();

        $catalogPhotos = [
            'Laptop'      => 'laptop.jpg',
            'Smartphone'  => 'handphone.jpg',
            'TV'          => 'television.jpg',
            'Kulkas'      => 'kulkas.webp',
            'Rice Cooker' => 'rice-cooker.png',
            'Kompor'      => 'kompor.jpg',
            'Kipas'       => 'kipas.jpeg',
            'Emas'        => 'perhiasan.jpg',
            'Motor'       => 'motor.webp',
            'Sepeda'      => 'sepeda.jpg',
        ];

        $jenisItems = $jenisBarang->map(function ($j) use ($catalogPhotos) {
            $photo = null;
            foreach ($catalogPhotos as $key => $file) {
                if (str_contains($j->name, $key)) { $photo = $file; break; }
            }
            return [
                'id'           => $j->id,
                'name'         => $j->name,
                'unit'         => $j->unit,
                'maxPct'       => (float) $j->max_loan_percentage,
                'requirements' => $j->requirements ?? [],
                'image'        => $j->image_path
                    ? asset('storage/' . $j->image_path)
                    : ($photo ? asset('images/' . $photo) : null),
                'brands' => collect($j->brands ?? [])->map(fn ($b) => [
                    'name'  => $b['name'] ?? '-',
                    'value' => (int) ($b['value'] ?? 0),
                ])->values(),
            ];
        })->values();

        return view('anggota.gadai.pengajuan-wizard', compact('jenisBarang', 'jenisItems'));
    }

    public function storePengajuan(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'jenis_barang_id'    => 'required|exists:jenis_barang_gadai,id',
            'brand_name'         => 'nullable|string|max:100',
            'description'        => 'required|string|min:10',
            'weight_or_quantity' => 'nullable|numeric|min:1',
            'estimated_value'    => 'required|numeric|min:100000',
            'loan_request_amount'=> 'required|numeric|min:100000',
            'item_photos'        => 'required|array|min:3|max:3',
            'item_photos.*'      => 'file|mimes:jpg,jpeg,png|max:2048',
            'item_video'         => 'required|file|mimes:mp4,mov,avi,webm|max:20480',
            'supporting_docs'    => 'nullable|array|max:2',
            'supporting_docs.*'  => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $jenisBarang = JenisBarangGadai::findOrFail($request->jenis_barang_id);
        $maxPct  = (float) ($jenisBarang->max_loan_percentage ?? 100);
        $maxLoan = (int) floor($request->estimated_value * $maxPct / 100);
        $minLoan = (int) floor($maxLoan * 90 / 100);

        if ($request->loan_request_amount < $minLoan) {
            $msg = "Minimal pinjaman: Rp " . number_format($minLoan, 0, ',', '.') . " (90% dari maksimal {$maxPct}% nilai taksiran)";
            if ($request->ajax()) return response()->json(['success' => false, 'message' => $msg], 422);
            return back()->withErrors(['loan_request_amount' => $msg])->withInput();
        }
        if ($request->loan_request_amount > $maxLoan) {
            $msg = "Maksimal pinjaman: Rp " . number_format($maxLoan, 0, ',', '.') . " ({$maxPct}% dari nilai taksiran)";
            if ($request->ajax()) return response()->json(['success' => false, 'message' => $msg], 422);
            return back()->withErrors(['loan_request_amount' => $msg])->withInput();
        }

        $photoPaths = [];
        foreach ($request->file('item_photos') as $photo) {
            $photoPaths[] = $photo->store('gadai/photos', 'public');
        }

        $videoPath = null;
        if ($request->hasFile('item_video')) {
            $videoPath = $request->file('item_video')->store('gadai/videos', 'public');
        }

        $docPaths = [];
        if ($request->hasFile('supporting_docs')) {
            foreach ($request->file('supporting_docs') as $doc) {
                $docPaths[] = $doc->store('gadai/docs', 'public');
            }
        }

        $pengajuan = PengajuanGadai::create([
            'anggota_id'          => auth()->id(),
            'jenis_barang_id'     => $request->jenis_barang_id,
            'brand_name'          => $request->brand_name,
            'description'         => $request->description,
            'weight_or_quantity'  => $request->weight_or_quantity,
            'condition'           => 'Sesuai kondisi barang',
            'estimated_value'     => $request->estimated_value,
            'loan_request_amount' => $request->loan_request_amount,
            'item_photo_paths'    => $photoPaths,
            'item_video_path'     => $videoPath,
            'supporting_doc_paths'=> $docPaths,
            'status'              => 'proses',
            'submitted_at'        => now(),
        ]);

        if ($request->ajax()) {
            $jenis = \App\Models\JenisBarangGadai::find($request->jenis_barang_id);
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $jenis->name ?? 'ITM'), 0, 3));
            $refNumber = $prefix . '-' . now()->format('dmy') . '-' . str_pad($pengajuan->id, 3, '0', STR_PAD_LEFT);
            return response()->json([
                'success'    => true,
                'ref_number' => $refNumber,
            ]);
        }

        return redirect()->route('anggota.gadai.index')->with('success', 'Pengajuan gadai berhasil diajukan. Tunggu konfirmasi pengurus.');
    }

    public function simulasi()
    {
        $jenisBarang = JenisBarangGadai::active()->get();

        // Sama dengan $catalogMeta pada landing page: mencocokkan nama jenis barang
        // dengan foto referensi yang sudah diunggah ke public/images.
        $catalogPhotos = [
            'Laptop'      => 'laptop.jpg',
            'Smartphone'  => 'handphone.jpg',
            'TV'          => 'television.jpg',
            'Kulkas'      => 'kulkas.webp',
            'Rice Cooker' => 'rice-cooker.png',
            'Kompor'      => 'kompor.jpg',
            'Kipas'       => 'kipas.jpeg',
            'Emas'        => 'perhiasan.jpg',
            'Motor'       => 'motor.webp',
            'Sepeda'      => 'sepeda.jpg',
        ];

        $simulasiItems = $jenisBarang->map(function ($j) use ($catalogPhotos) {
            $photo = null;
            foreach ($catalogPhotos as $key => $file) {
                if (str_contains($j->name, $key)) {
                    $photo = $file;
                    break;
                }
            }

            return [
                'id'       => $j->id,
                'name'     => $j->name,
                'category' => $j->category,
                'unit'     => $j->unit,
                'maxPct'   => (float) $j->max_loan_percentage,
                'image'    => $j->image_path
                    ? asset('storage/' . $j->image_path)
                    : ($photo ? asset('images/' . $photo) : null),
                'brands'   => collect($j->brands ?? [])->map(fn ($b) => [
                    'name'  => $b['name'] ?? '-',
                    'value' => (int) ($b['value'] ?? 0),
                ])->values(),
            ];
        })->values();

        return view('anggota.gadai.simulasi', compact('jenisBarang', 'simulasiItems'));
    }

    public function detail(TransaksiGadai $transaksi)
    {
        abort_if($transaksi->anggota_id !== auth()->id(), 403);
        $transaksi->load(['jenisBarang','pembayaran','pengajuan']);
        $info = KoperasiInfo::getInstance();
        return view('anggota.gadai.detail', compact('transaksi', 'info'));
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

        $pembayaran = PembayaranGadai::create([
            'transaksi_gadai_id' => $transaksi->id,
            'payment_type'       => $request->payment_type,
            'paid_months'        => $request->paid_months ?? [],
            'amount'             => $request->amount,
            'transfer_proof_path'=> $proofPath,
            'status'             => 'pending',
            'submitted_at'       => now(),
        ]);

        if ($request->ajax()) {
            $months = $request->paid_months ?? [];
            return response()->json([
                'success'  => true,
                'title'    => 'Pembayaran Terkirim!',
                'subtitle' => $transaksi->reference_number . ' — ' . $transaksi->jenisBarang?->name,
                'rows'     => array_values(array_filter([
                    ['Jenis Pembayaran', $pembayaran->payment_type_label],
                    count($months) ? ['Bulan Dibayar', count($months) . ' bulan'] : null,
                    ['Jumlah Transfer', 'Rp ' . number_format($pembayaran->amount, 0, ',', '.')],
                    ['Tanggal Kirim', $pembayaran->submitted_at->format('d M Y, H:i')],
                ])),
            ]);
        }

        return redirect()->route('anggota.gadai.detail', $transaksi)->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu konfirmasi.');
    }
}
