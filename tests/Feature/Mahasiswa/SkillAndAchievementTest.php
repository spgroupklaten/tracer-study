<?php

namespace Tests\Feature\Mahasiswa;

use App\Enums\AchievementLevel;
use App\Enums\AchievementType;
use App\Enums\Role;
use App\Enums\SkillLevel;
use App\Models\Achievement;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SkillAndAchievementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected User $mahasiswaUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->mahasiswaUser = User::factory()->create(['role' => Role::Mahasiswa]);
        $this->adminUser = User::factory()->create(['role' => Role::Admin]);
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /**
     * Helper to create profile.
     */
    protected function createProfile(User $user, array $attributes = []): Profile
    {
        return Profile::create(array_merge([
            'user_id' => $user->id,
            'nik' => fake()->unique()->numerify('################'),
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'L',
            'nim' => fake()->unique()->numerify('########'),
            'fakultas' => 'Fakultas Teknik',
            'program_studi' => 'Teknik Informatika',
            'angkatan' => 2022,
            'tahun_masuk' => 2022,
            'academic_status' => \App\Enums\AcademicStatus::Aktif,
        ], $attributes));
    }

    // ============================================
    // SKILL TESTS
    // ============================================

    /**
     * Test guest cannot access skill routes.
     */
    public function test_guest_cannot_access_skill_routes(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);

        // Guest cannot add skill
        $response = $this->post(route('mahasiswa.profile.skills.store'), [
            'skill_id' => 1,
            'level' => 'pemula',
        ]);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test mahasiswa can view skill section.
     */
    public function test_mahasiswa_can_view_skill_section(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.profile.index'));

        $response->assertStatus(200);
        $response->assertSee('Keahlian');
    }

    /**
     * Test mahasiswa can add skill.
     */
    public function test_mahasiswa_can_add_skill(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $skill = Skill::create(['name' => 'Laravel', 'slug' => 'laravel']);

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.skills.store'), [
                'skill_id' => $skill->id,
                'level' => SkillLevel::Advanced->value,
                'description' => 'Pengembangan web Laravel',
            ]);

        $response->assertRedirect(route('mahasiswa.profile.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('profile_skills', [
            'profile_id' => $profile->id,
            'skill_id' => $skill->id,
            'level' => 'mahir',
        ]);
    }

    /**
     * Test mahasiswa can update skill.
     */
    public function test_mahasiswa_can_update_skill(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $skill = Skill::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $profile->skills()->attach($skill->id, ['level' => 'pemula', 'description' => null]);

        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.skills.update', $skill->id), [
                'level' => SkillLevel::Advanced->value,
                'description' => 'Updated description',
            ]);

        $response->assertRedirect(route('mahasiswa.profile.index'));
        $response->assertSessionHas('success');

        $profile->refresh();
        $pivot = $profile->skills()->where('skill_id', $skill->id)->first()->pivot;
        $this->assertEquals('mahir', $pivot->level);
        $this->assertEquals('Updated description', $pivot->description);
    }

    /**
     * Test mahasiswa can delete skill.
     */
    public function test_mahasiswa_can_delete_skill(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $skill = Skill::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $profile->skills()->attach($skill->id, ['level' => 'pemula']);

        $response = $this->actingAs($this->mahasiswaUser)
            ->delete(route('mahasiswa.profile.skills.destroy', $skill->id));

        $response->assertRedirect(route('mahasiswa.profile.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('profile_skills', [
            'profile_id' => $profile->id,
            'skill_id' => $skill->id,
        ]);
    }

    /**
     * Test profile can have multiple skills.
     */
    public function test_profile_can_have_multiple_skills(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $skill1 = Skill::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $skill2 = Skill::create(['name' => 'PHP', 'slug' => 'php']);

        $profile->skills()->attach($skill1->id, ['level' => 'mahir']);
        $profile->skills()->attach($skill2->id, ['level' => 'mahir']);

        $this->assertEquals(2, $profile->skills()->count());
    }

    /**
     * Test duplicate skill is rejected.
     */
    public function test_duplicate_skill_is_rejected(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $skill = Skill::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $profile->skills()->attach($skill->id, ['level' => 'pemula']);

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.skills.store'), [
                'skill_id' => $skill->id,
                'level' => 'mahir',
            ]);

        $response->assertSessionHasErrors('skill_id');
    }

    /**
     * Test skill level must be valid.
     */
    public function test_skill_level_must_be_valid(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $skill = Skill::create(['name' => 'Laravel', 'slug' => 'laravel']);

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.skills.store'), [
                'skill_id' => $skill->id,
                'level' => 'invalid_level',
            ]);

        $response->assertSessionHasErrors('level');
    }

    /**
     * Test user A cannot update user B's skill.
     */
    public function test_user_cannot_update_other_user_skill(): void
    {
        $userA = $this->mahasiswaUser;
        $userB = User::factory()->create(['role' => Role::Mahasiswa]);

        $profileA = $this->createProfile($userA);
        $profileB = $this->createProfile($userB);

        $skill = Skill::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $profileB->skills()->attach($skill->id, ['level' => 'pemula']);

        // User A tries to update User B's skill
        $response = $this->actingAs($userA)
            ->patch(route('mahasiswa.profile.skills.update', $skill->id), [
                'level' => 'ahli',
            ]);

        // Should return 404
        $response->assertStatus(404);
    }

    /**
     * Test user A cannot delete user B's skill.
     */
    public function test_user_cannot_delete_other_user_skill(): void
    {
        $userA = $this->mahasiswaUser;
        $userB = User::factory()->create(['role' => Role::Mahasiswa]);

        $profileA = $this->createProfile($userA);
        $profileB = $this->createProfile($userB);

        $skill = Skill::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $profileB->skills()->attach($skill->id, ['level' => 'pemula']);

        // User A tries to delete User B's skill
        $response = $this->actingAs($userA)
            ->delete(route('mahasiswa.profile.skills.destroy', $skill->id));

        // Should return 404
        $response->assertStatus(404);

        // Skill should still exist
        $this->assertDatabaseHas('profile_skills', [
            'profile_id' => $profileB->id,
            'skill_id' => $skill->id,
        ]);
    }

    /**
     * Test skill not found is rejected.
     */
    public function test_nonexistent_skill_is_rejected(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.skills.store'), [
                'skill_id' => 9999,
                'level' => 'pemula',
            ]);

        $response->assertSessionHasErrors('skill_id');
    }

    // ============================================
    // ACHIEVEMENT TESTS
    // ============================================

    /**
     * Test guest cannot access achievement routes.
     */
    public function test_guest_cannot_access_achievement_routes(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->post(route('mahasiswa.profile.achievements.store'), [
            'judul' => 'Test',
            'jenis' => 'akademik',
            'tingkat' => 'nasional',
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test mahasiswa can view achievement section.
     */
    public function test_mahasiswa_can_view_achievement_section(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.profile.index'));

        $response->assertStatus(200);
        $response->assertSee('Prestasi');
    }

    /**
     * Test mahasiswa can create achievement.
     */
    public function test_mahasiswa_can_create_achievement(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.achievements.store'), [
                'judul' => 'Juara 1 Programming Competition',
                'jenis' => AchievementType::NonAcademic->value,
                'tingkat' => AchievementLevel::National->value,
                'penyelenggara' => 'Universitas ABC',
                'tahun' => 2024,
                'deskripsi' => 'Juara 1 kompetisi programming tingkat nasional',
            ]);

        $response->assertRedirect(route('mahasiswa.profile.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('achievements', [
            'profile_id' => $profile->id,
            'judul' => 'Juara 1 Programming Competition',
            'jenis' => 'non_akademik',
            'tingkat' => 'nasional',
        ]);
    }

    /**
     * Test achievement requires judul.
     */
    public function test_achievement_requires_judul(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.achievements.store'), [
                'jenis' => 'akademik',
                'tingkat' => 'nasional',
            ]);

        $response->assertSessionHasErrors('judul');
    }

    /**
     * Test achievement requires jenis.
     */
    public function test_achievement_requires_jenis(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.achievements.store'), [
                'judul' => 'Test Achievement',
                'tingkat' => 'nasional',
            ]);

        $response->assertSessionHasErrors('jenis');
    }

    /**
     * Test achievement requires tingkat.
     */
    public function test_achievement_requires_tingkat(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.achievements.store'), [
                'judul' => 'Test Achievement',
                'jenis' => 'akademik',
            ]);

        $response->assertSessionHasErrors('tingkat');
    }

    /**
     * Test achievement with certificate upload.
     */
    public function test_achievement_can_have_certificate(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);

        $file = UploadedFile::fake()->create('certificate.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.achievements.store'), [
                'judul' => 'Juara 1 Programming',
                'jenis' => 'akademik',
                'tingkat' => 'nasional',
                'tahun' => 2024,
                'certificate' => $file,
            ]);

        $response->assertRedirect(route('mahasiswa.profile.index'));

        $achievement = $profile->achievements()->first();
        $this->assertNotNull($achievement->certificate_path);
    }

    /**
     * Test invalid certificate extension is rejected.
     */
    public function test_invalid_certificate_extension_rejected(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $file = UploadedFile::fake()->create('script.exe', 1024, 'application/octet-stream');

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.achievements.store'), [
                'judul' => 'Test',
                'jenis' => 'akademik',
                'tingkat' => 'nasional',
                'certificate' => $file,
            ]);

        $response->assertSessionHasErrors('certificate');
    }

    /**
     * Test certificate size limit.
     */
    public function test_certificate_size_limit(): void
    {
        $this->createProfile($this->mahasiswaUser);

        // Create file larger than 5MB
        $file = UploadedFile::fake()->create('large.pdf', 6000, 'application/pdf');

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.achievements.store'), [
                'judul' => 'Test',
                'jenis' => 'akademik',
                'tingkat' => 'nasional',
                'certificate' => $file,
            ]);

        $response->assertSessionHasErrors('certificate');
    }

    /**
     * Test mahasiswa can update achievement.
     */
    public function test_mahasiswa_can_update_achievement(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $achievement = Achievement::create([
            'profile_id' => $profile->id,
            'judul' => 'Original Title',
            'jenis' => 'akademik',
            'tingkat' => 'nasional',
            'tahun' => 2024,
        ]);

        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.achievements.update', $achievement->id), [
                'judul' => 'Updated Title',
                'jenis' => 'non_akademik',
                'tingkat' => 'provinsi',
                'tahun' => 2023,
            ]);

        $response->assertRedirect(route('mahasiswa.profile.index'));
        $response->assertSessionHas('success');

        $achievement->refresh();
        $this->assertEquals('Updated Title', $achievement->judul);
        $this->assertEquals('non_akademik', $achievement->jenis->value);
    }

    /**
     * Test mahasiswa can delete achievement.
     */
    public function test_mahasiswa_can_delete_achievement(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $achievement = Achievement::create([
            'profile_id' => $profile->id,
            'judul' => 'Test Achievement',
            'jenis' => 'akademik',
            'tingkat' => 'nasional',
        ]);

        $response = $this->actingAs($this->mahasiswaUser)
            ->delete(route('mahasiswa.profile.achievements.destroy', $achievement->id));

        $response->assertRedirect(route('mahasiswa.profile.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('achievements', ['id' => $achievement->id]);
    }

    /**
     * Test user A cannot view user B's achievement.
     */
    public function test_user_cannot_view_other_user_achievement(): void
    {
        $userA = $this->mahasiswaUser;
        $userB = User::factory()->create(['role' => Role::Mahasiswa]);

        $profileA = $this->createProfile($userA);
        $profileB = $this->createProfile($userB);

        $achievementB = Achievement::create([
            'profile_id' => $profileB->id,
            'judul' => 'User B Achievement',
            'jenis' => 'akademik',
            'tingkat' => 'nasional',
        ]);

        // User A tries to edit User B's achievement
        $response = $this->actingAs($userA)
            ->get(route('mahasiswa.profile.achievements.edit', $achievementB->id));

        $response->assertStatus(404);
    }

    /**
     * Test user A cannot update user B's achievement.
     */
    public function test_user_cannot_update_other_user_achievement(): void
    {
        $userA = $this->mahasiswaUser;
        $userB = User::factory()->create(['role' => Role::Mahasiswa]);

        $profileA = $this->createProfile($userA);
        $profileB = $this->createProfile($userB);

        $achievementB = Achievement::create([
            'profile_id' => $profileB->id,
            'judul' => 'Original',
            'jenis' => 'akademik',
            'tingkat' => 'nasional',
        ]);

        $response = $this->actingAs($userA)
            ->patch(route('mahasiswa.profile.achievements.update', $achievementB->id), [
                'judul' => 'Hacked',
                'jenis' => 'akademik',
                'tingkat' => 'nasional',
            ]);

        $response->assertStatus(404);

        $achievementB->refresh();
        $this->assertEquals('Original', $achievementB->judul);
    }

    /**
     * Test user A cannot delete user B's achievement.
     */
    public function test_user_cannot_delete_other_user_achievement(): void
    {
        $userA = $this->mahasiswaUser;
        $userB = User::factory()->create(['role' => Role::Mahasiswa]);

        $profileA = $this->createProfile($userA);
        $profileB = $this->createProfile($userB);

        $achievementB = Achievement::create([
            'profile_id' => $profileB->id,
            'judul' => 'User B Achievement',
            'jenis' => 'akademik',
            'tingkat' => 'nasional',
        ]);

        $response = $this->actingAs($userA)
            ->delete(route('mahasiswa.profile.achievements.destroy', $achievementB->id));

        $response->assertStatus(404);

        $this->assertDatabaseHas('achievements', ['id' => $achievementB->id]);
    }

    /**
     * Test multiple achievements are allowed.
     */
    public function test_multiple_achievements_allowed(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);

        Achievement::create([
            'profile_id' => $profile->id,
            'judul' => 'Achievement 1',
            'jenis' => 'akademik',
            'tingkat' => 'nasional',
        ]);

        Achievement::create([
            'profile_id' => $profile->id,
            'judul' => 'Achievement 2',
            'jenis' => 'non_akademik',
            'tingkat' => 'provinsi',
        ]);

        $this->assertEquals(2, $profile->achievements()->count());
    }

    /**
     * Test deleting achievement also deletes certificate file.
     */
    public function test_deleting_achievement_deletes_certificate(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);

        $file = UploadedFile::fake()->create('cert.pdf', 1024, 'application/pdf');
        $path = $file->store('certificates/' . $profile->id, 'public');

        $achievement = Achievement::create([
            'profile_id' => $profile->id,
            'judul' => 'With Certificate',
            'jenis' => 'akademik',
            'tingkat' => 'nasional',
            'certificate_path' => $path,
            'certificate_original_name' => 'cert.pdf',
        ]);

        Storage::disk('public')->assertExists($path);

        $this->actingAs($this->mahasiswaUser)
            ->delete(route('mahasiswa.profile.achievements.destroy', $achievement->id));

        Storage::disk('public')->assertMissing($path);
    }
}
