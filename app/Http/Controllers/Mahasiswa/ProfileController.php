<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMahasiswaProfileRequest;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the mahasiswa's profile.
     */
    public function index(): View
    {
        $user = Auth::user();
        $profile = $user->profile;

        // If profile doesn't exist, show message or create empty profile
        if (!$profile) {
            return view('mahasiswa.profile.index', [
                'user' => $user,
                'profile' => null,
                'isNew' => true,
                'skills' => collect(),
                'skillLevels' => \App\Enums\SkillLevel::options(),
                'achievements' => collect(),
            ]);
        }

        return view('mahasiswa.profile.index', [
            'user' => $user,
            'profile' => $profile,
            'isNew' => false,
            'skills' => $profile->skills()->orderByPivot('created_at', 'desc')->get(),
            'skillLevels' => \App\Enums\SkillLevel::options(),
            'achievements' => $profile->achievements()->orderByDesc('tanggal')->get(),
        ]);
    }

    /**
     * Show the form for editing the mahasiswa's profile.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            // Create empty profile if doesn't exist
            $profile = new Profile([
                'user_id' => $user->id,
                'academic_status' => \App\Enums\AcademicStatus::Aktif,
            ]);
        }

        return view('mahasiswa.profile.edit', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Update the mahasiswa's profile.
     */
    public function update(UpdateMahasiswaProfileRequest $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        // Create profile if doesn't exist
        if (!$profile) {
            $profile = new Profile([
                'user_id' => $user->id,
                'academic_status' => \App\Enums\AcademicStatus::Aktif,
            ]);
        }

        $validated = $request->validated();

        // Remove protected fields (should not exist but safety check)
        $protectedFields = [
            'user_id', 'role', 'academic_status', 'nim', 'fakultas',
            'program_studi', 'angkatan', 'tahun_masuk', 'tahun_lulus',
            'tanggal_lulus', 'ipk'
        ];
        foreach ($protectedFields as $field) {
            unset($validated[$field]);
        }

        // Handle photo upload
        $oldPhotoPath = $profile->foto;
        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->handlePhotoUpload($request->file('foto'));
        } else {
            unset($validated['foto']);
        }

        DB::transaction(function () use ($user, $profile, $validated) {
            // Update user name if provided
            if (isset($validated['name'])) {
                $user->update([
                    'name' => $validated['name'],
                ]);
                unset($validated['name']);
            }

            // Update profile
            if (!empty($validated)) {
                $profile->update($validated);
            }
        });

        // Delete old photo after successful update
        if (isset($validated['foto']) && $oldPhotoPath) {
            $this->deletePhoto($oldPhotoPath);
        }

        return redirect()
            ->route('mahasiswa.profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Handle photo upload.
     */
    private function handlePhotoUpload($file): string
    {
        // Generate unique filename
        $filename = 'profile_' . auth()->id() . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Store in public storage
        $path = $file->storeAs('photos', $filename, 'public');

        return $path;
    }

    /**
     * Delete photo from storage.
     */
    private function deletePhoto(string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Get options for dropdowns.
     */
    public function getOptions(): array
    {
        return [
            'jenis_kelamin' => [
                'L' => 'Laki-laki',
                'P' => 'Perempuan',
            ],
            'status_pernikahan' => [
                'belum_menikah' => 'Belum Menikah',
                'menikah' => 'Menikah',
                'cerai_hidup' => 'Cerai Hidup',
                'cerai_mati' => 'Cerai Mati',
            ],
            'agama' => [
                'islam' => 'Islam',
                'kristen' => 'Kristen',
                'katolik' => 'Katolik',
                'hindu' => 'Hindu',
                'buddha' => 'Buddha',
                'konghucu' => 'Konghucu',
                'lainnya' => 'Lainnya',
            ],
        ];
    }
}
