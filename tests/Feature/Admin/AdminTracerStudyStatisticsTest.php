<?php

namespace Tests\Feature\Admin;

use App\Enums\AcademicStatus;
use App\Enums\Role;
use App\Enums\TracerStudyStatus;
use App\Models\Profile;
use App\Models\TracerStudy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTracerStudyStatisticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $mahasiswaUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->adminUser = User::factory()->create([
            'role' => Role::Admin,
        ]);

        // Create mahasiswa user
        $this->mahasiswaUser = User::factory()->create([
            'role' => Role::Mahasiswa,
        ]);
    }

    /**
     * Create a profile with user for lulus alumni.
     */
    protected function createAlumniProfile(int $count = 1, array $attributes = []): \Illuminate\Database\Eloquent\Collection
    {
        return Profile::factory()->count($count)->lulus()->create(array_merge([
            'user_id' => User::factory()->create(['role' => Role::Mahasiswa])->id,
        ], $attributes));
    }

    // ============================================
    // BASIC STATISTICS TESTS
    // ============================================

    public function test_admin_can_view_tracer_study_statistics(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.statistics.tracer-study');
    }

    public function test_mahasiswa_cannot_access_admin_statistics(): void
    {
        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_statistics(): void
    {
        $response = $this->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(302); // Redirect to login
    }

    // ============================================
    // TOTAL ALUMNI STATISTICS TESTS
    // ============================================

    public function test_total_alumni_count_is_correct(): void
    {
        // Create 5 alumni profiles (status = lulus)
        Profile::factory()->count(5)->lulus()->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewHas('totalAlumni', 5);
    }

    public function test_total_alumni_is_zero_when_no_alumni(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewHas('totalAlumni', 0);
    }

    // ============================================
    // TRACER STUDY COUNT TESTS
    // ============================================

    public function test_total_tracer_study_count_is_correct(): void
    {
        $profiles = Profile::factory()->count(3)->lulus()->create();
        foreach ($profiles as $p) {
            TracerStudy::create([
                'profile_id' => $p->id,
                'status' => TracerStudyStatus::Draft,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewHas('totalTracerStudy', 3);
    }

    public function test_total_tracer_study_is_zero_when_no_tracer_study(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewHas('totalTracerStudy', 0);
    }

    // ============================================
    // DRAFT STATISTICS TESTS
    // ============================================

    public function test_draft_count_is_correct(): void
    {
        $profiles = Profile::factory()->count(3)->lulus()->create();
        foreach ($profiles as $p) {
            TracerStudy::create([
                'profile_id' => $p->id,
                'status' => TracerStudyStatus::Draft,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewHas('totalDraft', 3);
    }

    public function test_draft_count_is_zero_when_all_submitted(): void
    {
        $profiles = Profile::factory()->count(2)->lulus()->create();
        foreach ($profiles as $p) {
            TracerStudy::create([
                'profile_id' => $p->id,
                'status' => TracerStudyStatus::Submitted,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewHas('totalDraft', 0);
    }

    // ============================================
    // SUBMITTED STATISTICS TESTS
    // ============================================

    public function test_submitted_count_is_correct(): void
    {
        $profiles = Profile::factory()->count(4)->lulus()->create();
        foreach ($profiles as $p) {
            TracerStudy::create([
                'profile_id' => $p->id,
                'status' => TracerStudyStatus::Submitted,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewHas('totalSubmitted', 4);
    }

    public function test_submitted_count_is_zero_when_all_draft(): void
    {
        $profiles = Profile::factory()->count(2)->lulus()->create();
        foreach ($profiles as $p) {
            TracerStudy::create([
                'profile_id' => $p->id,
                'status' => TracerStudyStatus::Draft,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewHas('totalSubmitted', 0);
    }

    // ============================================
    // BELUM MENGISI STATISTICS TESTS
    // ============================================

    public function test_belum_mengisi_count_is_correct(): void
    {
        // 5 alumni total, 2 have tracer study
        Profile::factory()->count(5)->lulus()->create();
        $profilesWithTracer = Profile::lulus()->limit(2)->get();
        foreach ($profilesWithTracer as $p) {
            TracerStudy::create([
                'profile_id' => $p->id,
                'status' => TracerStudyStatus::Draft,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewHas('alumniBelumMengisi', 3);
    }

    public function test_belum_mengisi_is_total_alumni_when_no_tracer_study(): void
    {
        Profile::factory()->count(7)->lulus()->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewHas('alumniBelumMengisi', 7);
    }

    // ============================================
    // PARTICIPATION PERCENTAGE TESTS
    // ============================================

    public function test_participation_percentage_is_calculated_correctly(): void
    {
        // 10 alumni, 5 have tracer study = 50%
        Profile::factory()->count(10)->lulus()->create();
        $profilesWithTracer = Profile::lulus()->limit(5)->get();
        foreach ($profilesWithTracer as $p) {
            TracerStudy::create([
                'profile_id' => $p->id,
                'status' => TracerStudyStatus::Draft,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewHas('persentasePartisipasi', 50.0);
    }

    public function test_participation_percentage_is_zero_when_no_tracer_study(): void
    {
        Profile::factory()->count(5)->lulus()->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewHas('persentasePartisipasi', 0.0);
    }

    public function test_participation_percentage_is_100_when_all_filled(): void
    {
        $profiles = Profile::factory()->count(3)->lulus()->create();
        foreach ($profiles as $p) {
            TracerStudy::create([
                'profile_id' => $p->id,
                'status' => TracerStudyStatus::Submitted,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewHas('persentasePartisipasi', 100.0);
    }

    // ============================================
    // SUBMITTED PERCENTAGE TESTS
    // ============================================

    public function test_submitted_percentage_is_calculated_correctly(): void
    {
        // 4 tracer studies, 1 submitted = 25%
        $profiles = Profile::factory()->count(4)->lulus()->create();
        $count = 0;
        foreach ($profiles as $p) {
            TracerStudy::create([
                'profile_id' => $p->id,
                'status' => $count < 1 ? TracerStudyStatus::Submitted : TracerStudyStatus::Draft,
            ]);
            $count++;
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);
        $response->assertViewHas('persentaseSubmitted', 25.0);
    }

    // ============================================
    // TAHUN LULUS STATISTICS TESTS
    // ============================================

    public function test_statistik_tahun_lulus_groups_correctly(): void
    {
        Profile::factory()->count(5)->lulus()->create(['tahun_lulus' => 2020]);
        Profile::factory()->count(3)->lulus()->create(['tahun_lulus' => 2021]);
        Profile::factory()->count(7)->lulus()->create(['tahun_lulus' => 2022]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);

        $statistik = $response->viewData('statistikTahunLulus');
        $this->assertCount(3, $statistik);
    }

    public function test_statistik_tahun_lulus_shows_tracer_count(): void
    {
        // 2023: 5 alumni, 2 with tracer study
        $profiles2023 = Profile::factory()->count(5)->lulus()->create(['tahun_lulus' => 2023]);
        $count = 0;
        foreach ($profiles2023 as $p) {
            if ($count < 2) {
                TracerStudy::create(['profile_id' => $p->id, 'status' => TracerStudyStatus::Draft]);
            }
            $count++;
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);

        $denganTracer = $response->viewData('statistikTahunLulusDenganTracer');
        $tracer2023 = $denganTracer->firstWhere('tahun_lulus', 2023);

        $this->assertEquals(2, $tracer2023['total_tracer']);
    }

    // ============================================
    // PROGRAM STUDI STATISTICS TESTS
    // ============================================

    public function test_statistik_program_studi_groups_correctly(): void
    {
        Profile::factory()->count(4)->lulus()->create(['program_studi' => 'Informatika']);
        Profile::factory()->count(6)->lulus()->create(['program_studi' => 'Sistem Informasi']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);

        $statistik = $response->viewData('statistikProgramStudi');
        $this->assertCount(2, $statistik);
    }

    public function test_statistik_program_studi_shows_tracer_count(): void
    {
        $profilesInformatika = Profile::factory()->count(3)->lulus()->create(['program_studi' => 'Informatika']);
        foreach ($profilesInformatika as $p) {
            TracerStudy::create(['profile_id' => $p->id, 'status' => TracerStudyStatus::Submitted]);
        }
        Profile::factory()->count(2)->lulus()->create(['program_studi' => 'Informatika']); // No tracer study

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);

        $denganTracer = $response->viewData('statistikProgramStudiDenganTracer');
        $tracerInformatika = $denganTracer->firstWhere('program_studi', 'Informatika');

        $this->assertEquals(3, $tracerInformatika['total_tracer']);
    }

    // ============================================
    // EDGE CASES TESTS
    // ============================================

    public function test_statistics_with_mixed_data(): void
    {
        // 10 alumni total
        Profile::factory()->count(5)->lulus()->create(['tahun_lulus' => 2023]);
        Profile::factory()->count(3)->lulus()->create(['tahun_lulus' => 2022]);
        Profile::factory()->count(2)->lulus()->create(['tahun_lulus' => 2021]);

        // 7 tracer studies (3 alumni belum mengisi)
        $allProfiles = Profile::lulus()->limit(7)->get();
        foreach ($allProfiles as $index => $p) {
            TracerStudy::create([
                'profile_id' => $p->id,
                'status' => $index < 4 ? TracerStudyStatus::Submitted : TracerStudyStatus::Draft,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);

        $response->assertViewHas('totalAlumni', 10);
        $response->assertViewHas('totalTracerStudy', 7);
        $response->assertViewHas('alumniBelumMengisi', 3);
        $response->assertViewHas('totalSubmitted', 4);
        $response->assertViewHas('totalDraft', 3);
        $response->assertViewHas('persentasePartisipasi', 70.0);
        $response->assertViewHas('persentaseSubmitted', 57.1); // 4/7 = 57.14...
    }

    public function test_statistics_view_has_correct_structure(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.statistics.tracer-study'));

        $response->assertStatus(200);

        // Check all required view variables exist
        $response->assertViewHas('totalAlumni');
        $response->assertViewHas('totalTracerStudy');
        $response->assertViewHas('totalDraft');
        $response->assertViewHas('totalSubmitted');
        $response->assertViewHas('alumniBelumMengisi');
        $response->assertViewHas('persentasePartisipasi');
        $response->assertViewHas('persentaseSubmitted');
        $response->assertViewHas('distribusiPekerjaan');
        $response->assertViewHas('statistikTahunLulus');
        $response->assertViewHas('statistikTahunLulusDenganTracer');
        $response->assertViewHas('statistikProgramStudi');
        $response->assertViewHas('statistikProgramStudiDenganTracer');
        $response->assertViewHas('alumniSubmitted');
        $response->assertViewHas('alumniDraft');
    }
}
