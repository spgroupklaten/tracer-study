<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Http\Requests\StoreAlumniRequest;
use App\Http\Requests\UpdateAlumniRequest;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    /**
     * Display a listing of the resource.
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
            $query->search($search);
        }

        // Apply filters
        if ($program_studi) {
            $query->byProgramStudi($program_studi);
        }

        if ($tahun_lulus) {
            $query->byTahunLulus($tahun_lulus);
        }

        if ($status_pekerjaan) {
            $query->byStatusPekerjaan($status_pekerjaan);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $alumni = $query->paginate($perPage)->withQueryString();

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('alumni.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlumniRequest $request)
    {
        $validated = $request->validated();
        
        Alumni::create($validated);

        return redirect()
            ->route('alumni.index')
            ->with('success', 'Data alumni berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Alumni $alumni)
    {
        return view('alumni.show', compact('alumni'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Alumni $alumni)
    {
        return view('alumni.edit', compact('alumni'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlumniRequest $request, Alumni $alumni)
    {
        $validated = $request->validated();
        
        $alumni->update($validated);

        return redirect()
            ->route('alumni.index')
            ->with('success', 'Data alumni berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alumni $alumni)
    {
        $alumni->delete();

        return redirect()
            ->route('alumni.index')
            ->with('success', 'Data alumni berhasil dihapus.');
    }

    /**
     * Export alumni data to CSV.
     */
    public function export()
    {
        $alumni = Alumni::orderBy('tahun_lulus', 'desc')->get();

        $csvContent = "NIM,Nama Lengkap,Jenis Kelamin,Tempat Lahir,Tanggal Lahir,Email,No HP,Tahun Masuk,Tahun Lulus,Program Studi,Pekerjaan,Status Pekerjaan,Alamat\n";
        
        foreach ($alumni as $a) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $a->nim,
                str_replace(',', ';', $a->nama_lengkap),
                $a->jenis_kelamin_display,
                str_replace(',', ';', $a->tempat_lahir),
                $a->tanggal_lahir->format('Y-m-d'),
                $a->email,
                $a->nomor_hp ?? '',
                $a->tahun_masuk,
                $a->tahun_lulus,
                str_replace(',', ';', $a->program_studi),
                str_replace(',', ';', $a->pekerjaan ?? ''),
                $a->status_pekerjaan,
                str_replace([",", "\n", "\r"], [';', ' ', ''], $a->alamat ?? '')
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="data-alumni-' . date('Y-m-d') . '.csv"');
    }

    /**
     * Get statistics for dashboard.
     */
    public function statistics()
    {
        $totalAlumni = Alumni::count();
        $totalLaki = Alumni::where('jenis_kelamin', 'L')->count();
        $totalPerempuan = Alumni::where('jenis_kelamin', 'P')->count();
        $totalBekerja = Alumni::where('status_pekerjaan', 'Bekerja')->count();
        $totalBelumBekerja = Alumni::where('status_pekerjaan', 'Belum Bekerja')->count();
        
        $latestAlumni = Alumni::orderBy('created_at', 'desc')->limit(5)->get();
        
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
}
