<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShuPeriod;
use App\Models\ShuDistribution;
use App\Services\ShuCalculatorService;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ShuController extends Controller
{
    public function __construct(private ShuCalculatorService $calculator, private ReportService $reportService) {}

    public function index()
    {
        $periods = ShuPeriod::withCount('distributions')->latest('year')->get();
        return view('admin.shu.index', compact('periods'));
    }

    public function create()
    {
        return view('admin.shu.form', ['period' => new ShuPeriod()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'year'               => 'required|integer|min:2020|max:2099|unique:shu_period,year',
            'pct_dana_cadangan'  => 'required|numeric|min:0|max:100',
            'pct_jasa_modal'     => 'required|numeric|min:0|max:100',
            'pct_jasa_usaha'     => 'required|numeric|min:0|max:100',
            'pct_dana_pengurus'  => 'required|numeric|min:0|max:100',
            'pct_dana_pendidikan'=> 'required|numeric|min:0|max:100',
            'pct_dana_sosial'    => 'required|numeric|min:0|max:100',
        ]);
        ShuPeriod::create($data);
        return redirect()->route('admin.shu.index')->with('success', "Periode SHU {$data['year']} dibuat.");
    }

    public function show(ShuPeriod $shu)
    {
        $shu->load('distributions.anggota');
        return view('admin.shu.show', compact('shu'));
    }

    public function calculate(ShuPeriod $shu)
    {
        if ($shu->isClosed()) {
            return back()->with('error', 'Periode sudah ditutup/dipublikasikan.');
        }
        $this->calculator->calculate($shu);
        return back()->with('success', 'SHU berhasil dihitung dan didistribusikan.');
    }

    public function publish(ShuPeriod $shu)
    {
        $this->calculator->publish($shu);
        return back()->with('success', "SHU {$shu->year} dipublikasikan ke anggota.");
    }

    public function exportExcel(ShuPeriod $shu)
    {
        return $this->reportService->shuExcel($shu->id);
    }

    public function exportPdf(ShuPeriod $shu)
    {
        return $this->reportService->shuPdf($shu->id);
    }
}
