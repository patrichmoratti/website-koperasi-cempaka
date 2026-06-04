<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KoperasiInfo;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $info = KoperasiInfo::getInstance();
        return view('admin.pengaturan', compact('info'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'vision'             => 'nullable|string',
            'mission'            => 'nullable|string',
            'address'            => 'nullable|string',
            'phone'              => 'nullable|string|max:20',
            'email'              => 'nullable|email|max:255',
            'bank_name'          => 'nullable|string|max:100',
            'bank_account_number'=> 'nullable|string|max:30',
            'bank_account_name'  => 'nullable|string|max:100',
            'terms_and_conditions' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('koperasi', 'public');
        }

        KoperasiInfo::getInstance()->update($data);
        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
