<?php

namespace Tests\Feature;

use App\Enums\AcademicStatus;
use App\Enums\Role;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AlumniTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Disable CSRF middleware for testing
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
     * Helper to create valid alumni profile data.
     */
    protected function validAlumniData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nim' => '12345678',
            'fakultas' => 'Fakultas Teknik',
            'program_studi' => 'Teknik Informatika',
            'tahun_masuk' => 2018,
            'tahun_lulus' => 2022,
            'ipk' => 3.75,
        ], $overrides);
    }

    /**
     * Helper to create lulus profile directly.
     */
    protected function createLulusProfile(array $overrides = []): Profile
    {
        $user = User::factory()->create([
            'name' => $overrides['name'] ?? 'Test User',
            'email' => $overrides['email'] ?? fake()->unique()->safeEmail(),
        ]);

        return Profile::create(array_merge([
            'user_id' => $user->id,
            'nim' => fake()->unique()->numerify('########'),
            'fakultas' => 'Fakultas Teknik',
            'program_studi' => 'Teknik Informatika',
            'tahun_masuk' => 2018,
            'tahun_lulus' => 2022,
            'academic_status' => AcademicStatus::Lulus,
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'L',
            'angkatan' => 2018,
        ], $overrides));
    }

    // ============================================
    // AUTHORIZATION TESTS
    // ============================================

    /**
     * Test admin can access admin alumni index.
     */
    public function test_admin_can_access_admin_alumni_index(): void
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get(route('admin.alumni.index'));
        $response->assertStatus(200);
    }

    /**
     * Test admin can access admin alumni create.
     */
    public function test_admin_can_access_admin_alumni_create(): void
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get(route('admin.alumni.create'));
        $response->assertStatus(200);
    }

    /**
     * Test admin can store alumni (user + profile).
     */
    public function test_admin_can_store_alumni(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.alumni.store'),
            $this->validAlumniData()
        );

        $response->assertRedirect(route('admin.alumni.index'));
        $response->assertSessionHas('success');

        // Check user was created
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'mahasiswa',
        ]);

        // Check profile was created with status = lulus
        $this->assertDatabaseHas('profiles', [
            'nim' => '12345678',
            'academic_status' => 'lulus',
        ]);
    }

    /**
     * Test admin can update alumni profile.
     */
    public function test_admin_can_update_alumni(): void
    {
        $admin = $this->createAdmin();
        $profile = $this->createLulusProfile(['nim' => '12345678']);
        $user = $profile->user;

        $response = $this->actingAs($admin)->put(route('admin.alumni.update', $profile),
            $this->validAlumniData(['nim' => '87654321', 'name' => 'Updated Name'])
        );

        $response->assertRedirect(route('admin.alumni.index'));
        $response->assertSessionHas('success');

        $profile->refresh();
        $user->refresh();
        $this->assertEquals('87654321', $profile->nim);
        $this->assertEquals('Updated Name', $user->name);
    }

    /**
     * Test admin can delete alumni.
     */
    public function test_admin_can_delete_alumni(): void
    {
        $admin = $this->createAdmin();
        $profile = $this->createLulusProfile();
        $userId = $profile->user_id;
        $profileId = $profile->id;

        $response = $this->actingAs($admin)->delete(route('admin.alumni.destroy', $profile));

        $response->assertRedirect(route('admin.alumni.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('profiles', ['id' => $profileId]);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    /**
     * Test admin can export alumni.
     */
    public function test_admin_can_export_alumni(): void
    {
        $admin = $this->createAdmin();
        $this->createLulusProfile();
        $this->createLulusProfile();

        $response = $this->actingAs($admin)->get(route('admin.alumni.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /**
     * Test guest cannot create alumni.
     */
    public function test_guest_cannot_create_alumni(): void
    {
        $response = $this->post(route('admin.alumni.store'), $this->validAlumniData());
        $this->assertTrue(in_array($response->status(), [302, 401, 419]));
    }

    /**
     * Test guest cannot update alumni.
     */
    public function test_guest_cannot_update_alumni(): void
    {
        $profile = $this->createLulusProfile();
        $response = $this->put(route('admin.alumni.update', $profile), $this->validAlumniData());
        $this->assertTrue(in_array($response->status(), [302, 401, 419]));
    }

    /**
     * Test guest cannot delete alumni.
     */
    public function test_guest_cannot_delete_alumni(): void
    {
        $profile = $this->createLulusProfile();
        $response = $this->delete(route('admin.alumni.destroy', $profile));
        $this->assertTrue(in_array($response->status(), [302, 401, 419]));
    }

    /**
     * Test guest cannot export alumni.
     */
    public function test_guest_cannot_export_alumni(): void
    {
        $response = $this->get(route('admin.alumni.export'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test mahasiswa cannot access admin alumni index.
     */
    public function test_mahasiswa_cannot_access_admin_alumni_index(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $response = $this->actingAs($mahasiswa)->get(route('admin.alumni.index'));
        $response->assertStatus(403);
    }

    /**
     * Test mahasiswa cannot create alumni.
     */
    public function test_mahasiswa_cannot_create_alumni(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $response = $this->actingAs($mahasiswa)->post(route('admin.alumni.store'), $this->validAlumniData());
        $this->assertTrue(in_array($response->status(), [403, 419]));
    }

    /**
     * Test mahasiswa cannot update alumni.
     */
    public function test_mahasiswa_cannot_update_alumni(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createLulusProfile();
        $response = $this->actingAs($mahasiswa)->put(route('admin.alumni.update', $profile), $this->validAlumniData());
        $this->assertTrue(in_array($response->status(), [403, 419]));
    }

    /**
     * Test mahasiswa cannot delete alumni.
     */
    public function test_mahasiswa_cannot_delete_alumni(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $profile = $this->createLulusProfile();
        $response = $this->actingAs($mahasiswa)->delete(route('admin.alumni.destroy', $profile));
        $this->assertTrue(in_array($response->status(), [403, 419]));
    }

    /**
     * Test mahasiswa cannot export alumni.
     */
    public function test_mahasiswa_cannot_export_alumni(): void
    {
        $mahasiswa = $this->createMahasiswa();
        $response = $this->actingAs($mahasiswa)->get(route('admin.alumni.export'));
        $response->assertStatus(403);
    }

    // ============================================
    // VALIDATION TESTS
    // ============================================

    /**
     * Test name is required.
     */
    public function test_name_is_required(): void
    {
        $admin = $this->createAdmin();
        $data = $this->validAlumniData();
        unset($data['name']);

        $response = $this->actingAs($admin)->post(route('admin.alumni.store'), $data);
        $response->assertSessionHasErrors('name');
    }

    /**
     * Test email is required.
     */
    public function test_email_is_required(): void
    {
        $admin = $this->createAdmin();
        $data = $this->validAlumniData();
        unset($data['email']);

        $response = $this->actingAs($admin)->post(route('admin.alumni.store'), $data);
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test NIM must be unique.
     */
    public function test_nim_must_be_unique(): void
    {
        $admin = $this->createAdmin();
        $this->createLulusProfile(['nim' => '12345678']);

        $response = $this->actingAs($admin)->post(route('admin.alumni.store'), $this->validAlumniData());
        $response->assertSessionHasErrors('nim');
    }

    /**
     * Test email must be unique.
     */
    public function test_email_must_be_unique(): void
    {
        $admin = $this->createAdmin();
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->actingAs($admin)->post(route('admin.alumni.store'), $this->validAlumniData());
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test password is required.
     */
    public function test_password_is_required(): void
    {
        $admin = $this->createAdmin();
        $data = $this->validAlumniData();
        unset($data['password']);

        $response = $this->actingAs($admin)->post(route('admin.alumni.store'), $data);
        $response->assertSessionHasErrors('password');
    }

    // ============================================
    // PUBLIC PRIVACY TESTS
    // ============================================

    /**
     * Test public alumni index is accessible.
     */
    public function test_public_alumni_index_is_accessible(): void
    {
        $this->createLulusProfile();
        $this->createLulusProfile();
        $response = $this->get(route('alumni.index'));
        $response->assertStatus(200);
    }

    /**
     * Test public alumni show is accessible.
     */
    public function test_public_alumni_show_is_accessible(): void
    {
        $profile = $this->createLulusProfile();
        $response = $this->get(route('alumni.show', $profile));
        $response->assertStatus(200);
    }

    /**
     * Test public alumni statistics is accessible.
     */
    public function test_public_alumni_statistics_is_accessible(): void
    {
        $response = $this->get(route('alumni.statistics'));
        $response->assertStatus(200);
    }

    /**
     * Test public alumni index does NOT expose email.
     */
    public function test_public_alumni_index_hides_email(): void
    {
        $profile = $this->createLulusProfile();
        $user = $profile->user;
        $user->email = 'secret@email.com';
        $user->save();

        $response = $this->get(route('alumni.index'));
        $response->assertDontSee('secret@email.com');
    }

    /**
     * Test public alumni index does NOT expose no_hp.
     */
    public function test_public_alumni_index_hides_no_hp(): void
    {
        $this->createLulusProfile(['no_hp' => '081234567890']);
        $response = $this->get(route('alumni.index'));
        $response->assertDontSee('081234567890');
    }

    /**
     * Test public alumni index does NOT expose alamat.
     */
    public function test_public_alumni_index_hides_alamat(): void
    {
        $this->createLulusProfile(['alamat' => 'Secret Address 123']);
        $response = $this->get(route('alumni.index'));
        $response->assertDontSee('Secret Address 123');
    }

    /**
     * Test public alumni show does NOT expose email.
     */
    public function test_public_alumni_show_hides_email(): void
    {
        $profile = $this->createLulusProfile();
        $user = $profile->user;
        $user->email = 'secret@email.com';
        $user->save();

        $response = $this->get(route('alumni.show', $profile));
        $response->assertDontSee('secret@email.com');
    }

    /**
     * Test only lulus profiles show in alumni.
     */
    public function test_only_lulus_profiles_show_in_alumni(): void
    {
        // Create lulus profile
        $lulusProfile = $this->createLulusProfile(['nim' => '11111111']);

        // Create aktif profile (mahasiswa)
        $user = User::factory()->create();
        Profile::create([
            'user_id' => $user->id,
            'nim' => '22222222',
            'fakultass' => 'Fakultas Teknik',
            'program_studi' => 'Teknik Informatika',
            'tahun_masuk' => 2018,
            'academic_status' => AcademicStatus::Aktif,
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'L',
            'angkatan' => 2018,
        ]);

        $response = $this->get(route('alumni.index'));

        // Should show lulus profile
        $response->assertSee('11111111');
        // Should not show aktif profile
        $response->assertDontSee('22222222');
    }

    // ============================================
    // SEARCH & FILTER TESTS
    // ============================================

    /**
     * Test search by NIM.
     */
    public function test_search_by_nim(): void
    {
        $this->createLulusProfile(['nim' => '12345678']);
        $this->createLulusProfile(['nim' => '87654321']);

        $response = $this->get(route('alumni.index', ['search' => '12345678']));
        $response->assertSee('12345678');
    }

    /**
     * Test filter by program studi.
     */
    public function test_filter_by_program_studi(): void
    {
        $this->createLulusProfile(['program_studi' => 'Teknik Informatika']);
        $this->createLulusProfile(['program_studi' => 'Sistem Informasi']);

        $response = $this->get(route('alumni.index', ['program_studi' => 'Teknik Informatika']));
        $response->assertSee('Teknik Informatika');
    }

    /**
     * Test filter by tahun lulus.
     */
    public function test_filter_by_tahun_lulus(): void
    {
        $this->createLulusProfile(['nim' => '11111111', 'tahun_lulus' => 2022]);
        $this->createLulusProfile(['nim' => '22222222', 'tahun_lulus' => 2023]);

        $response = $this->get(route('alumni.index', ['tahun_lulus' => '2022']));
        $response->assertSee('11111111');
    }

    // ============================================
    // CRUD TESTS
    // ============================================

    /**
     * Test admin can view alumni detail.
     */
    public function test_admin_can_view_alumni_detail(): void
    {
        $admin = $this->createAdmin();
        $profile = $this->createLulusProfile(['name' => 'Test User']);

        $response = $this->actingAs($admin)->get(route('admin.alumni.show', $profile));
        $response->assertStatus(200);
        $response->assertSee('Test User');
    }

    /**
     * Test admin can access edit alumni.
     */
    public function test_admin_can_access_edit_alumni(): void
    {
        $admin = $this->createAdmin();
        $profile = $this->createLulusProfile();

        $response = $this->actingAs($admin)->get(route('admin.alumni.edit', $profile));
        $response->assertStatus(200);
    }

    /**
     * Test alumni store creates user and profile.
     */
    public function test_alumni_store_creates_user_and_profile(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)->post(route('admin.alumni.store'), $this->validAlumniData());

        // Check user was created
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Check profile was created
        $this->assertDatabaseHas('profiles', [
            'nim' => '12345678',
        ]);
    }

    /**
     * Test alumni update modifies profile.
     */
    public function test_alumni_update_modifies_profile(): void
    {
        $admin = $this->createAdmin();
        $profile = $this->createLulusProfile();
        $user = $profile->user;

        $this->actingAs($admin)->put(route('admin.alumni.update', $profile),
            $this->validAlumniData(['name' => 'Updated Name'])
        );

        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
    }

    /**
     * Test alumni destroy removes user and profile.
     */
    public function test_alumni_destroy_removes_user_and_profile(): void
    {
        $admin = $this->createAdmin();
        $profile = $this->createLulusProfile();
        $userId = $profile->user_id;
        $profileId = $profile->id;

        $this->actingAs($admin)->delete(route('admin.alumni.destroy', $profile));

        $this->assertDatabaseMissing('profiles', ['id' => $profileId]);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    // ============================================
    // FLASH MESSAGE TESTS
    // ============================================

    /**
     * Test store alumni shows success message.
     */
    public function test_store_alumni_shows_success_message(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.alumni.store'), $this->validAlumniData());
        $response->assertSessionHas('success', 'Data alumni berhasil ditambahkan.');
    }

    /**
     * Test update alumni shows success message.
     */
    public function test_update_alumni_shows_success_message(): void
    {
        $admin = $this->createAdmin();
        $profile = $this->createLulusProfile();

        $response = $this->actingAs($admin)->put(route('admin.alumni.update', $profile), $this->validAlumniData());
        $response->assertSessionHas('success', 'Data alumni berhasil diperbarui.');
    }

    /**
     * Test delete alumni shows success message.
     */
    public function test_delete_alumni_shows_success_message(): void
    {
        $admin = $this->createAdmin();
        $profile = $this->createLulusProfile();

        $response = $this->actingAs($admin)->delete(route('admin.alumni.destroy', $profile));
        $response->assertSessionHas('success', 'Data alumni berhasil dihapus.');
    }

    // ============================================
    // STATUS TESTS
    // ============================================

    /**
     * Test role stays as mahasiswa when academic_status is lulus.
     */
    public function test_role_stays_mahasiswa_when_lulus(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)->post(route('admin.alumni.store'), $this->validAlumniData());

        $user = User::where('email', 'john@example.com')->first();
        $this->assertEquals(Role::Mahasiswa, $user->role);

        $profile = Profile::where('nim', '12345678')->first();
        $this->assertEquals(AcademicStatus::Lulus, $profile->academic_status);
    }
}
