<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Simpanan;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function index(Request $request)
    {
        $query = Simpanan::with('anggota');

        if ($request->search) {
            $q = $request->search;
            $query->whereHas('anggota', fn($sq) => $sq->where('name','like',"%{$q}%"));
        }
        if ($request->type)  $query->where('type', $request->type);
        if ($request->month) $query->where('period_month', $request->month);
        if ($request->year)  $query->where('period_year', $request->year);
        if ($request->status) $query->where('status', $request->status);

        $simpanan = $query->latest()->paginate(20)->withQueryString();

        $summary = [
            'total_pokok' => Simpanan::where('type','pokok')->where('status','confirmed')->sum('amount'),
            'total_wajib' => Simpanan::where('type','wajib')->where('status','confirmed')->sum('amount'),
        ];

        $perAnggota = User::where('role','anggota')->where('account_status','active')
            ->withSum(['simpanan as total_simpanan' => fn($q) => $q->where('status','confirmed')], 'amount')
            ->having('total_simpanan', '>', 0)
            ->orderByDesc('total_simpanan')
            ->paginate(15);

        return view('admin.simpanan.index', compact('simpanan','summary','perAnggota'));
    }

    public function exportExcel()
    {
        return $this->reportService->simpananExcel();
    }
}
