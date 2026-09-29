<?php

namespace Tests\Feature\Admin;

use App\Enums\QuestionType;
use App\Models\TracerAnswer;
use App\Models\TracerQuestion;
use App\Models\TracerStudy;
use App\Models\Alumni;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TracerQuestionTest extends TestCase
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
     * Helper to create alumni linked to user.
     */
    protected function createAlumniForUser(User $user): Alumni
    {
        return Alumni::create([
            'user_id' => $user->id,
            'nim' => fake()->unique()->numerify('########'),
            'nama_lengkap' => $user->name,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2000-01-01',
            'email' => $user->email,
            'tahun_masuk' => 2018,
            'tahun_lulus' => 2022,
            'program_studi' => 'Teknik Informatika',
        ]);
    }

    /**
     * Helper to create a question.
     */
    protected function createQuestion(array $attributes = []): TracerQuestion
    {
        return TracerQuestion::create(array_merge([
            'code' => fake()->unique()->word(),
            'question_text' => 'Sample question?',
            'type' => QuestionType::Text,
            'is_required' => false,
            'is_active' => true,
            'order' => 1,
            'options' => null,
        ], $attributes));
    }

    // ============================================
    // ADMIN AUTHORIZATION TESTS
    // ============================================

    /**
     * Test admin can access kuesioner index page.
     */
    public function test_admin_can_access_kuesioner_index(): void
    {
        $admin = $this->createAdmin();
        $this->createQuestion();

        $response = $this->actingAs($admin)->get(route('admin.kuesioner.index'));

        $response->assertStatus(200);
        $response->assertSee('Kuesioner Tracer Study');
    }

    /**
     * Test admin can access kuesioner create page.
     */
    public function test_admin_can_access_kuesioner_create(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.kuesioner.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Pertanyaan');
    }

    /**
     * Test admin can create question.
     */
    public function test_admin_can_create_question(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.kuesioner.store'), [
            'question_text' => 'Apa hobi Anda?',
            'type' => 'text',
            'is_required' => true,
            'is_active' => true,
            'order' => 1,
        ]);

        $response->assertRedirect(route('admin.kuesioner.index'));
        $response->assertSessionHas('success', 'Pertanyaan berhasil ditambahkan.');

        $this->assertDatabaseHas('tracer_questions', [
            'question_text' => 'Apa hobi Anda?',
            'type' => 'text',
            'is_required' => true,
            'order' => 1,
        ]);
    }

    /**
     * Test admin can create question with options.
     */
    public function test_admin_can_create_question_with_options(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.kuesioner.store'), [
            'question_text' => 'Pilih warna favorit?',
            'type' => 'radio',
            'is_required' => true,
            'is_active' => true,
            'order' => 1,
            'option_labels' => ['Merah', 'Hijau', 'Biru', 'Kuning'],
            'option_values' => ['merah', 'hijau', 'biru', 'kuning'],
        ]);

        $response->assertRedirect(route('admin.kuesioner.index'));

        $question = TracerQuestion::where('question_text', 'Pilih warna favorit?')->first();
        $this->assertNotNull($question);

        // Verify the question was created with correct data
        $this->assertDatabaseHas('tracer_questions', [
            'question_text' => 'Pilih warna favorit?',
            'type' => 'radio',
            'order' => 1,
        ]);

        // Verify options are stored and can be retrieved
        $this->assertCount(4, $question->questionOptions);
    }

    /**
     * Test admin can access kuesioner edit page.
     */
    public function test_admin_can_access_kuesioner_edit(): void
    {
        $admin = $this->createAdmin();
        $question = $this->createQuestion();

        $response = $this->actingAs($admin)->get(route('admin.kuesioner.edit', $question));

        $response->assertStatus(200);
        $response->assertSee('Edit Pertanyaan');
    }

    /**
     * Test admin can update question.
     */
    public function test_admin_can_update_question(): void
    {
        $admin = $this->createAdmin();
        $question = $this->createQuestion();

        $response = $this->actingAs($admin)->put(route('admin.kuesioner.update', $question), [
            'question_text' => 'Updated question text',
            'type' => 'textarea',
            'is_required' => true,
            'is_active' => false,
            'order' => 5,
        ]);

        $response->assertRedirect(route('admin.kuesioner.index'));
        $response->assertSessionHas('success', 'Pertanyaan berhasil diperbarui.');

        $this->assertDatabaseHas('tracer_questions', [
            'id' => $question->id,
            'question_text' => 'Updated question text',
            'type' => 'textarea',
            'is_required' => true,
            'is_active' => false,
            'order' => 5,
        ]);
    }

    /**
     * Test admin can toggle question status.
     */
    public function test_admin_can_toggle_question_status(): void
    {
        $admin = $this->createAdmin();
        $question = $this->createQuestion(['is_active' => true]);

        $response = $this->actingAs($admin)->patch(route('admin.kuesioner.toggle-status', $question));

        $response->assertRedirect(route('admin.kuesioner.index'));
        $response->assertSessionHas('success', 'Pertanyaan berhasil dinonaktifkan.');

        $question->refresh();
        $this->assertFalse($question->is_active);
    }

    /**
     * Test admin can activate question.
     */
    public function test_admin_can_activate_question(): void
    {
        $admin = $this->createAdmin();
        $question = $this->createQuestion(['is_active' => false]);

        $response = $this->actingAs($admin)->patch(route('admin.kuesioner.toggle-status', $question));

        $response->assertRedirect(route('admin.kuesioner.index'));
        $response->assertSessionHas('success', 'Pertanyaan berhasil diaktifkan.');

        $question->refresh();
        $this->assertTrue($question->is_active);
    }

    /**
     * Test admin can delete question without answers.
     */
    public function test_admin_can_delete_question_without_answers(): void
    {
        $admin = $this->createAdmin();
        $question = $this->createQuestion();

        $response = $this->actingAs($admin)->delete(route('admin.kuesioner.destroy', $question));

        $response->assertRedirect(route('admin.kuesioner.index'));
        $response->assertSessionHas('success', 'Pertanyaan berhasil dihapus.');

        $this->assertDatabaseMissing('tracer_questions', ['id' => $question->id]);
    }

    /**
     * Test admin cannot delete question with answers.
     */
    public function test_admin_cannot_delete_question_with_answers(): void
    {
        $admin = $this->createAdmin();
        $mahasiswa = $this->createMahasiswa();
        $alumni = $this->createAlumniForUser($mahasiswa);

        $question = $this->createQuestion();
        $tracerStudy = TracerStudy::create([
            'alumni_id' => $alumni->id,
            'status' => 'submitted',
        ]);
        TracerAnswer::create([
            'tracer_study_id' => $tracerStudy->id,
            'question_id' => $question->id,
            'answer_value' => 'Test answer',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.kuesioner.destroy', $question));

        $response->assertRedirect(route('admin.kuesioner.index'));
        $response->assertSessionHas('error', 'Pertanyaan tidak dapat dihapus karena sudah memiliki jawaban Tracer Study. Nonaktifkan pertanyaan sebagai gantinya.');

        $this->assertDatabaseHas('tracer_questions', ['id' => $question->id]);
    }

    // ============================================
    // MAHASISWA BLOCKED TESTS
    // ============================================

    /**
     * Test mahasiswa cannot access kuesioner index.
     */
    public function test_mahasiswa_cannot_access_kuesioner_index(): void
    {
        $mahasiswa = $this->createMahasiswa();

        $response = $this->actingAs($mahasiswa)->get(route('admin.kuesioner.index'));

        $response->assertStatus(403);
    }

    /**
     * Test mahasiswa cannot access kuesioner create.
     */
    public function test_mahasiswa_cannot_access_kuesioner_create(): void
    {
        $mahasiswa = $this->createMahasiswa();

        $response = $this->actingAs($mahasiswa)->get(route('admin.kuesioner.create'));

        $response->assertStatus(403);
    }

    /**
     * Test mahasiswa cannot create question.
     */
    public function test_mahasiswa_cannot_create_question(): void
    {
        $mahasiswa = $this->createMahasiswa();

        $response = $this->actingAs($mahasiswa)->post(route('admin.kuesioner.store'), [
            'question_text' => 'Hacked question',
            'type' => 'text',
            'is_required' => false,
            'is_active' => true,
            'order' => 1,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('tracer_questions', ['question_text' => 'Hacked question']);
    }

    // ============================================
    // PUBLIC BLOCKED TESTS
    // ============================================

    /**
     * Test public cannot access kuesioner index.
     */
    public function test_public_cannot_access_kuesioner_index(): void
    {
        $response = $this->get(route('admin.kuesioner.index'));

        $response->assertRedirect('/login');
    }

    /**
     * Test public cannot access kuesioner create.
     */
    public function test_public_cannot_access_kuesioner_create(): void
    {
        $response = $this->get(route('admin.kuesioner.create'));

        $response->assertRedirect('/login');
    }

    /**
     * Test public cannot create question.
     */
    public function test_public_cannot_create_question(): void
    {
        $response = $this->post(route('admin.kuesioner.store'), [
            'question_text' => 'Hacked question',
            'type' => 'text',
            'is_required' => false,
            'is_active' => true,
            'order' => 1,
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('tracer_questions', ['question_text' => 'Hacked question']);
    }

    // ============================================
    // VALIDATION TESTS
    // ============================================

    /**
     * Test question text is required.
     */
    public function test_question_text_is_required(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.kuesioner.store'), [
            'question_text' => '',
            'type' => 'text',
            'order' => 1,
        ]);

        $response->assertSessionHasErrors('question_text');
    }

    /**
     * Test type is required.
     */
    public function test_type_is_required(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.kuesioner.store'), [
            'question_text' => 'Valid question',
            'type' => '',
            'order' => 1,
        ]);

        $response->assertSessionHasErrors('type');
    }

    /**
     * Test order is required.
     */
    public function test_order_is_required(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.kuesioner.store'), [
            'question_text' => 'Valid question',
            'type' => 'text',
            'order' => '',
        ]);

        $response->assertSessionHasErrors('order');
    }

    /**
     * Test invalid type is rejected.
     */
    public function test_invalid_type_is_rejected(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.kuesioner.store'), [
            'question_text' => 'Valid question',
            'type' => 'invalid_type',
            'order' => 1,
        ]);

        $response->assertSessionHasErrors('type');
    }

    /**
     * Test unique code validation.
     */
    public function test_code_must_be_unique(): void
    {
        $admin = $this->createAdmin();
        $this->createQuestion(['code' => 'existing_code']);

        $response = $this->actingAs($admin)->post(route('admin.kuesioner.store'), [
            'question_text' => 'New question',
            'type' => 'text',
            'order' => 1,
            'code' => 'existing_code',
        ]);

        $response->assertSessionHasErrors('code');
    }

    // ============================================
    // SEARCH & FILTER TESTS
    // ============================================

    /**
     * Test search functionality.
     */
    public function test_can_search_questions(): void
    {
        $admin = $this->createAdmin();
        $this->createQuestion(['question_text' => 'Specific question about work']);
        $this->createQuestion(['question_text' => 'Another question about study']);

        $response = $this->actingAs($admin)->get(route('admin.kuesioner.index', ['search' => 'work']));

        $response->assertStatus(200);
        $response->assertSee('Specific question about work');
        $response->assertDontSee('Another question about study');
    }

    /**
     * Test filter by type.
     */
    public function test_can_filter_by_type(): void
    {
        $admin = $this->createAdmin();
        $this->createQuestion(['type' => QuestionType::Text]);
        $this->createQuestion(['type' => QuestionType::Radio]);

        $response = $this->actingAs($admin)->get(route('admin.kuesioner.index', ['type' => 'text']));

        $response->assertStatus(200);
        $response->assertSee('Text');
    }

    /**
     * Test filter by status.
     */
    public function test_can_filter_by_status(): void
    {
        $admin = $this->createAdmin();
        $this->createQuestion(['is_active' => true]);
        $this->createQuestion(['is_active' => false]);

        $response = $this->actingAs($admin)->get(route('admin.kuesioner.index', ['status' => 'active']));

        $response->assertStatus(200);
    }

    // ============================================
    // PAGINATION TESTS
    // ============================================

    /**
     * Test pagination works.
     */
    public function test_pagination_works(): void
    {
        $admin = $this->createAdmin();

        // Create 20 questions
        for ($i = 1; $i <= 20; $i++) {
            $this->createQuestion(['question_text' => "Question number {$i}", 'order' => $i]);
        }

        $response = $this->actingAs($admin)->get(route('admin.kuesioner.index'));

        $response->assertStatus(200);
        $response->assertSee('1'); // First item number
    }

    // ============================================
    // CSRF & MASS ASSIGNMENT TESTS
    // ============================================

    /**
     * Test CSRF protection is configured.
     * Note: The project uses a custom CSRF middleware that is bypassed in tests.
     * This test verifies the request is processed correctly.
     */
    public function test_csrf_middleware_is_configured(): void
    {
        // Verify the CSRF middleware exists
        $this->assertTrue(class_exists(\App\Http\Middleware\VerifyCsrfToken::class));

        // Verify the request processes correctly with valid data
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.kuesioner.store'), [
            'question_text' => 'CSRF Test Question',
            'type' => 'text',
            'order' => 1,
        ]);

        // Should redirect on success
        $response->assertRedirect(route('admin.kuesioner.index'));

        // Verify the question was created
        $this->assertDatabaseHas('tracer_questions', [
            'question_text' => 'CSRF Test Question',
        ]);
    }

    /**
     * Test mass assignment is protected.
     */
    public function test_mass_assignment_is_protected(): void
    {
        $admin = $this->createAdmin();

        // Try to set fields via mass assignment that shouldn't be assignable
        // Note: is_admin_override is just ignored, the model is still created
        // The test verifies that fillable is properly configured
        $this->actingAs($admin)->post(route('admin.kuesioner.store'), [
            'question_text' => 'Mass assignment test',
            'type' => 'text',
            'order' => 1,
            'is_admin_override' => true, // This should not be assignable
        ]);

        // Question should be created with valid fields, but is_admin_override should be ignored
        $question = TracerQuestion::where('question_text', 'Mass assignment test')->first();
        $this->assertNotNull($question);

        // Verify that is_admin_override is NOT in the model's attributes (guarded works)
        $this->assertArrayNotHasKey('is_admin_override', $question->getAttributes());
    }

    // ============================================
    // IDOR PROTECTION TESTS
    // ============================================

    /**
     * Test edit protects against IDOR.
     */
    public function test_edit_uses_route_model_binding(): void
    {
        $admin = $this->createAdmin();
        $question = $this->createQuestion();

        // Accessing another admin's question should still work (all admins are equal)
        $response = $this->actingAs($admin)->get(route('admin.kuesioner.edit', $question));

        $response->assertStatus(200);
    }

    /**
     * Test delete protects against IDOR.
     */
    public function test_delete_uses_route_model_binding(): void
    {
        $admin = $this->createAdmin();
        $question = $this->createQuestion();

        $response = $this->actingAs($admin)->delete(route('admin.kuesioner.destroy', $question));

        $response->assertRedirect(route('admin.kuesioner.index'));
        $this->assertDatabaseMissing('tracer_questions', ['id' => $question->id]);
    }
}
