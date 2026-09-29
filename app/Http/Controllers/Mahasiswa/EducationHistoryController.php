<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEducationHistoryRequest;
use App\Http\Requests\UpdateEducationHistoryRequest;
use App\Models\EducationHistory;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EducationHistoryController extends Controller
{
    /**
     * Store a newly created education history.
     */
    public function store(StoreEducationHistoryRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return redirect()
                ->route('mahasiswa.profile.index')
                ->with('error', 'Profil tidak ditemukan.');
        }

        $validated = $request->validated();

        // Add profile_id from authenticated profile (security)
        $profile->educationHistories()->create($validated);

        return redirect()
            ->route('mahasiswa.profile.index')
            ->with('success', 'Riwayat pendidikan berhasil ditambahkan.');
    }

    /**
     * Show the edit form.
     */
    public function edit(int $id): View
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            abort(404, 'Profil tidak ditemukan.');
        }

        // Find education that belongs to this profile only (security)
        $education = $profile->educationHistories()->findOrFail($id);

        return view('mahasiswa.profile.education-edit', [
            'user' => $user,
            'profile' => $profile,
            'education' => $education,
        ]);
    }

    /**
     * Update the education history.
     */
    public function update(UpdateEducationHistoryRequest $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return redirect()
                ->route('mahasiswa.profile.index')
                ->with('error', 'Profil tidak ditemukan.');
        }

        // Find education that belongs to this profile only (security)
        $education = $profile->educationHistories()->findOrFail($id);

        $validated = $request->validated();

        $education->update($validated);

        return redirect()
            ->route('mahasiswa.profile.index')
            ->with('success', 'Riwayat pendidikan berhasil diperbarui.');
    }

    /**
     * Remove the education history.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return redirect()
                ->route('mahasiswa.profile.index')
                ->with('error', 'Profil tidak ditemukan.');
        }

        // Find education that belongs to this profile only (security)
        $education = $profile->educationHistories()->findOrFail($id);

        $education->delete();

        return redirect()
            ->route('mahasiswa.profile.index')
            ->with('success', 'Riwayat pendidikan berhasil dihapus.');
    }
}
