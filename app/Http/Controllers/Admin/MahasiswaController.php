<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\Role;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of mahasiswa users.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $query = User::where('role', Role::Mahasiswa)
            ->with('profile')
            ->orderBy('created_at', 'desc');

        // Search by name or email
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $mahasiswas = $query->paginate(10)->withQueryString();

        return view('admin.mahasiswa.index', compact('mahasiswas', 'search'));
    }
}
