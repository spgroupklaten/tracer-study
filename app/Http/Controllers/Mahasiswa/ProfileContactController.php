<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileContactRequest;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileContactController extends Controller
{
    /**
     * Show the contact edit form.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $profile = $user->profile;

        // If profile doesn't exist, redirect to profile index
        if (!$profile) {
            return redirect()
                ->route('mahasiswa.profile.index')
                ->with('error', 'Profil tidak ditemukan.');
        }

        // Get or create contact
        $contact = $profile->contact ?? new \App\Models\ProfileContact(['profile_id' => $profile->id]);

        return view('mahasiswa.profile.contact-edit', [
            'user' => $user,
            'profile' => $profile,
            'contact' => $contact,
        ]);
    }

    /**
     * Update the contact.
     */
    public function update(UpdateProfileContactRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return redirect()
                ->route('mahasiswa.profile.index')
                ->with('error', 'Profil tidak ditemukan.');
        }

        $validated = $request->validated();

        // Update or create contact - profile_id comes from authenticated profile, not request
        $profile->contact()->updateOrCreate(
            ['profile_id' => $profile->id],
            $validated
        );

        return redirect()
            ->route('mahasiswa.profile.index')
            ->with('success', 'Kontak dan media sosial berhasil diperbarui.');
    }
}
