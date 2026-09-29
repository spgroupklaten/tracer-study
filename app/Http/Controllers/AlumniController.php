<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    /**
     * Display a listing of alumni profiles (PUBLIC - limited data).
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $program_studi = $request->input('program_studi', '');
        $tahun_lulus = $request->input('tahun_lulus', '');

        $query = Profile::lulus()
            ->with('user:id,name');

        // Apply search filter (only nim and program_studi are searchable publicly)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nim', 'like', '%' . $search . '%')
                  ->orWhere('program_studi', 'like', '%' . $search . '%');
            });
        }

        // Apply filters
        if ($program_studi) {
            $query->where('program_studi', $program_studi);
        }

        if ($tahun_lulus) {
            $query->where('tahun_lulus', $tahun_lulus);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Paginate results (only public-safe columns)
        $profiles = $query->select([
            'id', 'nim', 'jenis_kelamin',
            'tahun_masuk', 'tahun_lulus', 'program_studi',
            'created_at'
        ])->paginate($perPage)->withQueryString();

        // Get unique values for filters
        $programStudis = Profile::lulus()->distinct()->pluck('program_studi')->sort()->values();
        $tahunLulusOptions = Profile::lulus()->distinct()->pluck('tahun_lulus')->sortDesc()->values();

        return view('alumni.index', compact(
            'profiles',
            'search',
            'program_studi',
            'tahun_lulus',
            'programStudis',
            'tahunLulusOptions',
            'perPage'
        ));
    }

    /**
     * Display the specified profile (PUBLIC - limited data).
     */
    public function show(Profile $alumni)
    {
        // Only show profiles with status lulus
        if (!$alumni->isLulus()) {
            abort(404);
        }

        // Return a view that only shows public data
        return view('alumni.show', [
            'profile' => $alumni,
            'isPublic' => true
        ]);
    }

    /**
     * Get statistics for dashboard (PUBLIC - aggregated data only).
     */
    public function statistics()
    {
        $totalAlumni = Profile::lulus()->count();
        $totalLaki = Profile::lulus()->where('jenis_kelamin', 'L')->count();
        $totalPerempuan = Profile::lulus()->where('jenis_kelamin', 'P')->count();

        $latestAlumni = Profile::lulus()
            ->with('user:id,name')
            ->select(['id', 'nim', 'program_studi', 'tahun_lulus'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $byProgramStudi = Profile::lulus()
            ->selectRaw('program_studi, COUNT(*) as total')
            ->groupBy('program_studi')
            ->get();

        $byTahunLulus = Profile::lulus()
            ->selectRaw('tahun_lulus, COUNT(*) as total')
            ->groupBy('tahun_lulus')
            ->orderBy('tahun_lulus', 'desc')
            ->limit(10)
            ->get();

        // Placeholder values for work statistics (not yet in profiles)
        $totalBekerja = 0;
        $totalBelumBekerja = 0;

        return view('alumni.statistics', compact(
            'totalAlumni',
            'totalLaki',
            'totalPerempuan',
            'totalBekerja',
            'totalBelumBekerja',
            'latestAlumni',
            'byProgramStudi',
            'byTahunLulus'
        ));
    }

    /**
     * Export alumni data to CSV (PUBLIC - limited data).
     */
    public function export()
    {
        // Only export non-sensitive data for profiles with status lulus
        $profiles = Profile::lulus()
            ->with('user:id,name')
            ->select([
                'nim', 'jenis_kelamin',
                'tahun_masuk', 'tahun_lulus', 'program_studi'
            ])
            ->orderBy('tahun_lulus', 'desc')
            ->get();

        $csvContent = "NIM,Nama Lengkap,Jenis Kelamin,Tahun Masuk,Tahun Lulus,Program Studi\n";

        foreach ($profiles as $p) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $p->nim,
                str_replace(',', ';', $p->nama_lengkap),
                $p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                $p->tahun_masuk,
                $p->tahun_lulus ?? '',
                str_replace(',', ';', $p->program_studi)
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="data-alumni-' . date('Y-m-d') . '.csv"');
    }
}
