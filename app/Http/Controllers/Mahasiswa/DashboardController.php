<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the mahasiswa dashboard.
     */
    public function index(Request $request)
    {
        return view('mahasiswa.dashboard');
    }
}
