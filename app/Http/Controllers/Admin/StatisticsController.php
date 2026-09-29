<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\TracerStudy;
use App\Services\TracerStudyQuestionStatisticsService;
use App\Enums\TracerStudyStatus;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    /**
     * Display Tracer Study statistics for admin.
     */
    public function tracerStudy()
    {
        // Basic counts (Alumni = Profile with status lulus)
        $totalAlumni = Profile::lulus()->count();
        $totalTracerStudy = TracerStudy::whereHas('profile', fn ($q) => $q->lulus())->count();
        $totalDraft = TracerStudy::whereHas('profile', fn ($q) => $q->lulus())
            ->where('status', TracerStudyStatus::Draft)->count();
        $totalSubmitted = TracerStudy::whereHas('profile', fn ($q) => $q->lulus())
            ->where('status', TracerStudyStatus::Submitted)->count();
        $alumniBelumMengisi = $totalAlumni - $totalTracerStudy;

        // Participation percentage
        $persentasePartisipasi = $totalAlumni > 0
            ? round(($totalTracerStudy / $totalAlumni) * 100, 1)
            : 0;

        // Submitted percentage from tracer studies
        $persentaseSubmitted = $totalTracerStudy > 0
            ? round(($totalSubmitted / $totalTracerStudy) * 100, 1)
            : 0;

        // Statistik berdasarkan tahun lulus
        $statistikTahunLulus = Profile::lulus()
            ->selectRaw('tahun_lulus, COUNT(*) as total_alumni')
            ->groupBy('tahun_lulus')
            ->orderByDesc('tahun_lulus')
            ->get();

        // Statistik tahun lulus dengan tracer study
        $statistikTahunLulusDenganTracer = Profile::lulus()
            ->selectRaw('profiles.tahun_lulus, COUNT(tracer_studies.id) as total_tracer')
            ->leftJoin('tracer_studies', 'profiles.id', '=', 'tracer_studies.profile_id')
            ->groupBy('profiles.tahun_lulus')
            ->orderByDesc('profiles.tahun_lulus')
            ->get()
            ->map(function ($item) {
                return [
                    'tahun_lulus' => $item->tahun_lulus,
                    'total_tracer' => $item->total_tracer ?? 0,
                ];
            });

        // Statistik berdasarkan program studi
        $statistikProgramStudi = Profile::lulus()
            ->selectRaw('program_studi, COUNT(*) as total_alumni')
            ->groupBy('program_studi')
            ->orderByDesc('total_alumni')
            ->get();

        // Statistik program studi dengan tracer study
        $statistikProgramStudiDenganTracer = Profile::lulus()
            ->selectRaw('profiles.program_studi, COUNT(tracer_studies.id) as total_tracer')
            ->leftJoin('tracer_studies', 'profiles.id', '=', 'tracer_studies.profile_id')
            ->groupBy('profiles.program_studi')
            ->orderByDesc('profiles.program_studi')
            ->get()
            ->map(function ($item) {
                return [
                    'program_studi' => $item->program_studi,
                    'total_tracer' => $item->total_tracer ?? 0,
                ];
            });

        // Alumni dengan tracer study submitted
        $alumniSubmitted = Profile::lulus()->whereHas('tracerStudy', function ($query) {
            $query->where('status', TracerStudyStatus::Submitted);
        })->count();

        // Alumni dengan tracer study draft
        $alumniDraft = Profile::lulus()->whereHas('tracerStudy', function ($query) {
            $query->where('status', TracerStudyStatus::Draft);
        })->count();

        // Placeholder for distribusiPekerjaan (moved from alumni table)
        $distribusiPekerjaan = collect();

        return view('admin.statistics.tracer-study', compact(
            'totalAlumni',
            'totalTracerStudy',
            'totalDraft',
            'totalSubmitted',
            'alumniBelumMengisi',
            'persentasePartisipasi',
            'persentaseSubmitted',
            'distribusiPekerjaan',
            'statistikTahunLulus',
            'statistikTahunLulusDenganTracer',
            'statistikProgramStudi',
            'statistikProgramStudiDenganTracer',
            'alumniSubmitted',
            'alumniDraft',
        ));
    }

    /**
     * Display Question Statistics for admin.
     */
    public function questionStatistics(TracerStudyQuestionStatisticsService $statisticsService)
    {
        $summary = $statisticsService->getSummary();
        $questionStatistics = $statisticsService->getQuestionStatistics();

        return view('admin.statistics.question', compact(
            'summary',
            'questionStatistics',
        ));
    }
}
