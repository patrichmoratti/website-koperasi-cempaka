<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\BiayaOperasional;
use Illuminate\Http\Request;

class BiayaController extends Controller
{
    public function create()
    {
        return view('pengurus.biaya.create');
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
        return redirect()->route('pengurus.dashboard')->with('success', 'Biaya operasional berhasil dicatat.');
    }
}
