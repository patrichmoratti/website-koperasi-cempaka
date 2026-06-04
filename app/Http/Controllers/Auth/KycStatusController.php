<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KycStatusController extends Controller
{
    public function show(): View
    {
        return view('auth.kyc-status', ['user' => auth()->user()]);
    }
}
