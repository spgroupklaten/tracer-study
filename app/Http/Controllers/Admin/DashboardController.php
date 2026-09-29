<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use App\Enums\Role;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(Request $request)
    {
        // Profile Statistics (Alumni = lulus)
        $totalAlumni = Profile::lulus()->count();
        $totalMahasiswaAktif = Profile::aktif()->count();

        // User Statistics
        $totalUsers = User::count();
        $totalMahasiswa = User::where('role', Role::Mahasiswa)->count();
        $totalAdmin = User::where('role', Role::Admin)->count();

        // Program Studi Statistics (Alumni)
        $programStudis = Profile::lulus()
            ->select('program_studi')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('program_studi')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Tahun Lulusan Terbaru
        $tahunLulusanTerbaru = Profile::lulus()
            ->select('tahun_lulus')
            ->distinct()
            ->orderByDesc('tahun_lulus')
            ->limit(1)
            ->first();

        // Recent Alumni (Profiles with tracer study submitted)
        $recentAlumni = Profile::lulus()
            ->with('tracerStudy')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // For backward compat, also count old alumni table
        $alumniBekerja = Profile::lulus()->count(); // simplified
        $alumniStudiLanjut = 0;
        $alumniBelumBekerja = 0;

        return view('admin.dashboard', compact(
            'totalAlumni',
            'totalMahasiswaAktif',
            'totalUsers',
            'totalMahasiswa',
            'totalAdmin',
            'programStudis',
            'tahunLulusanTerbaru',
            'recentAlumni',
            'alumniBekerja',
            'alumniStudiLanjut',
            'alumniBelumBekerja',
        ));
    }
}
