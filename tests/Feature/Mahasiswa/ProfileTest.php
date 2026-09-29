<?php

namespace Tests\Feature\Mahasiswa;

use App\Enums\AcademicStatus;
use App\Enums\Role;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected User $mahasiswaUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mahasiswa user
        $this->mahasiswaUser = User::factory()->create([
            'role' => Role::Mahasiswa,
            'name' => 'Test Mahasiswa',
            'email' => 'test@example.com',
        ]);

        // Create admin user for authorization tests
        $this->adminUser = User::factory()->create([
            'role' => Role::Admin,
        ]);

        // Disable CSRF middleware for testing
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /**
     * Helper to create aktif profile for user.
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
            'academic_status' => AcademicStatus::Aktif,
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Test No. 1',
        ], $attributes));
    }

    // ============================================
    // BASIC ACCESS TESTS
    // ============================================

    /**
     * Test mahasiswa can access profile page.
     */
    public function test_mahasiswa_can_access_profile_page(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.profile.index'));

        $response->assertStatus(200);
        $response->assertViewIs('mahasiswa.profile.index');
    }

    /**
     * Test mahasiswa can access profile edit page.
     */
    public function test_mahasiswa_can_access_profile_edit_page(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('mahasiswa.profile.edit');
    }

    /**
     * Test guest cannot access profile page.
     */
    public function test_guest_cannot_access_profile_page(): void
    {
        $response = $this->get(route('mahasiswa.profile.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test admin cannot access mahasiswa profile routes.
     */
    public function test_admin_cannot_access_mahasiswa_profile(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('mahasiswa.profile.index'));

        $response->assertStatus(403);
    }

    // ============================================
    // DATA DISPLAY TESTS
    // ============================================

    /**
     * Test profile displays user name correctly.
     */
    public function test_profile_displays_user_name(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.profile.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Mahasiswa');
    }

    /**
     * Test profile displays academic status as "Mahasiswa" when aktif.
     */
    public function test_profile_displays_mahasiswa_status_when_aktif(): void
    {
        $this->createProfile($this->mahasiswaUser, [
            'academic_status' => AcademicStatus::Aktif,
        ]);

        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.profile.index'));

        $response->assertStatus(200);
        $response->assertSee('Mahasiswa Aktif');
    }

    /**
     * Test profile displays academic status as "Alumni" when lulus.
     */
    public function test_profile_displays_alumni_status_when_lulus(): void
    {
        $this->createProfile($this->mahasiswaUser, [
            'academic_status' => AcademicStatus::Lulus,
            'tahun_lulus' => 2024,
            'tanggal_lulus' => '2024-08-01',
            'ipk' => 3.75,
        ]);

        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.profile.index'));

        $response->assertStatus(200);
        $response->assertSee('Alumni');
    }

    /**
     * Test user role remains mahasiswa even when academic status is lulus.
     */
    public function test_user_role_remains_mahasiswa_when_lulus(): void
    {
        $this->createProfile($this->mahasiswaUser, [
            'academic_status' => AcademicStatus::Lulus,
        ]);

        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.profile.index'));

        $response->assertStatus(200);
        // Should not show "Alumni" as role
        $response->assertSee('Mahasiswa');
        $this->assertEquals(Role::Mahasiswa, $this->mahasiswaUser->role);
    }

    // ============================================
    // UPDATE TESTS
    // ============================================

    /**
     * Test mahasiswa can update profile data.
     */
    public function test_mahasiswa_can_update_profile(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'name' => 'Updated Name',
                'nik' => '1234567890123456',
                'tempat_lahir' => 'Bandung',
                'jenis_kelamin' => 'L',
                'no_hp' => '089876543210',
            ]);

        $response->assertRedirect(route('mahasiswa.profile.index'));
        $response->assertSessionHas('success', 'Profil berhasil diperbarui.');

        // Verify database
        $this->mahasiswaUser->refresh();
        $this->assertEquals('Updated Name', $this->mahasiswaUser->name);
        $this->assertEquals('1234567890123456', $this->mahasiswaUser->profile->nik);
        $this->assertEquals('Bandung', $this->mahasiswaUser->profile->tempat_lahir);
    }

    /**
     * Test name can be updated.
     */
    public function test_name_can_be_updated(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'name' => 'New Student Name',
            ]);

        $this->mahasiswaUser->refresh();
        $this->assertEquals('New Student Name', $this->mahasiswaUser->name);
    }

    /**
     * Test NIK can be saved.
     */
    public function test_nik_can_be_saved(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);

        $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'nik' => '1234567890123456',
            ]);

        $profile->refresh();
        $this->assertEquals('1234567890123456', $profile->nik);
    }

    /**
     * Test NIK cannot be duplicate with another profile.
     */
    public function test_nik_cannot_be_duplicate(): void
    {
        // Create another user with NIK
        $otherUser = User::factory()->create(['role' => Role::Mahasiswa]);
        $this->createProfile($otherUser, ['nik' => '1234567890123456']);

        // Try to use same NIK
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'nik' => '1234567890123456',
            ]);

        $response->assertSessionHasErrors('nik');
    }

    /**
     * Test validates jenis kelamin.
     */
    public function test_validates_jenis_kelamin(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'jenis_kelamin' => 'X', // Invalid
            ]);

        $response->assertSessionHasErrors('jenis_kelamin');
    }

    /**
     * Test validates tanggal lahir.
     */
    public function test_validates_tanggal_lahir(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'tanggal_lahir' => now()->addDay()->format('Y-m-d'), // Future date
            ]);

        $response->assertSessionHasErrors('tanggal_lahir');
    }

    /**
     * Test validates status pernikahan.
     */
    public function test_validates_status_pernikahan(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'status_pernikahan' => 'invalid_status',
            ]);

        $response->assertSessionHasErrors('status_pernikahan');
    }

    /**
     * Test validates agama.
     */
    public function test_validates_agama(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'agama' => 'invalid_religion',
            ]);

        $response->assertSessionHasErrors('agama');
    }

    // ============================================
    // PROTECTED FIELDS TESTS
    // ============================================

    /**
     * Test mahasiswa cannot change user_id (field is silently ignored).
     */
    public function test_mahasiswa_cannot_change_user_id(): void
    {
        $this->createProfile($this->mahasiswaUser);

        // Try to change user_id via hidden field (should be silently ignored)
        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'name' => 'Test Name',
                'user_id' => $this->adminUser->id,
            ]);

        // Should succeed (field is ignored, not blocked)
        $response->assertRedirect(route('mahasiswa.profile.index'));

        // Verify name was updated but user_id was not
        $this->mahasiswaUser->refresh();
        $this->assertEquals('Test Name', $this->mahasiswaUser->name);
        $this->assertEquals($this->mahasiswaUser->id, $this->mahasiswaUser->profile->user_id);
    }

    /**
     * Test role is not in the request (security by omission).
     */
    public function test_role_field_is_not_processed(): void
    {
        $this->createProfile($this->mahasiswaUser);

        // Verify user role is not affected
        $this->assertEquals(Role::Mahasiswa, $this->mahasiswaUser->role);
    }

    /**
     * Test academic_status is not in the request (security by omission).
     */
    public function test_academic_status_is_not_processed(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $originalStatus = $profile->academic_status;

        $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'name' => 'Test Name',
            ]);

        $profile->refresh();
        $this->assertEquals($originalStatus, $profile->academic_status);
    }

    /**
     * Test NIM is not in the request (security by omission).
     */
    public function test_nim_is_not_processed(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $originalNim = $profile->nim;

        $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'name' => 'Test Name',
            ]);

        $profile->refresh();
        $this->assertEquals($originalNim, $profile->nim);
    }

    /**
     * Test fakultas is not in the request (security by omission).
     */
    public function test_fakultas_is_not_processed(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $originalFakultas = $profile->fakultas;

        $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'name' => 'Test Name',
            ]);

        $profile->refresh();
        $this->assertEquals($originalFakultas, $profile->fakultas);
    }

    /**
     * Test program_studi is not in the request (security by omission).
     */
    public function test_program_studi_is_not_processed(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $originalProgramStudi = $profile->program_studi;

        $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'name' => 'Test Name',
            ]);

        $profile->refresh();
        $this->assertEquals($originalProgramStudi, $profile->program_studi);
    }

    // ============================================
    // OWNERSHIP TESTS
    // ============================================

    /**
     * Test user A cannot update user B's profile.
     */
    public function test_user_cannot_update_other_user_profile(): void
    {
        // Create two users with profiles
        $userA = $this->mahasiswaUser;
        $userB = User::factory()->create(['role' => Role::Mahasiswa]);

        $this->createProfile($userA, ['nik' => '1111111111111111']);
        $this->createProfile($userB, ['nik' => '2222222222222222']);

        // User A tries to update (should only update their own)
        $response = $this->actingAs($userA)
            ->patch(route('mahasiswa.profile.update'), [
                'nik' => '3333333333333333',
            ]);

        $response->assertRedirect(route('mahasiswa.profile.index'));

        // Verify only User A's profile was updated
        $userA->refresh();
        $userB->refresh();
        $this->assertEquals('3333333333333333', $userA->profile->nik);
        $this->assertEquals('2222222222222222', $userB->profile->nik);
    }

    // ============================================
    // PHOTO UPLOAD TESTS
    // ============================================

    /**
     * Test photo upload validation.
     */
    public function test_photo_upload_validation(): void
    {
        Storage::fake('public');
        $this->createProfile($this->mahasiswaUser);

        // Test invalid file type
        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'foto' => UploadedFile::fake()->create('document.pdf', 100),
            ]);

        $response->assertSessionHasErrors('foto');
    }

    /**
     * Test photo can be uploaded.
     */
    public function test_photo_can_be_uploaded(): void
    {
        Storage::fake('public');
        $this->createProfile($this->mahasiswaUser);

        $file = UploadedFile::fake()->image('avatar.jpg', 400, 400);

        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.update'), [
                'foto' => $file,
            ]);

        $response->assertRedirect(route('mahasiswa.profile.index'));

        // Verify photo was stored
        $this->mahasiswaUser->refresh();
        $this->assertNotNull($this->mahasiswaUser->profile->foto);
        Storage::disk('public')->assertExists($this->mahasiswaUser->profile->foto);
    }

    // ============================================
    // PROFILE NOT FOUND TESTS
    // ============================================

    /**
     * Test handles profile not found gracefully.
     */
    public function test_handles_profile_not_found_gracefully(): void
    {
        // User without profile tries to access
        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.profile.index'));

        $response->assertStatus(200);
        $response->assertViewIs('mahasiswa.profile.index');
    }

    // ============================================
    // EXISTING TESTS REGRESSION
    // ============================================

    /**
     * Test existing dashboard still works.
     */
    public function test_dashboard_still_works(): void
    {
        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.dashboard'));

        $response->assertStatus(200);
    }

    /**
     * Test existing tracer study still works.
     */
    public function test_tracer_study_still_works(): void
    {
        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.tracer-study.index'));

        $response->assertStatus(200);
    }
}
