<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\TracerStudy;
use App\Models\TracerQuestion;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the mahasiswa dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get profile for mahasiswa
        $profile = $user->profile;

        // Determine profile status
        $profileStatus = $profile ? 'complete' : 'incomplete';

        // Get tracer study data (check both profile_id and alumni_id for backward compat)
        $tracerStudy = null;
        if ($profile) {
            $tracerStudy = $profile->tracerStudy;
        }
        if (!$tracerStudy && $user->alumni) {
            $tracerStudy = TracerStudy::where('alumni_id', $user->alumni->id)->first();
        }

        // Tracer study status
        $tracerStudyStatus = 'not_started';
        if ($tracerStudy) {
            $tracerStudyStatus = $tracerStudy->isSubmitted() ? 'submitted' : 'draft';
        }

        // Question progress
        $totalQuestions = TracerQuestion::where('is_active', true)->count();
        $completedQuestions = $tracerStudy
            ? $tracerStudy->answers()->count()
            : 0;

        return view('mahasiswa.dashboard', compact(
            'user',
            'profile',
            'tracerStudy',
            'tracerStudyStatus',
            'completedQuestions',
            'totalQuestions',
            'profileStatus',
        ));
    }
}
