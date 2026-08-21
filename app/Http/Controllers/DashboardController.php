<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dispatch to appropriate dashboard based on user role.
     */
    public function index(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        return redirect()->route('mahasiswa.dashboard');
    }
}
