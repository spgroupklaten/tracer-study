<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAchievementRequest;
use App\Http\Requests\UpdateAchievementRequest;
use App\Models\Achievement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AchievementController extends Controller
{
    /**
     * Store a newly created achievement in storage.
     */
    public function store(StoreAchievementRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return redirect()
                ->route('mahasiswa.profile.index')
                ->with('error', 'Profil tidak ditemukan.');
        }

        $validated = $request->validated();

        // Handle certificate upload
        $certificatePath = null;
        $originalName = null;
        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            $originalName = $file->getClientOriginalName();
            $filename = 'cert_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $certificatePath = $file->storeAs(
                'certificates/' . $profile->id,
                $filename,
                'public'
            );
        }

        // Extract tahun from tanggal if not provided
        if (empty($validated['tahun']) && !empty($validated['tanggal'])) {
            $validated['tahun'] = date('Y', strtotime($validated['tanggal']));
        }

        // Create achievement
        Achievement::create([
            'profile_id' => $profile->id,
            'judul' => $validated['judul'],
            'jenis' => $validated['jenis'],
            'tingkat' => $validated['tingkat'],
            'penyelenggara' => $validated['penyelenggara'] ?? null,
            'tanggal' => $validated['tanggal'] ?? null,
            'tahun' => $validated['tahun'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'certificate_path' => $certificatePath,
            'certificate_original_name' => $originalName,
        ]);

        return redirect()
            ->route('mahasiswa.profile.index')
            ->with('success', 'Prestasi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified achievement.
     */
    public function edit(Request $request, int $id): View|RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return redirect()
                ->route('mahasiswa.profile.index')
                ->with('error', 'Profil tidak ditemukan.');
        }

        // Find achievement that belongs to this profile only (security)
        $achievement = $profile->achievements()->findOrFail($id);

        return view('mahasiswa.profile.achievement-edit', [
            'user' => $user,
            'profile' => $profile,
            'achievement' => $achievement,
            'achievementTypes' => \App\Enums\AchievementType::options(),
            'achievementLevels' => \App\Enums\AchievementLevel::options(),
        ]);
    }

    /**
     * Update the specified achievement in storage.
     */
    public function update(UpdateAchievementRequest $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return redirect()
                ->route('mahasiswa.profile.index')
                ->with('error', 'Profil tidak ditemukan.');
        }

        // Find achievement that belongs to this profile only (security)
        $achievement = $profile->achievements()->findOrFail($id);

        $validated = $request->validated();

        // Handle certificate upload
        $certificatePath = $achievement->certificate_path;
        $originalName = $achievement->certificate_original_name;

        // If user wants to remove certificate
        if ($request->has('remove_certificate') && $request->remove_certificate == '1') {
            $achievement->deleteCertificate();
            $certificatePath = null;
            $originalName = null;
        }

        // If user uploads new certificate
        if ($request->hasFile('certificate')) {
            // Delete old certificate first
            $achievement->deleteCertificate();

            $file = $request->file('certificate');
            $originalName = $file->getClientOriginalName();
            $filename = 'cert_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $certificatePath = $file->storeAs(
                'certificates/' . $profile->id,
                $filename,
                'public'
            );
        }

        // Extract tahun from tanggal if not provided
        if (empty($validated['tahun']) && !empty($validated['tanggal'])) {
            $validated['tahun'] = date('Y', strtotime($validated['tanggal']));
        }

        // Update achievement
        $achievement->update([
            'judul' => $validated['judul'],
            'jenis' => $validated['jenis'],
            'tingkat' => $validated['tingkat'],
            'penyelenggara' => $validated['penyelenggara'] ?? null,
            'tanggal' => $validated['tanggal'] ?? null,
            'tahun' => $validated['tahun'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'certificate_path' => $certificatePath,
            'certificate_original_name' => $originalName,
        ]);

        return redirect()
            ->route('mahasiswa.profile.index')
            ->with('success', 'Prestasi berhasil diperbarui.');
    }

    /**
     * Remove the specified achievement from storage.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return redirect()
                ->route('mahasiswa.profile.index')
                ->with('error', 'Profil tidak ditemukan.');
        }

        // Find achievement that belongs to this profile only (security)
        $achievement = $profile->achievements()->findOrFail($id);

        // Delete certificate file
        $achievement->deleteCertificate();

        // Delete achievement
        $achievement->delete();

        return redirect()
            ->route('mahasiswa.profile.index')
            ->with('success', 'Prestasi berhasil dihapus.');
    }

    /**
     * View certificate (authorized access only).
     */
    public function viewCertificate(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return redirect()
                ->route('mahasiswa.profile.index')
                ->with('error', 'Profil tidak ditemukan.');
        }

        // Find achievement that belongs to this profile only (security)
        $achievement = $profile->achievements()->findOrFail($id);

        if (!$achievement->hasCertificate()) {
            return redirect()
                ->route('mahasiswa.profile.index')
                ->with('error', 'Sertifikat tidak ditemukan.');
        }

        return redirect(Storage::url($achievement->certificate_path));
    }
}
