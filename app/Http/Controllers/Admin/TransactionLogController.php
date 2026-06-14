<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransactionLog;
use Illuminate\Http\Request;

class TransactionLogController extends Controller
{
    public function index(Request $request)
    {
        $query = TransactionLog::with(['anggota', 'pengurus']);

        if ($request->transaction_type) {
            $query->where('transaction_type', $request->transaction_type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->year) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->month) {
            $query->whereMonth('created_at', $request->month);
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        $transactionTypes = [
            'pengajuan_gadai'     => 'Pengajuan Gadai',
            'approval_gadai'      => 'Approval Gadai',
            'pembayaran_gadai'    => 'Pembayaran Gadai',
            'pelunasan_gadai'     => 'Pelunasan Gadai',
            'pembayaran_simpanan' => 'Pembayaran Simpanan',
            'approval_simpanan'   => 'Approval Simpanan',
            'registrasi'          => 'Registrasi Anggota',
        ];

        $counts = [
            'committed'   => TransactionLog::where('status', 'committed')->count(),
            'rolled_back' => TransactionLog::where('status', 'rolled_back')->count(),
        ];

        return view('admin.transaction-logs.index', compact('logs', 'transactionTypes', 'counts'));
    }
}