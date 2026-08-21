<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    /**
     * Display a listing of the resource (PUBLIC - limited data).
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $program_studi = $request->input('program_studi', '');
        $tahun_lulus = $request->input('tahun_lulus', '');
        $status_pekerjaan = $request->input('status_pekerjaan', '');

        $query = Alumni::query();

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nim', 'like', '%' . $search . '%')
                  ->orWhere('nama_lengkap', 'like', '%' . $search . '%')
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

        if ($status_pekerjaan) {
            $query->where('status_pekerjaan', $status_pekerjaan);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $alumni = $query->select([
            'id', 'nim', 'nama_lengkap', 'jenis_kelamin', 
            'tahun_masuk', 'tahun_lulus', 'program_studi', 
            'status_pekerjaan', 'pekerjaan', 'created_at'
        ])->paginate($perPage)->withQueryString();

        // Get unique values for filters
        $programStudis = Alumni::distinct()->pluck('program_studi')->sort()->values();
        $tahunLulusOptions = Alumni::distinct()->pluck('tahun_lulus')->sortDesc()->values();
        $statusPekerjaanOptions = Alumni::distinct()->pluck('status_pekerjaan')->sort()->values();

        return view('alumni.index', compact(
            'alumni',
            'search',
            'program_studi',
            'tahun_lulus',
            'status_pekerjaan',
            'programStudis',
            'tahunLulusOptions',
            'statusPekerjaanOptions',
            'perPage'
        ));
    }

    /**
     * Display the specified resource (PUBLIC - limited data).
     */
    public function show(Alumni $alumni)
    {
        // Show only public-safe data
        $publicData = [
            'id' => $alumni->id,
            'nim' => $alumni->nim,
            'nama_lengkap' => $alumni->nama_lengkap,
            'jenis_kelamin' => $alumni->jenis_kelamin,
            'tahun_masuk' => $alumni->tahun_masuk,
            'tahun_lulus' => $alumni->tahun_lulus,
            'program_studi' => $alumni->program_studi,
            'status_pekerjaan' => $alumni->status_pekerjaan,
            'pekerjaan' => $alumni->pekerjaan,
            'created_at' => $alumni->created_at,
            'updated_at' => $alumni->updated_at,
        ];

        // Return a view that only shows public data
        // For show page, we'll use a modified view
        return view('alumni.show', [
            'alumni' => (object) $publicData,
            'isPublic' => true
        ]);
    }

    /**
     * Get statistics for dashboard (PUBLIC - aggregated data only).
     */
    public function statistics()
    {
        $totalAlumni = Alumni::count();
        $totalLaki = Alumni::where('jenis_kelamin', 'L')->count();
        $totalPerempuan = Alumni::where('jenis_kelamin', 'P')->count();
        $totalBekerja = Alumni::where('status_pekerjaan', 'Bekerja')->count();
        $totalBelumBekerja = Alumni::where('status_pekerjaan', 'Belum Bekerja')->count();
        
        $latestAlumni = Alumni::select(['id', 'nim', 'nama_lengkap', 'program_studi', 'status_pekerjaan', 'tahun_lulus'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $byProgramStudi = Alumni::selectRaw('program_studi, COUNT(*) as total')
            ->groupBy('program_studi')
            ->get();

        $byTahunLulus = Alumni::selectRaw('tahun_lulus, COUNT(*) as total')
            ->groupBy('tahun_lulus')
            ->orderBy('tahun_lulus', 'desc')
            ->limit(10)
            ->get();

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
     * Note: This should be protected in routes, but providing safe data here too
     */
    public function export()
    {
        // Note: Export should be admin-only, but this serves as fallback
        // Only export non-sensitive data
        $alumni = Alumni::select([
            'nim', 'nama_lengkap', 'jenis_kelamin',
            'tahun_masuk', 'tahun_lulus', 'program_studi',
            'status_pekerjaan', 'pekerjaan'
        ])->orderBy('tahun_lulus', 'desc')->get();

        $csvContent = "NIM,Nama Lengkap,Jenis Kelamin,Tahun Masuk,Tahun Lulus,Program Studi,Status Pekerjaan,Pekerjaan\n";
        
        foreach ($alumni as $a) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s\n",
                $a->nim,
                str_replace(',', ';', $a->nama_lengkap),
                $a->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                $a->tahun_masuk,
                $a->tahun_lulus,
                str_replace(',', ';', $a->program_studi),
                $a->status_pekerjaan,
                str_replace(',', ';', $a->pekerjaan ?? '')
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="data-alumni-' . date('Y-m-d') . '.csv"');
    }
}
