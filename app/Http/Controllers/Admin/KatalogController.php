<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisBarangGadai;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = JenisBarangGadai::query();
        if ($request->search) {
            $query->where('name','like',"%{$request->search}%");
        }
        if ($request->category) {
            $query->where('category', $request->category);
        }
        $katalog = $query->latest()->paginate(15)->withQueryString();
        return view('admin.katalog.index', compact('katalog'));
    }

    public function create()
    {
        return view('admin.katalog.form', ['item' => new JenisBarangGadai()]);
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, $this->rules());
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('katalog', 'public');
        }
        $data['conditions']   = $this->parseLines($request->conditions_raw);
        $data['requirements'] = $this->parseLines($request->requirements_raw);
        $data['brands']       = $this->parseBrands($request->brands_raw);
        JenisBarangGadai::create($data);
        return redirect()->route('admin.katalog.index')->with('success', 'Jenis barang berhasil ditambah.');
    }

    public function edit(JenisBarangGadai $katalog)
    {
        return view('admin.katalog.form', ['item' => $katalog]);
    }

    public function update(Request $request, JenisBarangGadai $katalog)
    {
        $data = $this->validate($request, $this->rules($katalog->id));
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('katalog', 'public');
        }
        $data['conditions']   = $this->parseLines($request->conditions_raw);
        $data['requirements'] = $this->parseLines($request->requirements_raw);
        $data['brands']       = $this->parseBrands($request->brands_raw);
        $katalog->update($data);
        return redirect()->route('admin.katalog.index')->with('success', 'Jenis barang berhasil diperbarui.');
    }

    public function destroy(JenisBarangGadai $katalog)
    {
        $katalog->update(['is_active' => false]);
        return back()->with('success', 'Jenis barang dinonaktifkan.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'name'               => 'required|string|max:100',
            'category'           => 'required|string|max:100',
            'description'        => 'nullable|string',
            'base_value_per_unit'=> 'required|numeric|min:0',
            'unit'               => 'required|string|max:20',
            'max_loan_percentage'=> 'required|numeric|min:1|max:100',
            'image'              => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'is_active'          => 'boolean',
        ];
    }

    private function parseLines(?string $raw): array
    {
        if (!$raw) return [];
        return array_values(array_filter(array_map('trim', explode("\n", $raw))));
    }

    private function parseBrands(?string $raw): array
    {
        if (!$raw) return [];
        $brands = [];
        foreach (explode("\n", $raw) as $line) {
            $parts = explode('|', $line);
            if (count($parts) >= 1 && trim($parts[0])) {
                $brands[] = ['name' => trim($parts[0]), 'value' => isset($parts[1]) ? (int) trim($parts[1]) : 0];
            }
        }
        return $brands;
    }
}
