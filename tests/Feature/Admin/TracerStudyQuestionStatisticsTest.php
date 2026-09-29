<?php

namespace Tests\Feature\Admin;

use App\Enums\QuestionType;
use App\Enums\Role;
use App\Enums\TracerStudyStatus;
use App\Models\Alumni;
use App\Models\TracerQuestion;
use App\Models\TracerQuestionOption;
use App\Models\TracerStudy;
use App\Models\TracerAnswer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TracerStudyQuestionStatisticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $mahasiswaUser;
    protected Alumni $alumni;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => Role::Admin]);
        $this->mahasiswaUser = User::factory()->create(['role' => Role::Mahasiswa]);
    }

    // ============================================
    // ACCESS CONTROL TESTS
    // ============================================

    public function test_admin_can_view_question_statistics(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.statistics.question');
    }

    public function test_mahasiswa_cannot_access_question_statistics(): void
    {
        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_question_statistics(): void
    {
        $response = $this->get(route('admin.statistics.question'));

        $response->assertStatus(302);
    }

    // ============================================
    // SUBMITTED VS DRAFT TESTS
    // ============================================

    public function test_submitted_response_is_counted(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Text,
            'is_active' => true,
        ]);

        $alumni = Alumni::factory()->create();
        $tracerStudy = TracerStudy::create([
            'alumni_id' => $alumni->id,
            'status' => TracerStudyStatus::Submitted,
        ]);
        TracerAnswer::create([
            'tracer_study_id' => $tracerStudy->id,
            'question_id' => $question->id,
            'answer_value' => 'Test answer',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('summary', function ($summary) {
            return $summary['total_submitted_responses'] === 1;
        });
    }

    public function test_draft_response_is_not_counted(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Text,
            'is_active' => true,
        ]);

        $alumni = Alumni::factory()->create();
        TracerStudy::create([
            'alumni_id' => $alumni->id,
            'status' => TracerStudyStatus::Draft,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('summary', function ($summary) {
            return $summary['total_submitted_responses'] === 0;
        });
    }

    // ============================================
    // RADIO QUESTION STATISTICS TESTS
    // ============================================

    public function test_radio_statistics_shows_correct_counts(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Radio,
            'is_active' => true,
        ]);
        $option1 = TracerQuestionOption::factory()->create([
            'tracer_question_id' => $question->id,
            'label' => 'Bekerja',
            'value' => 'bekerja',
        ]);
        $option2 = TracerQuestionOption::factory()->create([
            'tracer_question_id' => $question->id,
            'label' => 'Belum Bekerja',
            'value' => 'belum_bekerja',
        ]);

        // Create 5 submitted responses
        for ($i = 0; $i < 5; $i++) {
            $alumni = Alumni::factory()->create();
            $tracerStudy = TracerStudy::create([
                'alumni_id' => $alumni->id,
                'status' => TracerStudyStatus::Submitted,
            ]);
            TracerAnswer::create([
                'tracer_study_id' => $tracerStudy->id,
                'question_id' => $question->id,
                'answer_value' => $i < 3 ? 'bekerja' : 'belum_bekerja',
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) use ($question) {
            $qStats = collect($stats)->firstWhere('id', $question->id);
            $option1Stats = collect($qStats['options'])->firstWhere('value', 'bekerja');
            $option2Stats = collect($qStats['options'])->firstWhere('value', 'belum_bekerja');

            return $option1Stats['count'] === 3 &&
                   $option2Stats['count'] === 2 &&
                   $option1Stats['percentage'] === 60.0 &&
                   $option2Stats['percentage'] === 40.0;
        });
    }

    // ============================================
    // OPTION WITH ZERO COUNT TESTS
    // ============================================

    public function test_option_with_zero_count_still_displayed(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Radio,
            'is_active' => true,
        ]);
        $option1 = TracerQuestionOption::factory()->create([
            'tracer_question_id' => $question->id,
            'label' => 'Bekerja',
            'value' => 'bekerja',
        ]);
        $option2 = TracerQuestionOption::factory()->create([
            'tracer_question_id' => $question->id,
            'label' => 'Studi Lanjut',
            'value' => 'studi_lanjut',
        ]);

        // Only create answers for option1
        $alumni = Alumni::factory()->create();
        $tracerStudy = TracerStudy::create([
            'alumni_id' => $alumni->id,
            'status' => TracerStudyStatus::Submitted,
        ]);
        TracerAnswer::create([
            'tracer_study_id' => $tracerStudy->id,
            'question_id' => $question->id,
            'answer_value' => 'bekerja',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) use ($question) {
            $qStats = collect($stats)->firstWhere('id', $question->id);
            $option2Stats = collect($qStats['options'])->firstWhere('value', 'studi_lanjut');

            return $option2Stats !== null &&
                   $option2Stats['count'] === 0 &&
                   $option2Stats['percentage'] == 0.0;
        });
    }

    // ============================================
    // DIVISION BY ZERO TESTS
    // ============================================

    public function test_division_by_zero_handled_correctly(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Radio,
            'is_active' => true,
        ]);
        TracerQuestionOption::factory()->create([
            'tracer_question_id' => $question->id,
            'label' => 'Option',
            'value' => 'option',
        ]);

        // No submitted responses - no division should occur
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) use ($question) {
            $qStats = collect($stats)->firstWhere('id', $question->id);
            $optionStats = collect($qStats['options'])->first();

            return $optionStats['count'] === 0 &&
                   $optionStats['percentage'] == 0.0;
        });
    }

    // ============================================
    // OPTIONAL QUESTION TESTS
    // ============================================

    public function test_optional_question_unanswered_counted_correctly(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Text,
            'is_required' => false,
            'is_active' => true,
        ]);

        // 5 submitted, 3 answered, 2 unanswered
        for ($i = 0; $i < 5; $i++) {
            $alumni = Alumni::factory()->create();
            $tracerStudy = TracerStudy::create([
                'alumni_id' => $alumni->id,
                'status' => TracerStudyStatus::Submitted,
            ]);
            if ($i < 3) {
                TracerAnswer::create([
                    'tracer_study_id' => $tracerStudy->id,
                    'question_id' => $question->id,
                    'answer_value' => 'Answer ' . $i,
                ]);
            }
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) use ($question) {
            $qStats = collect($stats)->firstWhere('id', $question->id);

            return $qStats['total_answered'] === 3 &&
                   $qStats['total_unanswered'] === 2;
        });
    }

    // ============================================
    // CHECKBOX STATISTICS TESTS
    // ============================================

    public function test_checkbox_statistics_counted_correctly(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Checkbox,
            'is_active' => true,
        ]);
        $option1 = TracerQuestionOption::factory()->create([
            'tracer_question_id' => $question->id,
            'label' => 'Programming',
            'value' => 'programming',
        ]);
        $option2 = TracerQuestionOption::factory()->create([
            'tracer_question_id' => $question->id,
            'label' => 'Design',
            'value' => 'design',
        ]);

        // 3 submitted responses
        for ($i = 0; $i < 3; $i++) {
            $alumni = Alumni::factory()->create();
            $tracerStudy = TracerStudy::create([
                'alumni_id' => $alumni->id,
                'status' => TracerStudyStatus::Submitted,
            ]);
            $values = [];
            if ($i < 2) $values[] = 'programming'; // 2 chose programming
            if ($i === 0 || $i === 2) $values[] = 'design'; // 2 chose design
            TracerAnswer::create([
                'tracer_study_id' => $tracerStudy->id,
                'question_id' => $question->id,
                'answer_value' => json_encode($values),
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) use ($question) {
            $qStats = collect($stats)->firstWhere('id', $question->id);
            $opt1Stats = collect($qStats['options'])->firstWhere('value', 'programming');
            $opt2Stats = collect($qStats['options'])->firstWhere('value', 'design');

            // 2 chose programming, 2 chose design
            // Total is 3 responses, so percentages are based on 3
            return $opt1Stats['count'] === 2 &&
                   $opt2Stats['count'] === 2;
        });
    }

    // ============================================
    // NUMBER QUESTION STATISTICS TESTS
    // ============================================

    public function test_number_min_max_average_calculated_correctly(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Number,
            'is_active' => true,
        ]);

        // Create answers: 2, 4, 6
        $values = [2, 4, 6];
        foreach ($values as $val) {
            $alumni = Alumni::factory()->create();
            $tracerStudy = TracerStudy::create([
                'alumni_id' => $alumni->id,
                'status' => TracerStudyStatus::Submitted,
            ]);
            TracerAnswer::create([
                'tracer_study_id' => $tracerStudy->id,
                'question_id' => $question->id,
                'answer_value' => (string) $val,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) use ($question) {
            $qStats = collect($stats)->firstWhere('id', $question->id);

            return $qStats['min'] === 2.0 &&
                   $qStats['max'] === 6.0 &&
                   $qStats['average'] === 4.0;
        });
    }

    public function test_number_with_invalid_values_ignored(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Number,
            'is_active' => true,
        ]);

        // Create mixed valid and invalid answers
        $validValues = [2, 4, 6];
        $invalidValues = ['abc', '', null];

        $allValues = array_merge($validValues, $invalidValues);
        foreach ($allValues as $val) {
            $alumni = Alumni::factory()->create();
            $tracerStudy = TracerStudy::create([
                'alumni_id' => $alumni->id,
                'status' => TracerStudyStatus::Submitted,
            ]);
            TracerAnswer::create([
                'tracer_study_id' => $tracerStudy->id,
                'question_id' => $question->id,
                'answer_value' => $val === null ? null : (string) $val,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) use ($question) {
            $qStats = collect($stats)->firstWhere('id', $question->id);

            // Only 3 valid numeric answers
            return $qStats['total_answered'] === 3 &&
                   $qStats['min'] === 2.0 &&
                   $qStats['max'] === 6.0;
        });
    }

    // ============================================
    // DATE QUESTION STATISTICS TESTS
    // ============================================

    public function test_date_earliest_latest_calculated_correctly(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Date,
            'is_active' => true,
        ]);

        $dates = ['2023-01-15', '2023-06-20', '2023-03-10'];
        foreach ($dates as $date) {
            $alumni = Alumni::factory()->create();
            $tracerStudy = TracerStudy::create([
                'alumni_id' => $alumni->id,
                'status' => TracerStudyStatus::Submitted,
            ]);
            TracerAnswer::create([
                'tracer_study_id' => $tracerStudy->id,
                'question_id' => $question->id,
                'answer_value' => $date,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) use ($question) {
            $qStats = collect($stats)->firstWhere('id', $question->id);

            return $qStats['earliest'] === '2023-01-15' &&
                   $qStats['latest'] === '2023-06-20';
        });
    }

    public function test_date_with_invalid_values_handled(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Date,
            'is_active' => true,
        ]);

        // Valid date
        $alumni1 = Alumni::factory()->create();
        $tracerStudy1 = TracerStudy::create([
            'alumni_id' => $alumni1->id,
            'status' => TracerStudyStatus::Submitted,
        ]);
        TracerAnswer::create([
            'tracer_study_id' => $tracerStudy1->id,
            'question_id' => $question->id,
            'answer_value' => '2023-05-15',
        ]);

        // Invalid date
        $alumni2 = Alumni::factory()->create();
        $tracerStudy2 = TracerStudy::create([
            'alumni_id' => $alumni2->id,
            'status' => TracerStudyStatus::Submitted,
        ]);
        TracerAnswer::create([
            'tracer_study_id' => $tracerStudy2->id,
            'question_id' => $question->id,
            'answer_value' => 'invalid-date',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) use ($question) {
            $qStats = collect($stats)->firstWhere('id', $question->id);

            // Only 1 valid date counted
            return $qStats['total_answered'] === 1 &&
                   $qStats['earliest'] === '2023-05-15' &&
                   $qStats['latest'] === '2023-05-15';
        });
    }

    // ============================================
    // TEXT QUESTION STATISTICS TESTS
    // ============================================

    public function test_text_answered_unanswered_counted(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Text,
            'is_active' => true,
        ]);

        // 4 submitted, 3 answered, 1 unanswered
        for ($i = 0; $i < 4; $i++) {
            $alumni = Alumni::factory()->create();
            $tracerStudy = TracerStudy::create([
                'alumni_id' => $alumni->id,
                'status' => TracerStudyStatus::Submitted,
            ]);
            if ($i < 3) {
                TracerAnswer::create([
                    'tracer_study_id' => $tracerStudy->id,
                    'question_id' => $question->id,
                    'answer_value' => 'Some text answer',
                ]);
            }
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) use ($question) {
            $qStats = collect($stats)->firstWhere('id', $question->id);

            return $qStats['total_answered'] === 3 &&
                   $qStats['total_unanswered'] === 1;
        });
    }

    // ============================================
    // INACTIVE QUESTION TESTS
    // ============================================

    public function test_inactive_question_historical_data_preserved(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Radio,
            'is_active' => false, // Inactive question
        ]);
        $option1 = TracerQuestionOption::factory()->create([
            'tracer_question_id' => $question->id,
            'label' => 'Active Option',
            'value' => 'active',
            'is_active' => true,
        ]);
        $option2 = TracerQuestionOption::factory()->create([
            'tracer_question_id' => $question->id,
            'label' => 'Inactive Option',
            'value' => 'inactive',
            'is_active' => false,
        ]);

        // Create historical answers
        $alumni = Alumni::factory()->create();
        $tracerStudy = TracerStudy::create([
            'alumni_id' => $alumni->id,
            'status' => TracerStudyStatus::Submitted,
        ]);
        TracerAnswer::create([
            'tracer_study_id' => $tracerStudy->id,
            'question_id' => $question->id,
            'answer_value' => 'active',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) use ($question) {
            $qStats = collect($stats)->firstWhere('id', $question->id);

            return $qStats['is_active'] === false &&
                   $qStats['total_submitted_responses'] >= 1;
        });
    }

    // ============================================
    // INACTIVE OPTION TESTS
    // ============================================

    public function test_inactive_option_historical_data_preserved(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Radio,
            'is_active' => true,
        ]);
        TracerQuestionOption::factory()->create([
            'tracer_question_id' => $question->id,
            'label' => 'Currently Active',
            'value' => 'active',
            'is_active' => true,
        ]);
        $inactiveOption = TracerQuestionOption::factory()->create([
            'tracer_question_id' => $question->id,
            'label' => 'Now Inactive',
            'value' => 'inactive',
            'is_active' => false,
        ]);

        // Create answer for inactive option
        $alumni = Alumni::factory()->create();
        $tracerStudy = TracerStudy::create([
            'alumni_id' => $alumni->id,
            'status' => TracerStudyStatus::Submitted,
        ]);
        TracerAnswer::create([
            'tracer_study_id' => $tracerStudy->id,
            'question_id' => $question->id,
            'answer_value' => 'inactive',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) use ($question) {
            $qStats = collect($stats)->firstWhere('id', $question->id);
            $optStats = collect($qStats['options'])->firstWhere('value', 'inactive');

            return $optStats !== null &&
                   $optStats['is_active'] === false &&
                   $optStats['count'] === 1;
        });
    }

    // ============================================
    // QUESTION ORDER TESTS
    // ============================================

    public function test_questions_ordered_by_order_column(): void
    {
        $q3 = TracerQuestion::factory()->create(['order' => 3, 'question_text' => 'Question 3']);
        $q1 = TracerQuestion::factory()->create(['order' => 1, 'question_text' => 'Question 1']);
        $q2 = TracerQuestion::factory()->create(['order' => 2, 'question_text' => 'Question 2']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) {
            return $stats[0]['question_text'] === 'Question 1' &&
                   $stats[1]['question_text'] === 'Question 2' &&
                   $stats[2]['question_text'] === 'Question 3';
        });
    }

    // ============================================
    // EMPTY STATE TESTS
    // ============================================

    public function test_empty_submitted_response_shows_empty_state(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertSee('Belum ada data Tracer Study yang dapat dianalisis');
    }

    // ============================================
    // DUPLICATE PROTECTION TESTS
    // ============================================

    public function test_duplicate_tracer_study_not_double_counted(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Text,
            'is_active' => true,
        ]);

        // One alumni with one tracer study
        $alumni = Alumni::factory()->create();
        $tracerStudy = TracerStudy::create([
            'alumni_id' => $alumni->id,
            'status' => TracerStudyStatus::Submitted,
        ]);
        TracerAnswer::create([
            'tracer_study_id' => $tracerStudy->id,
            'question_id' => $question->id,
            'answer_value' => 'Answer',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('summary', function ($summary) {
            // Only 1 submitted response
            return $summary['total_submitted_responses'] === 1;
        });
    }

    // ============================================
    // PRIVACY TESTS
    // ============================================

    public function test_no_personal_data_in_statistics(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Text,
            'is_active' => true,
        ]);

        $alumni = Alumni::factory()->create([
            'nama_lengkap' => 'John Doe Private',
            'email' => 'private@example.com',
        ]);
        $tracerStudy = TracerStudy::create([
            'alumni_id' => $alumni->id,
            'status' => TracerStudyStatus::Submitted,
        ]);
        TracerAnswer::create([
            'tracer_study_id' => $tracerStudy->id,
            'question_id' => $question->id,
            'answer_value' => 'Some answer with private data',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $content = $response->getContent();

        // Personal data should NOT appear in statistics page
        $this->assertStringNotContainsString('John Doe Private', $content);
        $this->assertStringNotContainsString('private@example.com', $content);
    }

    // ============================================
    // PARTIAL ANSWER TESTS
    // ============================================

    public function test_partial_answer_questions_counted_correctly(): void
    {
        $question1 = TracerQuestion::factory()->create([
            'type' => QuestionType::Text,
            'is_active' => true,
        ]);
        $question2 = TracerQuestion::factory()->create([
            'type' => QuestionType::Radio,
            'is_active' => true,
        ]);
        TracerQuestionOption::factory()->create([
            'tracer_question_id' => $question2->id,
            'label' => 'Option',
            'value' => 'option',
        ]);

        // 3 submitted responses, but only 2 answered question1
        for ($i = 0; $i < 3; $i++) {
            $alumni = Alumni::factory()->create();
            $tracerStudy = TracerStudy::create([
                'alumni_id' => $alumni->id,
                'status' => TracerStudyStatus::Submitted,
            ]);
            if ($i < 2) {
                TracerAnswer::create([
                    'tracer_study_id' => $tracerStudy->id,
                    'question_id' => $question1->id,
                    'answer_value' => 'Answer',
                ]);
            }
            TracerAnswer::create([
                'tracer_study_id' => $tracerStudy->id,
                'question_id' => $question2->id,
                'answer_value' => 'option',
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) use ($question1, $question2) {
            $q1Stats = collect($stats)->firstWhere('id', $question1->id);
            $q2Stats = collect($stats)->firstWhere('id', $question2->id);

            // Question 1: 2 answered, 1 unanswered (out of 3 total)
            // Question 2: 3 answered (all)
            return $q1Stats['total_answered'] === 2 &&
                   $q1Stats['total_unanswered'] === 1 &&
                   $q2Stats['total_answered'] === 3;
        });
    }

    // ============================================
    // VIEW STRUCTURE TESTS
    // ============================================

    public function test_view_has_correct_structure(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);

        // Check all required view variables
        $response->assertViewHas('summary');
        $response->assertViewHas('questionStatistics');

        $summary = $response->viewData('summary');
        $this->assertArrayHasKey('total_submitted_responses', $summary);
        $this->assertArrayHasKey('total_questions', $summary);
        $this->assertArrayHasKey('total_active_questions', $summary);
    }

    // ============================================
    // SELECT QUESTION TESTS
    // ============================================

    public function test_select_statistics_same_as_radio(): void
    {
        $question = TracerQuestion::factory()->create([
            'type' => QuestionType::Select,
            'is_active' => true,
        ]);
        $option1 = TracerQuestionOption::factory()->create([
            'tracer_question_id' => $question->id,
            'label' => 'Option A',
            'value' => 'option_a',
        ]);
        $option2 = TracerQuestionOption::factory()->create([
            'tracer_question_id' => $question->id,
            'label' => 'Option B',
            'value' => 'option_b',
        ]);

        // 4 submitted responses
        for ($i = 0; $i < 4; $i++) {
            $alumni = Alumni::factory()->create();
            $tracerStudy = TracerStudy::create([
                'alumni_id' => $alumni->id,
                'status' => TracerStudyStatus::Submitted,
            ]);
            TracerAnswer::create([
                'tracer_study_id' => $tracerStudy->id,
                'question_id' => $question->id,
                'answer_value' => $i < 3 ? 'option_a' : 'option_b',
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.question'));

        $response->assertStatus(200);
        $response->assertViewHas('questionStatistics', function ($stats) use ($question) {
            $qStats = collect($stats)->firstWhere('id', $question->id);
            $opt1Stats = collect($qStats['options'])->firstWhere('value', 'option_a');
            $opt2Stats = collect($qStats['options'])->firstWhere('value', 'option_b');

            return $opt1Stats['count'] === 3 &&
                   $opt2Stats['count'] === 1 &&
                   $opt1Stats['percentage'] === 75.0;
        });
    }
}
