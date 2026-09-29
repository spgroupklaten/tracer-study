<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileAdminRequest;
use App\Models\Profile;
use App\Models\User;
use App\Enums\Role;
use App\Enums\AcademicStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AlumniController extends Controller
{
    /**
     * Display a listing of alumni (profiles with status = lulus).
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $program_studi = $request->input('program_studi', '');
        $tahun_lulus = $request->input('tahun_lulus', '');
        $fakultass = $request->input('fakultass', '');

        $query = Profile::lulus()
            ->with('user:id,name,email');

        // Apply search filter (name, nim, email)
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('nim', 'like', "%{$search}%");
        }

        // Apply filters
        if ($program_studi) {
            $query->where('program_studi', $program_studi);
        }

        if ($tahun_lulus) {
            $query->where('tahun_lulus', $tahun_lulus);
        }

        if ($fakultass) {
            $query->where('fakultass', $fakultass);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $profiles = $query->paginate($perPage)->withQueryString();

        // Get unique values for filters
        $programStudis = Profile::lulus()->distinct()->pluck('program_studi')->sort()->values();
        $tahunLulusOptions = Profile::lulus()->distinct()->whereNotNull('tahun_lulus')->orderBy('tahun_lulus', 'desc')->pluck('tahun_lulus');
        $fakultassList = Profile::lulus()->distinct()->pluck('fakultass')->sort()->values();

        return view('admin.alumni.index', [
            'profiles' => $profiles,
            'search' => $search,
            'program_studi' => $program_studi,
            'tahun_lulus' => $tahun_lulus,
            'fakultass' => $fakultass,
            'programStudis' => $programStudis,
            'tahunLulusOptions' => $tahunLulusOptions,
            'fakultassList' => $fakultassList,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Show the form for creating a new alumni (user with profile).
     */
    public function create()
    {
        return view('admin.alumni.create');
    }

    /**
     * Store a newly created alumni (user + profile with status = lulus).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'nim' => 'required|string|max:20|unique:profiles,nim',
            'fakultass' => 'nullable|string|max:255',
            'program_studi' => 'nullable|string|max:255',
            'tahun_masuk' => 'nullable|integer|min:1990|max:' . (date('Y') + 5),
            'tahun_lulus' => 'nullable|integer|min:1990|max:' . (date('Y') + 5),
            'ipk' => 'nullable|numeric|min:0|max:4',
            'tanggal_lulus' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated) {
            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => Role::Mahasiswa,
            ]);

            // Derive angkatan from tahun_masuk or default to current year
            $tahunMasuk = $validated['tahun_masuk'] ?? (int) date('Y');

            // Create profile with status = lulus
            Profile::create([
                'user_id' => $user->id,
                'nim' => $validated['nim'],
                'fakultass' => $validated['fakultass'] ?? null,
                'program_studi' => $validated['program_studi'] ?? null,
                'tahun_masuk' => $validated['tahun_masuk'] ?? null,
                'tahun_lulus' => $validated['tahun_lulus'] ?? null,
                'ipk' => $validated['ipk'] ?? null,
                'tanggal_lulus' => $validated['tanggal_lulus'] ?? null,
                'academic_status' => AcademicStatus::Lulus,
                'tempat_lahir' => '-',
                'tanggal_lahir' => now()->subYears(22)->format('Y-m-d'),
                'jenis_kelamin' => 'L',
                'angkatan' => $tahunMasuk,
            ]);
        });

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil ditambahkan.');
    }

    /**
     * Display the specified profile.
     */
    public function show(Profile $alumni)
    {
        // Load relationships
        $alumni->load([
            'user:id,name,email',
            'contact',
            'educationHistories',
            'skills',
            'achievements',
            'tracerStudy.answers',
        ]);

        // Check if profile is alumni (lulus)
        if (!$alumni->isLulus()) {
            abort(404, 'Profil bukan alumni.');
        }

        return view('admin.alumni.show', compact('alumni'));
    }

    /**
     * Show the form for editing the specified profile.
     */
    public function edit(Profile $alumni)
    {
        if (!$alumni->isLulus()) {
            abort(404, 'Profil bukan alumni.');
        }

        $alumni->load('user:id,name,email');

        return view('admin.alumni.edit', compact('alumni'));
    }

    /**
     * Update the specified profile (academic data only).
     */
    public function update(UpdateProfileAdminRequest $request, Profile $alumni)
    {
        if (!$alumni->isLulus()) {
            abort(404, 'Profil bukan alumni.');
        }

        $validated = $request->validated();

        DB::transaction(function () use ($alumni, $validated) {
            // Update user data if provided
            if (!empty($validated['name']) || !empty($validated['email'])) {
                $userData = [];
                if (!empty($validated['name'])) {
                    $userData['name'] = $validated['name'];
                }
                if (!empty($validated['email'])) {
                    $userData['email'] = $validated['email'];
                }
                $alumni->user->update($userData);
            }

            // Update profile academic data
            $profileData = array_intersect_key($validated, array_flip([
                'nim',
                'fakultass',
                'program_studi',
                'tahun_masuk',
                'tahun_lulus',
                'tanggal_lulus',
                'ipk',
                'academic_status',
            ]));

            if (!empty($profileData)) {
                $alumni->update($profileData);
            }
        });

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil diperbarui.');
    }

    /**
     * Remove the specified profile (and associated user).
     */
    public function destroy(Profile $alumni)
    {
        if (!$alumni->isLulus()) {
            abort(404, 'Profil bukan alumni.');
        }

        DB::transaction(function () use ($alumni) {
            // Delete associated user
            if ($alumni->user) {
                $alumni->user->delete();
            }
            // Profile will be cascade deleted due to foreign key
            $alumni->delete();
        });

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil dihapus.');
    }

    /**
     * Export alumni data to CSV (admin only - includes more data than public).
     */
    public function export()
    {
        $profiles = Profile::lulus()
            ->with('user:id,name,email')
            ->orderBy('tahun_lulus', 'desc')
            ->get();

        $csvContent = "NIM,Nama,Email,Fakultas,Program Studi,Tahun Masuk,Tahun Lulus,Tanggal Lulus,IPK\n";

        foreach ($profiles as $p) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $p->nim ?? '',
                str_replace(',', ';', $p->nama_lengkap),
                $p->email ?? '',
                str_replace(',', ';', $p->fakultass ?? ''),
                str_replace(',', ';', $p->program_studi ?? ''),
                $p->tahun_masuk ?? '',
                $p->tahun_lulus ?? '',
                $p->tanggal_lulus ? $p->tanggal_lulus->format('Y-m-d') : '',
                $p->ipk ?? ''
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="data-alumni-' . date('Y-m-d') . '.csv"');
    }
}
