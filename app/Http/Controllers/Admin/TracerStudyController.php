<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\TracerStudy;
use App\Enums\TracerStudyStatus;
use Illuminate\Http\Request;

class TracerStudyController extends Controller
{
    /**
     * Display a listing of tracer studies.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $program_studi = $request->input('program_studi', '');
        $tahun_lulus = $request->input('tahun_lulus', '');
        $status = $request->input('status', '');

        // Build query with tracer study status (alumni = profile with status lulus)
        $query = Profile::lulus()
            ->with(['tracerStudy', 'user'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('nim', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($q3) use ($search) {
                            $q3->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($program_studi, function ($q) use ($program_studi) {
                $q->where('program_studi', $program_studi);
            })
            ->when($tahun_lulus, function ($q) use ($tahun_lulus) {
                $q->where('tahun_lulus', $tahun_lulus);
            })
            ->when($status !== '', function ($q) use ($status) {
                if ($status === 'not_started') {
                    $q->whereDoesntHave('tracerStudy');
                } elseif ($status === 'draft') {
                    $q->whereHas('tracerStudy', function ($q2) {
                        $q2->where('status', TracerStudyStatus::Draft);
                    });
                } elseif ($status === 'submitted') {
                    $q->whereHas('tracerStudy', function ($q2) {
                        $q2->where('status', TracerStudyStatus::Submitted);
                    });
                }
            })
            ->orderBy('created_at', 'desc');

        // Get paginated results
        $profiles = $query->paginate($perPage)->withQueryString();

        // Get filter options
        $programStudis = Profile::lulus()->distinct()->pluck('program_studi')->sort()->values();
        $tahunLulusOptions = Profile::lulus()->distinct()->pluck('tahun_lulus')->sortDesc()->values();

        return view('admin.tracer-study.index', [
            'profiles' => $profiles,
            'search' => $search,
            'program_studi' => $program_studi,
            'tahun_lulus' => $tahun_lulus,
            'status' => $status,
            'programStudis' => $programStudis,
            'tahunLulusOptions' => $tahunLulusOptions,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Display the specified tracer study.
     */
    public function show(Profile $profile)
    {
        // Load tracer study with answers and questions
        $profile->load([
            'tracerStudy.answers.question',
            'user',
        ]);

        if (!$profile->tracerStudy) {
            return redirect()->route('admin.tracer-study.index')
                ->with('error', 'Tracer Study tidak ditemukan.');
        }

        // Get all questions with their answers
        $tracerStudy = $profile->tracerStudy;
        $questions = $tracerStudy->answers()
            ->with('question')
            ->get()
            ->pluck('question')
            ->filter()
            ->sortBy('order')
            ->values();

        // Create answer lookup
        $answers = $tracerStudy->answers()
            ->pluck('answer_value', 'question_id')
            ->toArray();

        return view('admin.tracer-study.show', [
            'profile' => $profile,
            'tracerStudy' => $tracerStudy,
            'questions' => $questions,
            'answers' => $answers,
        ]);
    }
}
