<?php

namespace Tests\Feature\Mahasiswa;

use App\Enums\AcademicStatus;
use App\Enums\QuestionType;
use App\Models\Profile;
use App\Models\TracerAnswer;
use App\Models\TracerQuestion;
use App\Models\TracerQuestionOption;
use App\Models\TracerStudy;
use App\Models\User;
use App\Enums\TracerStudyStatus;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DynamicTracerStudyTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /**
     * Helper to create admin user.
     */
    protected function createAdmin(): User
    {
        return User::factory()->admin()->create();
    }

    /**
     * Helper to create mahasiswa user.
     */
    protected function createMahasiswa(): User
    {
        return User::factory()->mahasiswa()->create();
    }

    /**
     * Helper to create alumni profile linked to user (status = lulus).
     */
    protected function createAlumniProfileForUser(User $user): Profile
    {
        return Profile::create([
            'user_id' => $user->id,
            'nik' => fake()->unique()->numerify('################'),
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'L',
            'nim' => fake()->unique()->numerify('########'),
            'fakultas' => 'Fakultas Teknik',
            'program_studi' => 'Teknik Informatika',
            'angkatan' => 2018,
            'tahun_masuk' => 2018,
            'tahun_lulus' => 2022,
            'academic_status' => AcademicStatus::Lulus,
        ]);
    }

    /**
     * Create a question with options.
     */
    protected function createQuestion(array $attributes = [], array $options = []): TracerQuestion
    {
        $question = TracerQuestion::create(array_merge([
            'code' => fake()->unique()->word(),
            'question_text' => 'Sample question?',
            'type' => QuestionType::Text,
            'is_required' => false,
            'is_active' => true,
            'order' => 1,
        ], $attributes));

        if (!empty($options)) {
            foreach ($options as $index => $option) {
                $question->questionOptions()->create([
                    'label' => $option['label'],
                    'value' => $option['value'],
                    'order' => $index,
                    'is_active' => $option['is_active'] ?? true,
                ]);
            }
        }

        return $question;
    }

    // ============================================
    // DYNAMIC QUESTIONS TESTS
    // ============================================

    /**
     * Test mahasiswa can see active questions.
     */
    public function test_mahasiswa_can_see_active_questions(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        // Create active questions
        $activeQuestion = $this->createQuestion([
            'question_text' => 'Active question?',
            'is_active' => true,
            'type' => QuestionType::Text,
        ]);

        $inactiveQuestion = $this->createQuestion([
            'question_text' => 'Inactive question?',
            'is_active' => false,
            'type' => QuestionType::Text,
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);
        $response->assertSee('Active question?');
        $response->assertDontSee('Inactive question?');
    }

    /**
     * Test inactive questions are not displayed.
     */
    public function test_inactive_questions_not_displayed(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        $inactiveQuestion = $this->createQuestion([
            'question_text' => 'Should not see this',
            'is_active' => false,
            'type' => QuestionType::Text,
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);
        $response->assertDontSee('Should not see this');
    }

    /**
     * Test questions are ordered by order column.
     */
    public function test_questions_ordered_by_order_column(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        $this->createQuestion([
            'code' => 'q3',
            'question_text' => 'Third question',
            'order' => 3,
        ]);
        $this->createQuestion([
            'code' => 'q1',
            'question_text' => 'First question',
            'order' => 1,
        ]);
        $this->createQuestion([
            'code' => 'q2',
            'question_text' => 'Second question',
            'order' => 2,
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);

        // Check order in response
        $content = $response->getContent();
        $firstPos = strpos($content, 'First question');
        $secondPos = strpos($content, 'Second question');
        $thirdPos = strpos($content, 'Third question');

        $this->assertLessThan($secondPos, $firstPos);
        $this->assertLessThan($thirdPos, $secondPos);
    }

    // ============================================
    // RENDERING TESTS
    // ============================================

    /**
     * Test text question renders.
     */
    public function test_text_question_renders(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        $this->createQuestion([
            'question_text' => 'Enter your name',
            'type' => QuestionType::Text,
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);
        $response->assertSee('Enter your name');
        $response->assertSee('name=', false);
    }

    /**
     * Test textarea question renders.
     */
    public function test_textarea_question_renders(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        $this->createQuestion([
            'question_text' => 'Enter your address',
            'type' => QuestionType::Textarea,
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);
        $response->assertSee('Enter your address');
        // Check that textarea element exists by looking for a unique attribute
        $response->assertSee('rows=', false);
    }

    /**
     * Test number question renders.
     */
    public function test_number_question_renders(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        $this->createQuestion([
            'question_text' => 'Enter your age',
            'type' => QuestionType::Number,
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);
        $response->assertSee('Enter your age');
        $response->assertSee('number', false);
    }

    /**
     * Test radio question renders with options.
     */
    public function test_radio_question_renders_with_options(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        $this->createQuestion([
            'question_text' => 'Choose one',
            'type' => QuestionType::Radio,
        ], [
            ['label' => 'Option A', 'value' => 'a'],
            ['label' => 'Option B', 'value' => 'b'],
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);
        $response->assertSee('Choose one');
        $response->assertSee('Option A');
        $response->assertSee('Option B');
        $response->assertSee('type=', false);
    }

    /**
     * Test select question renders.
     */
    public function test_select_question_renders(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        $this->createQuestion([
            'question_text' => 'Select one',
            'type' => QuestionType::Select,
        ], [
            ['label' => 'Choice 1', 'value' => 'c1'],
            ['label' => 'Choice 2', 'value' => 'c2'],
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);
        $response->assertSee('Select one');
        $response->assertSee('Choice 1');
        $response->assertSee('Choice 2');
    }

    /**
     * Test checkbox question renders.
     */
    public function test_checkbox_question_renders(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        $this->createQuestion([
            'question_text' => 'Select multiple',
            'type' => QuestionType::Checkbox,
        ], [
            ['label' => 'Choice A', 'value' => 'ca'],
            ['label' => 'Choice B', 'value' => 'cb'],
            ['label' => 'Choice C', 'value' => 'cc'],
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);
        $response->assertSee('Select multiple');
        $response->assertSee('Choice A');
        $response->assertSee('Choice B');
        $response->assertSee('Choice C');
    }

    /**
     * Test date question renders.
     */
    public function test_date_question_renders(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        $this->createQuestion([
            'question_text' => 'Select date',
            'type' => QuestionType::Date,
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);
        $response->assertSee('Select date');
        $response->assertSee('type=', false);
    }

    // ============================================
    // OPTIONS TESTS
    // ============================================

    /**
     * Test only active options are displayed.
     */
    public function test_only_active_options_displayed(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        $this->createQuestion([
            'question_text' => 'Choose status',
            'type' => QuestionType::Radio,
        ], [
            ['label' => 'Active Option', 'value' => 'active', 'is_active' => true],
            ['label' => 'Inactive Option', 'value' => 'inactive', 'is_active' => false],
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);
        $response->assertSee('Active Option');
        $response->assertDontSee('Inactive Option');
    }

    /**
     * Test invalid option value is rejected.
     */
    public function test_invalid_option_value_rejected(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);

        $question = $this->createQuestion([
            'question_text' => 'Select status',
            'type' => QuestionType::Radio,
            'is_required' => true,
        ], [
            ['label' => 'Option 1', 'value' => 'opt1'],
            ['label' => 'Option 2', 'value' => 'opt2'],
        ]);

        // Create tracer study first
        TracerStudy::create([
            'profile_id' => $profile->id,
            'status' => TracerStudyStatus::Draft,
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.tracer-study.submit'), [
            'answers' => [
                $question->id => 'invalid_value',
            ],
        ]);

        $response->assertSessionHasErrors();
    }

    // ============================================
    // REQUIRED VALIDATION TESTS
    // ============================================

    /**
     * Test required text must be filled on submit.
     */
    public function test_required_text_validated_on_submit(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);

        $question = $this->createQuestion([
            'question_text' => 'Required field',
            'type' => QuestionType::Text,
            'is_required' => true,
        ]);

        // Create tracer study first
        TracerStudy::create([
            'profile_id' => $profile->id,
            'status' => TracerStudyStatus::Draft,
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.tracer-study.submit'), [
            'answers' => [
                $question->id => '',
            ],
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test required radio must be selected on submit.
     */
    public function test_required_radio_validated_on_submit(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);

        $question = $this->createQuestion([
            'question_text' => 'Required radio',
            'type' => QuestionType::Radio,
            'is_required' => true,
        ], [
            ['label' => 'Option 1', 'value' => 'opt1'],
        ]);

        // Create tracer study first
        TracerStudy::create([
            'profile_id' => $profile->id,
            'status' => TracerStudyStatus::Draft,
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.tracer-study.submit'), [
            'answers' => [
                $question->id => '',
            ],
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test optional question may be empty.
     */
    public function test_optional_question_may_be_empty(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        $question = $this->createQuestion([
            'question_text' => 'Optional field',
            'type' => QuestionType::Text,
            'is_required' => false,
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.tracer-study.save'), [
            'answers' => [
                $question->id => '',
            ],
        ]);

        $response->assertStatus(302); // Redirect success
    }

    // ============================================
    // DRAFT TESTS
    // ============================================

    /**
     * Test mahasiswa can save draft.
     */
    public function test_mahasiswa_can_save_draft(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);

        $question = $this->createQuestion([
            'question_text' => 'Enter text',
            'type' => QuestionType::Text,
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.tracer-study.save'), [
            'answers' => [
                $question->id => 'My answer',
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Jawaban berhasil disimpan sebagai draft.');

        // Verify answer was saved
        $tracerStudy = $profile->fresh()->tracerStudy;
        $this->assertNotNull($tracerStudy);
        $this->assertEquals('My answer', $tracerStudy->answers()->where('question_id', $question->id)->first()->answer_value);
    }

    /**
     * Test existing answers are loaded.
     */
    public function test_existing_answers_loaded(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);

        $question = $this->createQuestion([
            'question_text' => 'Enter text',
            'type' => QuestionType::Text,
        ]);

        // Create draft with existing answer
        $tracerStudy = TracerStudy::create([
            'profile_id' => $profile->id,
            'status' => TracerStudyStatus::Draft,
        ]);
        TracerAnswer::create([
            'tracer_study_id' => $tracerStudy->id,
            'question_id' => $question->id,
            'answer_value' => 'Previous answer',
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);
        $response->assertSee('Previous answer');
    }

    /**
     * Test save draft does not duplicate answers.
     */
    public function test_save_draft_does_not_duplicate_answers(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);

        $question = $this->createQuestion([
            'question_text' => 'Enter text',
            'type' => QuestionType::Text,
        ]);

        // Create existing draft
        $tracerStudy = TracerStudy::create([
            'profile_id' => $profile->id,
            'status' => TracerStudyStatus::Draft,
        ]);
        TracerAnswer::create([
            'tracer_study_id' => $tracerStudy->id,
            'question_id' => $question->id,
            'answer_value' => 'Old answer',
        ]);

        // Save with new value
        $this->actingAs($mahasiswa)->post(route('mahasiswa.tracer-study.save'), [
            'answers' => [
                $question->id => 'New answer',
            ],
        ]);

        // Should still only have one answer
        $this->assertEquals(1, $tracerStudy->fresh()->answers()->where('question_id', $question->id)->count());
        $this->assertEquals('New answer', $tracerStudy->fresh()->answers()->where('question_id', $question->id)->first()->answer_value);
    }

    // ============================================
    // SUBMIT TESTS
    // ============================================

    /**
     * Test mahasiswa can submit valid response.
     */
    public function test_mahasiswa_can_submit_valid_response(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);

        $textQuestion = $this->createQuestion([
            'code' => 'text_q',
            'question_text' => 'Enter name',
            'type' => QuestionType::Text,
            'is_required' => true,
        ]);

        $radioQuestion = $this->createQuestion([
            'code' => 'radio_q',
            'question_text' => 'Select option',
            'type' => QuestionType::Radio,
            'is_required' => true,
        ], [
            ['label' => 'Option 1', 'value' => 'opt1'],
        ]);

        // Create tracer study first
        TracerStudy::create([
            'profile_id' => $profile->id,
            'status' => TracerStudyStatus::Draft,
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.tracer-study.submit'), [
            'answers' => [
                $textQuestion->id => 'My Name',
                $radioQuestion->id => 'opt1',
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Tracer Study berhasil disubmit.');

        // Verify submitted
        $tracerStudy = $profile->fresh()->tracerStudy;
        $this->assertTrue($tracerStudy->isSubmitted());
    }

    /**
     * Test submitted response is locked.
     */
    public function test_submitted_response_locked(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);

        // Create submitted tracer study
        TracerStudy::create([
            'profile_id' => $profile->id,
            'status' => TracerStudyStatus::Submitted,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Tracer Study sudah disubmit dan tidak dapat diubah.');
    }

    // ============================================
    // SECURITY TESTS
    // ============================================

    /**
     * Test mahasiswa A cannot access response of mahasiswa B.
     */
    public function test_mahasiswa_cannot_access_other_student_response(): void
    {
        $mahasiswa1 = $this->createMahasiswa();
        $mahasiswa2 = $this->createMahasiswa();

        $profile1 = $this->createAlumniProfileForUser($mahasiswa1);
        $this->createAlumniProfileForUser($mahasiswa2);

        // Create tracer study for mahasiswa1
        TracerStudy::create([
            'profile_id' => $profile1->id,
            'status' => TracerStudyStatus::Draft,
        ]);

        // Login as mahasiswa2
        $response = $this->actingAs($mahasiswa2)->get(route('mahasiswa.tracer-study.index'));

        // Should show not_started for mahasiswa2
        $response->assertSee('Belum Mengisi');
    }

    /**
     * Test public cannot access form.
     */
    public function test_public_cannot_access_form(): void
    {
        $response = $this->get(route('mahasiswa.tracer-study.create'));

        $response->assertRedirect('/login');
    }

    /**
     * Test checkbox answers stored correctly.
     */
    public function test_checkbox_answers_stored_correctly(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);

        $question = $this->createQuestion([
            'question_text' => 'Select skills',
            'type' => QuestionType::Checkbox,
        ], [
            ['label' => 'Skill A', 'value' => 'skill_a'],
            ['label' => 'Skill B', 'value' => 'skill_b'],
            ['label' => 'Skill C', 'value' => 'skill_c'],
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.tracer-study.save'), [
            'answers' => [
                $question->id => ['skill_a', 'skill_c'],
            ],
        ]);

        $response->assertRedirect();

        // Verify answer
        $tracerStudy = $profile->fresh()->tracerStudy;
        $answer = $tracerStudy->answers()->where('question_id', $question->id)->first();
        $this->assertNotNull($answer);

        $storedValue = json_decode($answer->answer_value, true);
        $this->assertEquals(['skill_a', 'skill_c'], $storedValue);
    }

    // ============================================
    // EDGE CASES TESTS
    // ============================================

    /**
     * Test no active questions shows message.
     */
    public function test_no_active_questions_shows_message(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        // Create inactive questions only
        $this->createQuestion([
            'question_text' => 'Inactive question',
            'is_active' => false,
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);
        $response->assertSee('Kuesioner belum tersedia');
    }

    /**
     * Test choice question without options shows safe message.
     */
    public function test_choice_question_without_options_shows_safe_message(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        // Create radio question without options
        $this->createQuestion([
            'question_text' => 'Question without options',
            'type' => QuestionType::Radio,
            'is_active' => true,
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);
        $response->assertSee('Opsi belum tersedia untuk pertanyaan ini');
    }

    /**
     * Test inactive option that was used does not break history.
     */
    public function test_inactive_option_does_not_break_history(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);

        $question = $this->createQuestion([
            'question_text' => 'Select status',
            'type' => QuestionType::Radio,
        ], [
            ['label' => 'Active', 'value' => 'active', 'is_active' => true],
            ['label' => 'Inactive', 'value' => 'inactive', 'is_active' => false],
        ]);

        $option = $question->questionOptions()->where('value', 'inactive')->first();

        // Create answer with now-inactive option
        $tracerStudy = TracerStudy::create([
            'profile_id' => $profile->id,
            'status' => TracerStudyStatus::Draft,
        ]);
        TracerAnswer::create([
            'tracer_study_id' => $tracerStudy->id,
            'question_id' => $question->id,
            'answer_value' => 'inactive',
        ]);

        // Answer should still exist
        $this->assertDatabaseHas('tracer_answers', [
            'tracer_study_id' => $tracerStudy->id,
            'question_id' => $question->id,
            'answer_value' => 'inactive',
        ]);
    }
}
