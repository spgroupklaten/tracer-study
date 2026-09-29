<?php

namespace App\Services;

use App\Enums\QuestionType;
use App\Enums\TracerStudyStatus;
use App\Models\TracerQuestion;
use App\Models\TracerAnswer;
use App\Models\TracerQuestionOption;
use App\Models\TracerStudy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TracerStudyQuestionStatisticsService
{
    /**
     * Get all questions with their statistics (only submitted responses).
     */
    public function getQuestionStatistics(): Collection
    {
        // Get total submitted responses count
        $totalSubmitted = TracerStudy::where('status', TracerStudyStatus::Submitted)->count();

        // Get all questions ordered by order, then id
        $questions = TracerQuestion::with(['questionOptions' => function ($query) {
            $query->orderBy('order')->orderBy('id');
        }])->orderBy('order')->orderBy('id')->get();

        return $questions->map(function ($question) use ($totalSubmitted) {
            return $this->buildQuestionStatistics($question, $totalSubmitted);
        });
    }

    /**
     * Build statistics for a single question.
     */
    protected function buildQuestionStatistics(TracerQuestion $question, int $totalSubmitted): array
    {
        $typeValue = $question->type->value;

        $baseStats = [
            'id' => $question->id,
            'code' => $question->code,
            'question_text' => $question->question_text,
            'type' => $typeValue,
            'type_label' => $question->type->label(),
            'is_required' => $question->is_required,
            'is_active' => $question->is_active,
            'order' => $question->order,
            'total_submitted_responses' => $totalSubmitted,
        ];

        // Get answer statistics based on type
        $answerStats = $this->getAnswerStatistics($question, $totalSubmitted);

        return array_merge($baseStats, $answerStats);
    }

    /**
     * Get answer statistics based on question type.
     */
    protected function getAnswerStatistics(TracerQuestion $question, int $totalSubmitted): array
    {
        $typeValue = $question->type->value;

        return match ($typeValue) {
            'radio', 'select' => $this->getRadioSelectStatistics($question, $totalSubmitted),
            'checkbox' => $this->getCheckboxStatistics($question, $totalSubmitted),
            'number' => $this->getNumberStatistics($question, $totalSubmitted),
            'date' => $this->getDateStatistics($question, $totalSubmitted),
            'text', 'textarea' => $this->getTextStatistics($question, $totalSubmitted),
            default => $this->getEmptyStatistics($totalSubmitted),
        };
    }

    /**
     * Get statistics for radio and select questions.
     */
    protected function getRadioSelectStatistics(TracerQuestion $question, int $totalSubmitted): array
    {
        // Get submitted tracer study IDs
        $submittedTracerStudyIds = TracerStudy::where('status', TracerStudyStatus::Submitted)
            ->pluck('id');

        // Get options for this question
        $options = $question->questionOptions->map(function ($option) use ($submittedTracerStudyIds, $question, $totalSubmitted) {
            // Count answers that match this option's value for this question
            $count = TracerAnswer::whereIn('tracer_study_id', $submittedTracerStudyIds)
                ->where('question_id', $question->id)
                ->where('answer_value', $option->value)
                ->count();

            $percentage = $totalSubmitted > 0 ? round(($count / $totalSubmitted) * 100, 1) : 0;

            return [
                'id' => $option->id,
                'label' => $option->label,
                'value' => $option->value,
                'is_active' => $option->is_active,
                'count' => $count,
                'percentage' => $percentage,
            ];
        });

        // Get total answered (any non-empty answer)
        $totalAnswered = TracerAnswer::whereIn('tracer_study_id', $submittedTracerStudyIds)
            ->where('question_id', $question->id)
            ->whereNotNull('answer_value')
            ->where('answer_value', '!=', '')
            ->count();

        $totalUnanswered = $totalSubmitted - $totalAnswered;

        return [
            'options' => $options,
            'total_answered' => $totalAnswered,
            'total_unanswered' => max(0, $totalUnanswered),
        ];
    }

    /**
     * Get statistics for checkbox questions.
     */
    protected function getCheckboxStatistics(TracerQuestion $question, int $totalSubmitted): array
    {
        $submittedTracerStudyIds = TracerStudy::where('status', TracerStudyStatus::Submitted)
            ->pluck('id');

        // For checkboxes, answer_value is stored as JSON array
        $options = $question->questionOptions->map(function ($option) use ($submittedTracerStudyIds, $question, $totalSubmitted) {
            // Count how many responses include this option value
            // Checkbox answers are stored as JSON: ["value1", "value2", ...]
            $count = TracerAnswer::whereIn('tracer_study_id', $submittedTracerStudyIds)
                ->where('question_id', $question->id)
                ->whereNotNull('answer_value')
                ->where('answer_value', '!=', '')
                ->get()
                ->filter(function ($answer) use ($option) {
                    $values = json_decode($answer->answer_value, true) ?? [];
                    return in_array($option->value, $values);
                })
                ->count();

            $percentage = $totalSubmitted > 0 ? round(($count / $totalSubmitted) * 100, 1) : 0;

            return [
                'id' => $option->id,
                'label' => $option->label,
                'value' => $option->value,
                'is_active' => $option->is_active,
                'count' => $count,
                'percentage' => $percentage,
            ];
        });

        // Get total answered (at least one option selected)
        $totalAnswered = TracerAnswer::whereIn('tracer_study_id', $submittedTracerStudyIds)
            ->where('question_id', $question->id)
            ->whereNotNull('answer_value')
            ->where('answer_value', '!=', '')
            ->count();

        $totalUnanswered = $totalSubmitted - $totalAnswered;

        return [
            'options' => $options,
            'total_answered' => $totalAnswered,
            'total_unanswered' => max(0, $totalUnanswered),
        ];
    }

    /**
     * Get statistics for number questions.
     */
    protected function getNumberStatistics(TracerQuestion $question, int $totalSubmitted): array
    {
        $submittedTracerStudyIds = TracerStudy::where('status', TracerStudyStatus::Submitted)
            ->pluck('id');

        // Get all numeric answers
        $numericAnswers = TracerAnswer::whereIn('tracer_study_id', $submittedTracerStudyIds)
            ->where('question_id', $question->id)
            ->whereNotNull('answer_value')
            ->where('answer_value', '!=', '')
            ->get()
            ->map(function ($answer) {
                // Filter only valid numeric values
                $value = is_numeric($answer->answer_value) ? (float) $answer->answer_value : null;
                return $value;
            })
            ->filter()
            ->values();

        $totalAnswered = $numericAnswers->count();
        $totalUnanswered = $totalSubmitted - $totalAnswered;

        $min = $numericAnswers->min();
        $max = $numericAnswers->max();
        $avg = $numericAnswers->count() > 0 ? round($numericAnswers->avg(), 2) : null;

        return [
            'total_answered' => $totalAnswered,
            'total_unanswered' => max(0, $totalUnanswered),
            'min' => $min,
            'max' => $max,
            'average' => $avg,
        ];
    }

    /**
     * Get statistics for date questions.
     */
    protected function getDateStatistics(TracerQuestion $question, int $totalSubmitted): array
    {
        $submittedTracerStudyIds = TracerStudy::where('status', TracerStudyStatus::Submitted)
            ->pluck('id');

        // Get all valid date answers
        $dateAnswers = TracerAnswer::whereIn('tracer_study_id', $submittedTracerStudyIds)
            ->where('question_id', $question->id)
            ->whereNotNull('answer_value')
            ->where('answer_value', '!=', '')
            ->get()
            ->map(function ($answer) {
                // Try to parse date, return null if invalid
                try {
                    return \Carbon\Carbon::parse($answer->answer_value);
                } catch (\Exception $e) {
                    return null;
                }
            })
            ->filter()
            ->values();

        $totalAnswered = $dateAnswers->count();
        $totalUnanswered = $totalSubmitted - $totalAnswered;

        $earliest = $dateAnswers->min();
        $latest = $dateAnswers->max();

        return [
            'total_answered' => $totalAnswered,
            'total_unanswered' => max(0, $totalUnanswered),
            'earliest' => $earliest?->format('Y-m-d'),
            'latest' => $latest?->format('Y-m-d'),
        ];
    }

    /**
     * Get statistics for text and textarea questions.
     */
    protected function getTextStatistics(TracerQuestion $question, int $totalSubmitted): array
    {
        $submittedTracerStudyIds = TracerStudy::where('status', TracerStudyStatus::Submitted)
            ->pluck('id');

        // Count answered and unanswered
        $totalAnswered = TracerAnswer::whereIn('tracer_study_id', $submittedTracerStudyIds)
            ->where('question_id', $question->id)
            ->whereNotNull('answer_value')
            ->where('answer_value', '!=', '')
            ->count();

        $totalUnanswered = $totalSubmitted - $totalAnswered;

        return [
            'total_answered' => $totalAnswered,
            'total_unanswered' => max(0, $totalUnanswered),
        ];
    }

    /**
     * Get empty statistics structure.
     */
    protected function getEmptyStatistics(int $totalSubmitted): array
    {
        return [
            'total_answered' => 0,
            'total_unanswered' => $totalSubmitted,
        ];
    }

    /**
     * Get summary statistics for all questions.
     */
    public function getSummary(): array
    {
        $totalSubmitted = TracerStudy::where('status', TracerStudyStatus::Submitted)->count();
        $totalQuestions = TracerQuestion::count();
        $totalActiveQuestions = TracerQuestion::where('is_active', true)->count();

        return [
            'total_submitted_responses' => $totalSubmitted,
            'total_questions' => $totalQuestions,
            'total_active_questions' => $totalActiveQuestions,
        ];
    }
}
