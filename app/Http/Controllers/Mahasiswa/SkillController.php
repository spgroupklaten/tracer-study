<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfileSkillRequest;
use App\Http\Requests\UpdateProfileSkillRequest;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SkillController extends Controller
{
    /**
     * Display a listing of the profile's skills.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return view('mahasiswa.profile.index', [
                'user' => $user,
                'profile' => null,
                'skills' => collect(),
                'skillLevels' => [],
            ]);
        }

        return view('mahasiswa.profile.index', [
            'user' => $user,
            'profile' => $profile,
            'skills' => $profile->skills()->orderByPivot('created_at', 'desc')->get(),
            'skillLevels' => \App\Enums\SkillLevel::options(),
        ]);
    }

    /**
     * Store a newly created skill in storage.
     */
    public function store(StoreProfileSkillRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return redirect()
                ->route('mahasiswa.profile.index')
                ->with('error', 'Profil tidak ditemukan.');
        }

        $validated = $request->validated();

        // Add skill to profile (security: using relationship)
        $profile->skills()->attach($validated['skill_id'], [
            'level' => $validated['level'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('mahasiswa.profile.index')
            ->with('success', 'Skill berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified skill.
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

        // Find skill that belongs to this profile only (security)
        $skill = $profile->skills()->findOrFail($id);
        $pivot = $skill->pivot;

        return view('mahasiswa.profile.skill-edit', [
            'user' => $user,
            'profile' => $profile,
            'skill' => $skill,
            'pivot' => $pivot,
            'skillLevels' => \App\Enums\SkillLevel::options(),
        ]);
    }

    /**
     * Update the specified skill in storage.
     */
    public function update(UpdateProfileSkillRequest $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return redirect()
                ->route('mahasiswa.profile.index')
                ->with('error', 'Profil tidak ditemukan.');
        }

        // Find skill that belongs to this profile only (security)
        $skill = $profile->skills()->findOrFail($id);

        $validated = $request->validated();

        // Update pivot data
        $profile->skills()->updateExistingPivot($skill->id, [
            'level' => $validated['level'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('mahasiswa.profile.index')
            ->with('success', 'Skill berhasil diperbarui.');
    }

    /**
     * Remove the specified skill from storage.
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

        // Find skill that belongs to this profile only (security)
        $skill = $profile->skills()->findOrFail($id);

        // Detach skill from profile
        $profile->skills()->detach($skill->id);

        return redirect()
            ->route('mahasiswa.profile.index')
            ->with('success', 'Skill berhasil dihapus.');
    }
}
