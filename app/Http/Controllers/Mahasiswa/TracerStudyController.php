<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\TracerQuestion;
use App\Models\TracerStudy;
use App\Enums\TracerStudyStatus;
use App\Enums\QuestionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TracerStudyController extends Controller
{
    /**
     * Display the tracer study page for the authenticated mahasiswa.
     */
    public function index()
    {
        $user = Auth::user();

        // Get profile for this user
        $profile = $user->profile;

        if (!$profile || !$profile->isLulus()) {
            // User doesn't have a lulus profile
            return view('mahasiswa.tracer-study.index', [
                'profile' => null,
                'tracerStudy' => null,
                'status' => 'no_alumni',
            ]);
        }

        // Get tracer study for this profile
        $tracerStudy = $profile->tracerStudy;

        // Determine status
        $status = match (true) {
            !$tracerStudy => 'not_started',
            $tracerStudy->isDraft() => 'draft',
            $tracerStudy->isSubmitted() => 'submitted',
            default => 'not_started',
        };

        return view('mahasiswa.tracer-study.index', [
            'profile' => $profile,
            'tracerStudy' => $tracerStudy,
            'status' => $status,
        ]);
    }

    /**
     * Show the tracer study form.
     */
    public function create()
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile || !$profile->isLulus()) {
            return redirect()->route('mahasiswa.tracer-study.index')
                ->with('error', 'Data alumni tidak ditemukan.');
        }

        // Get or create draft tracer study using profile_id
        $tracerStudy = TracerStudy::firstOrCreate(
            ['profile_id' => $profile->id],
            [
                'status' => TracerStudyStatus::Draft,
                'started_at' => now(),
            ]
        );

        // If already submitted, redirect back
        if ($tracerStudy->isSubmitted()) {
            return redirect()->route('mahasiswa.tracer-study.index')
                ->with('error', 'Tracer Study sudah disubmit dan tidak dapat diubah.');
        }

        // Get active questions ordered with their active options (eager loaded)
        $questions = TracerQuestion::query()
            ->active()
            ->ordered()
            ->with(['activeQuestionOptions' => function ($query) {
                $query->ordered();
            }])
            ->get();

        // Get existing answers keyed by question_id
        $answers = $tracerStudy->answers()
            ->pluck('answer_value', 'question_id')
            ->toArray();

        // Decode checkbox answers (stored as JSON)
        foreach ($answers as $questionId => $value) {
            if (is_string($value) && str_starts_with($value, '[')) {
                $decoded = json_decode($value, true);
                if (is_array($decoded)) {
                    $answers[$questionId] = $decoded;
                }
            }
        }

        return view('mahasiswa.tracer-study.form', [
            'tracerStudy' => $tracerStudy,
            'questions' => $questions,
            'answers' => $answers,
        ]);
    }

    /**
     * Save the tracer study answers as draft.
     */
    public function save(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile || !$profile->isLulus()) {
            return redirect()->route('mahasiswa.tracer-study.index')
                ->with('error', 'Data alumni tidak ditemukan.');
        }

        // Get or create draft tracer study using profile_id
        $tracerStudy = TracerStudy::firstOrCreate(
            ['profile_id' => $profile->id],
            [
                'status' => TracerStudyStatus::Draft,
                'started_at' => now(),
            ]
        );

        // If already submitted, cannot save
        if ($tracerStudy->isSubmitted()) {
            return redirect()->route('mahasiswa.tracer-study.index')
                ->with('error', 'Tracer Study sudah disubmit dan tidak dapat diubah.');
        }

        // Validate with dynamic rules
        $validator = $this->validateAnswers($request, $tracerStudy);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Save answers
        $answers = $request->input('answers', []);
        $this->saveAnswers($tracerStudy, $answers);

        return redirect()->back()
            ->with('success', 'Jawaban berhasil disimpan sebagai draft.');
    }

    /**
     * Submit the tracer study.
     */
    public function submit(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile || !$profile->isLulus()) {
            return redirect()->route('mahasiswa.tracer-study.index')
                ->with('error', 'Data alumni tidak ditemukan.');
        }

        $tracerStudy = $profile->tracerStudy;

        if (!$tracerStudy) {
            return redirect()->route('mahasiswa.tracer-study.index')
                ->with('error', 'Tracer Study tidak ditemukan.');
        }

        if ($tracerStudy->isSubmitted()) {
            return redirect()->route('mahasiswa.tracer-study.index')
                ->with('error', 'Tracer Study sudah disubmit.');
        }

        // Validate with dynamic rules (strict - required fields enforced)
        $validator = $this->validateAnswers($request, $tracerStudy, true);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Save answers (use merged answers from validation)
        $answers = $this->getMergedAnswers($request, $tracerStudy);
        $this->saveAnswers($tracerStudy, $answers);

        // Mark as submitted
        $tracerStudy->markAsSubmitted();

        return redirect()->route('mahasiswa.tracer-study.index')
            ->with('success', 'Tracer Study berhasil disubmit.');
    }

    /**
     * Validate answers dynamically based on question types and options.
     */
    protected function validateAnswers(Request $request, TracerStudy $tracerStudy, bool $strict = false): \Illuminate\Validation\Validator
    {
        // Get active questions with their options
        $questions = TracerQuestion::query()
            ->active()
            ->with(['activeQuestionOptions' => function ($query) {
                $query->ordered();
            }])
            ->get()
            ->keyBy('id');

        $rules = [];
        $messages = [];

        // Get submitted answers
        $submittedAnswers = $request->input('answers', []);

        // For strict validation (submit), merge with existing answers from DB
        // This allows submitting even if some answers were saved as draft without POST data
        if ($strict) {
            $existingAnswers = $tracerStudy->answers()
                ->pluck('answer_value', 'question_id')
                ->toArray();

            // Decode JSON checkbox answers from existing answers
            foreach ($existingAnswers as $qid => $value) {
                if (is_string($value) && str_starts_with($value, '[')) {
                    $decoded = json_decode($value, true);
                    if (is_array($decoded)) {
                        $existingAnswers[$qid] = $decoded;
                    }
                }
            }

            // Merge: submitted answers take precedence, fall back to existing
            foreach ($questions as $questionId => $question) {
                if (!isset($submittedAnswers[$questionId]) && isset($existingAnswers[$questionId])) {
                    $submittedAnswers[$questionId] = $existingAnswers[$questionId];
                }
            }
        }

        foreach ($questions as $questionId => $question) {
            $answerKey = "answers.{$questionId}";
            $questionRules = [];

            // Convert type to enum if it's a string
            $type = $question->type;
            if (is_string($type)) {
                $type = QuestionType::from($type);
            }

            // Determine if this question is required
            $isRequired = $strict && $question->is_required;

            // Build validation rules based on type
            switch ($type) {
                case QuestionType::Text:
                case QuestionType::Textarea:
                    $questionRules[] = $isRequired ? 'required' : 'nullable';
                    $questionRules[] = 'string';
                    $questionRules[] = 'max:65535';
                    break;

                case QuestionType::Number:
                    $questionRules[] = $isRequired ? 'required' : 'nullable';
                    $questionRules[] = 'numeric';
                    break;

                case QuestionType::Date:
                    $questionRules[] = $isRequired ? 'required' : 'nullable';
                    $questionRules[] = 'date';
                    break;

                case QuestionType::Radio:
                    if ($isRequired) {
                        $questionRules[] = 'required';
                    } else {
                        $questionRules[] = 'nullable';
                    }
                    $questionRules[] = 'string';

                    // Validate against valid option values
                    $validValues = $question->activeQuestionOptions->pluck('value')->toArray();
                    if (!empty($validValues)) {
                        $questionRules[] = Rule::in($validValues);
                        $messages["{$answerKey}.in"] = "Pilihan tidak valid untuk pertanyaan ini.";
                    }
                    break;

                case QuestionType::Select:
                    if ($isRequired) {
                        $questionRules[] = 'required';
                    } else {
                        $questionRules[] = 'nullable';
                    }
                    $questionRules[] = 'string';

                    // Validate against valid option values
                    $validValues = $question->activeQuestionOptions->pluck('value')->toArray();
                    if (!empty($validValues)) {
                        $questionRules[] = Rule::in($validValues);
                        $messages["{$answerKey}.in"] = "Pilihan tidak valid untuk pertanyaan ini.";
                    }
                    break;

                case QuestionType::Checkbox:
                    // For checkbox, we need array input
                    if ($isRequired) {
                        $questionRules[] = 'required';
                    } else {
                        $questionRules[] = 'nullable';
                    }
                    $questionRules[] = 'array';

                    // Validate each checkbox value against valid options
                    $validValues = $question->activeQuestionOptions->pluck('value')->toArray();
                    if (!empty($validValues)) {
                        $questionRules[] = 'array';
                        $questionRules[] = function ($attribute, $value, $fail) use ($validValues, $answerKey) {
                            if (!is_array($value)) {
                                return;
                            }
                            $invalidValues = array_diff($value, $validValues);
                            if (!empty($invalidValues)) {
                                $fail("Pilihan tidak valid untuk pertanyaan ini.");
                            }
                        };
                    }
                    break;
            }

            $rules[$answerKey] = $questionRules;

            // Custom messages for required
            if ($isRequired) {
                $messages["{$answerKey}.required"] = "Pertanyaan ini wajib diisi.";
            }
        }

        // Create data array with merged answers
        $data = ['answers' => $submittedAnswers];

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Get merged answers (submitted + existing).
     */
    protected function getMergedAnswers(Request $request, TracerStudy $tracerStudy): array
    {
        $questions = TracerQuestion::query()
            ->active()
            ->with(['activeQuestionOptions' => function ($query) {
                $query->ordered();
            }])
            ->get()
            ->keyBy('id');

        $submittedAnswers = $request->input('answers', []);

        $existingAnswers = $tracerStudy->answers()
            ->pluck('answer_value', 'question_id')
            ->toArray();

        // Decode JSON checkbox answers from existing answers
        foreach ($existingAnswers as $qid => $value) {
            if (is_string($value) && str_starts_with($value, '[')) {
                $decoded = json_decode($value, true);
                if (is_array($decoded)) {
                    $existingAnswers[$qid] = $decoded;
                }
            }
        }

        // Merge: submitted answers take precedence, fall back to existing
        foreach ($questions as $questionId => $question) {
            if (!isset($submittedAnswers[$questionId]) && isset($existingAnswers[$questionId])) {
                $submittedAnswers[$questionId] = $existingAnswers[$questionId];
            }
        }

        return $submittedAnswers;
    }

    /**
     * Save answers to the database.
     */
    protected function saveAnswers(TracerStudy $tracerStudy, array $answers): void
    {
        $questionIds = TracerQuestion::active()->pluck('id')->toArray();

        foreach ($answers as $questionId => $answerValue) {
            // Skip if question is not active
            if (!in_array((int) $questionId, $questionIds)) {
                continue;
            }

            // Skip empty values
            if ($answerValue === '' || $answerValue === null) {
                // Delete existing answer if any
                $tracerStudy->answers()
                    ->where('question_id', $questionId)
                    ->delete();
                continue;
            }

            // Handle checkbox - encode as JSON
            if (is_array($answerValue)) {
                // Filter out empty values
                $answerValue = array_values(array_filter($answerValue, function ($v) {
                    return $v !== '' && $v !== null;
                }));

                if (empty($answerValue)) {
                    $tracerStudy->answers()
                        ->where('question_id', $questionId)
                        ->delete();
                    continue;
                }

                $answerValue = json_encode($answerValue);
            }

            // Update or create answer
            $tracerStudy->answers()->updateOrCreate(
                [
                    'tracer_study_id' => $tracerStudy->id,
                    'question_id' => $questionId,
                ],
                [
                    'answer_value' => $answerValue,
                ]
            );
        }

        // Remove answers for questions that are no longer in the submitted data
        // But only for active questions (don't touch historical answers)
        $submittedQuestionIds = array_keys($answers);
        $tracerStudy->answers()
            ->whereIn('question_id', $questionIds)
            ->whereNotIn('question_id', $submittedQuestionIds)
            ->delete();
    }
}
