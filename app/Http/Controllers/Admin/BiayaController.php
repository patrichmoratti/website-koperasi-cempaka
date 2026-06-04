<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BiayaOperasional;
use Illuminate\Http\Request;

class BiayaController extends Controller
{
    public function index(Request $request)
    {
        $query = BiayaOperasional::with('recorder');
        if ($request->category) $query->where('category', $request->category);
        if ($request->month)    $query->whereMonth('date', $request->month);
        if ($request->year)     $query->whereYear('date', $request->year);

        $biaya = $query->latest('date')->paginate(20)->withQueryString();

        $totalMonth = BiayaOperasional::whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount');
        $totalYear  = BiayaOperasional::whereYear('date', now()->year)->sum('amount');

        $byCategory = BiayaOperasional::selectRaw('category, SUM(amount) as total')
            ->whereYear('date', $request->year ?? now()->year)
            ->groupBy('category')->get();

        return view('admin.biaya.index', compact('biaya','totalMonth','totalYear','byCategory'));
    }

    public function create()
    {
        return view('admin.biaya.form', ['biaya' => new BiayaOperasional()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date'        => 'required|date',
            'category'    => 'required|string|max:100',
            'amount'      => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
        ]);
        $data['recorded_by'] = auth()->id();
        BiayaOperasional::create($data);
        return redirect()->route('admin.biaya.index')->with('success', 'Biaya operasional berhasil dicatat.');
    }

    public function edit(BiayaOperasional $biaya)
    {
        return view('admin.biaya.form', compact('biaya'));
    }

    public function update(Request $request, BiayaOperasional $biaya)
    {
        $data = $request->validate([
            'date'        => 'required|date',
            'category'    => 'required|string|max:100',
            'amount'      => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
        ]);
        $biaya->update($data);
        return redirect()->route('admin.biaya.index')->with('success', 'Biaya diperbarui.');
    }

    public function destroy(BiayaOperasional $biaya)
    {
        $biaya->delete();
        return back()->with('success', 'Biaya dihapus.');
    }
}
