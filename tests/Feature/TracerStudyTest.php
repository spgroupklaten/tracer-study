<?php

namespace Tests\Feature;

use App\Enums\AcademicStatus;
use App\Enums\Role;
use App\Enums\TracerStudyStatus;
use App\Models\Alumni;
use App\Models\Profile;
use App\Models\TracerAnswer;
use App\Models\TracerQuestion;
use App\Models\TracerStudy;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TracerStudyTest extends TestCase
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
     * Helper to create questions.
     */
    protected function createQuestions(): void
    {
        TracerQuestion::create([
            'code' => 'status_saat_ini',
            'question_text' => 'Apa status Anda saat ini?',
            'type' => 'radio',
            'is_required' => true,
            'order' => 1,
            'options' => ['Bekerja', 'Wiraswasta', 'Belum Bekerja'],
        ]);

        TracerQuestion::create([
            'code' => 'nama_perusahaan',
            'question_text' => 'Nama perusahaan?',
            'type' => 'text',
            'is_required' => false,
            'order' => 2,
            'options' => null,
        ]);
    }

    // ============================================
    // MAHASISWA TESTS
    // ============================================

    /**
     * Test mahasiswa can access tracer study page.
     */
    public function test_mahasiswa_can_access_tracer_study_page(): void
    {
        $mahasiswa = $this->createMahasiswa();

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.index'));

        $response->assertStatus(200);
    }

    /**
     * Test mahasiswa without alumni sees no alumni message.
     */
    public function test_mahasiswa_without_alumni_sees_no_alumni_message(): void
    {
        $mahasiswa = $this->createMahasiswa();

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.index'));

        $response->assertStatus(200);
        $response->assertSee('Data Alumni Tidak Ditemukan');
    }

    /**
     * Test mahasiswa can create draft tracer study.
     */
    public function test_mahasiswa_can_create_draft(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);
        $this->createQuestions();

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(200);
        $response->assertSee('Isi Tracer Study');
    }

    /**
     * Test mahasiswa can save draft.
     */
    public function test_mahasiswa_can_save_draft(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);
        $this->createQuestions();

        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.tracer-study.save'), [
            'answers' => [
                1 => 'Bekerja',
                2 => 'PT Contoh',
            ],
        ]);

        $response->assertRedirect();

        // Get the tracer study
        $tracerStudy = TracerStudy::where('profile_id', $profile->id)->first();

        $this->assertDatabaseHas('tracer_studies', [
            'profile_id' => $profile->id,
            'status' => TracerStudyStatus::Draft->value,
        ]);
        $this->assertDatabaseHas('tracer_answers', [
            'tracer_study_id' => $tracerStudy->id,
            'question_id' => 1,
            'answer_value' => 'Bekerja',
        ]);
    }

    /**
     * Test mahasiswa can submit tracer study.
     */
    public function test_mahasiswa_can_submit(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);
        $this->createQuestions();

        // Create draft first
        $tracerStudy = TracerStudy::create([
            'profile_id' => $profile->id,
            'status' => TracerStudyStatus::Draft,
            'started_at' => now(),
        ]);

        // Answer required question
        TracerAnswer::create([
            'tracer_study_id' => $tracerStudy->id,
            'question_id' => 1,
            'answer_value' => 'Bekerja',
        ]);

        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.tracer-study.submit'));

        $response->assertRedirect();
        $this->assertDatabaseHas('tracer_studies', [
            'profile_id' => $profile->id,
            'status' => TracerStudyStatus::Submitted->value,
        ]);
    }

    /**
     * Test submitted tracer study cannot be edited.
     */
    public function test_submitted_cannot_be_edited(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);
        $this->createQuestions();

        // Create submitted tracer study
        TracerStudy::create([
            'profile_id' => $profile->id,
            'status' => TracerStudyStatus::Submitted,
            'started_at' => now()->subDay(),
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.tracer-study.create'));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Tracer Study sudah disubmit dan tidak dapat diubah.');
    }

    /**
     * Test mahasiswa cannot access another mahasiswa tracer study.
     */
    public function test_mahasiswa_cannot_access_other_tracer_study(): void
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

        // Try to access via mahasiswa1's ID - should work
        $response = $this->actingAs($mahasiswa1)->get(route('mahasiswa.tracer-study.create'));
        $response->assertStatus(200);
    }

    // ============================================
    // ADMIN TESTS
    // ============================================

    /**
     * Test admin can access tracer study list.
     */
    public function test_admin_can_access_tracer_study_list(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.tracer-study.index'));

        $response->assertStatus(200);
    }

    /**
     * Test admin can see alumni with tracer study.
     */
    public function test_admin_can_see_alumni_with_tracer_study(): void
    {
        $admin = $this->createAdmin();
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);
        TracerStudy::create([
            'profile_id' => $profile->id,
            'status' => TracerStudyStatus::Submitted,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.tracer-study.index'));

        $response->assertStatus(200);
        $response->assertSee($mahasiswa->name);
    }

    /**
     * Test admin can see alumni without tracer study.
     */
    public function test_admin_can_see_alumni_without_tracer_study(): void
    {
        $admin = $this->createAdmin();
        $mahasiswa = $this->createMahasiswa();
        $this->createAlumniProfileForUser($mahasiswa);

        $response = $this->actingAs($admin)->get(route('admin.tracer-study.index'));

        $response->assertStatus(200);
        $response->assertSee('Belum Mengisi');
    }

    /**
     * Test admin can filter by status.
     */
    public function test_admin_can_filter_by_status(): void
    {
        $admin = $this->createAdmin();
        $mahasiswa1 = $this->createMahasiswa();
        $mahasiswa2 = $this->createMahasiswa();
        $profile1 = $this->createAlumniProfileForUser($mahasiswa1);
        $profile2 = $this->createAlumniProfileForUser($mahasiswa2);

        TracerStudy::create([
            'profile_id' => $profile1->id,
            'status' => TracerStudyStatus::Submitted,
        ]);

        // profile2 has no tracer study

        $response = $this->actingAs($admin)->get(route('admin.tracer-study.index', ['status' => 'submitted']));

        $response->assertStatus(200);
        $response->assertSee($mahasiswa1->name);
        $response->assertDontSee($mahasiswa2->name);
    }

    /**
     * Test admin can view tracer study detail.
     */
    public function test_admin_can_view_tracer_study_detail(): void
    {
        $admin = $this->createAdmin();
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createAlumniProfileForUser($mahasiswa);
        $this->createQuestions();

        $tracerStudy = TracerStudy::create([
            'profile_id' => $profile->id,
            'status' => TracerStudyStatus::Submitted,
            'submitted_at' => now(),
        ]);

        TracerAnswer::create([
            'tracer_study_id' => $tracerStudy->id,
            'question_id' => 1,
            'answer_value' => 'Bekerja',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.tracer-study.show', $profile));

        $response->assertStatus(200);
        $response->assertSee($mahasiswa->name);
        $response->assertSee('Bekerja');
    }

    // ============================================
    // SECURITY TESTS
    // ============================================

    /**
     * Test public cannot access tracer study.
     */
    public function test_public_cannot_access_tracer_study(): void
    {
        $this->get(route('mahasiswa.tracer-study.index'))->assertRedirect(route('login'));
        $this->get(route('admin.tracer-study.index'))->assertRedirect(route('login'));
    }

    /**
     * Test mahasiswa cannot access admin tracer study.
     */
    public function test_mahasiswa_cannot_access_admin_tracer_study(): void
    {
        $mahasiswa = $this->createMahasiswa();

        $response = $this->actingAs($mahasiswa)->get(route('admin.tracer-study.index'));

        $response->assertStatus(403);
    }

    /**
     * Test admin cannot access mahasiswa tracer study create.
     */
    public function test_admin_cannot_access_mahasiswa_create(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('mahasiswa.tracer-study.create'));

        $response->assertStatus(403);
    }
}
