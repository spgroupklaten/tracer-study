<?php

namespace Tests\Feature\Mahasiswa;

use App\Enums\AcademicStatus;
use App\Enums\EducationLevel;
use App\Enums\Role;
use App\Models\EducationHistory;
use App\Models\Profile;
use App\Models\ProfileContact;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProfileContactAndEducationTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected User $mahasiswaUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

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
            'academic_status' => AcademicStatus::Aktif,
        ], $attributes));
    }

    // ============================================
    // CONTACT TESTS
    // ============================================

    /**
     * Test mahasiswa can access contact edit page.
     */
    public function test_mahasiswa_can_access_contact_edit_page(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.profile.contact.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('mahasiswa.profile.contact-edit');
    }

    /**
     * Test guest cannot access contact edit.
     */
    public function test_guest_cannot_access_contact_edit(): void
    {
        $response = $this->get(route('mahasiswa.profile.contact.edit'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test admin cannot access mahasiswa contact edit.
     */
    public function test_admin_cannot_access_contact_edit(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('mahasiswa.profile.contact.edit'));

        $response->assertStatus(403);
    }

    /**
     * Test mahasiswa can update contact.
     */
    public function test_mahasiswa_can_update_contact(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.contact.update'), [
                'whatsapp' => '081234567890',
                'linkedin' => 'https://linkedin.com/in/testuser',
                'instagram' => 'https://instagram.com/testuser',
            ]);

        $response->assertRedirect(route('mahasiswa.profile.index'));
        $response->assertSessionHas('success', 'Kontak dan media sosial berhasil diperbarui.');

        $profile->refresh();
        $this->assertEquals('081234567890', $profile->contact->whatsapp);
    }

    /**
     * Test valid URLs are accepted.
     */
    public function test_valid_urls_are_accepted(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.contact.update'), [
                'website' => 'https://example.com',
                'linkedin' => 'https://linkedin.com/in/user',
                'instagram' => 'https://instagram.com/user',
                'facebook' => 'https://facebook.com/user',
                'twitter' => 'https://x.com/user',
                'github' => 'https://github.com/user',
                'youtube' => 'https://youtube.com/@channel',
            ]);

        $response->assertRedirect(route('mahasiswa.profile.index'));
    }

    /**
     * Test invalid URLs are rejected.
     */
    public function test_invalid_urls_are_rejected(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.contact.update'), [
                'website' => 'not-a-valid-url',
            ]);

        $response->assertSessionHasErrors('website');
    }

    /**
     * Test whatsapp can be saved.
     */
    public function test_whatsapp_can_be_saved(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);

        $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.contact.update'), [
                'whatsapp' => '085123456789',
            ]);

        $profile->refresh();
        $this->assertEquals('085123456789', $profile->contact->whatsapp);
    }

    /**
     * Test user A cannot update user B's contact.
     */
    public function test_user_cannot_update_other_user_contact(): void
    {
        $userA = $this->mahasiswaUser;
        $userB = User::factory()->create(['role' => Role::Mahasiswa]);

        $this->createProfile($userA);
        $profileB = $this->createProfile($userB);

        // User A tries to update (should only affect their own)
        $this->actingAs($userA)
            ->patch(route('mahasiswa.profile.contact.update'), [
                'whatsapp' => '1111111111',
            ]);

        // User B's contact should be unchanged
        $profileB->refresh();
        $this->assertNull($profileB->contact);
    }

    // ============================================
    // EDUCATION HISTORY TESTS
    // ============================================

    /**
     * Test mahasiswa can add education.
     */
    public function test_mahasiswa_can_add_education(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.education.store'), [
                'jenjang' => 's1',
                'nama_institusi' => 'Universitas ABC',
                'jurusan' => 'Teknik Informatika',
                'kota' => 'Jakarta',
                'tahun_masuk' => 2018,
                'tahun_lulus' => 2022,
                'keterangan' => 'Lulus tepat waktu',
            ]);

        $response->assertRedirect(route('mahasiswa.profile.index'));
        $response->assertSessionHas('success', 'Riwayat pendidikan berhasil ditambahkan.');

        $this->assertDatabaseHas('education_histories', [
            'profile_id' => $profile->id,
            'jenjang' => 's1',
            'nama_institusi' => 'Universitas ABC',
        ]);
    }

    /**
     * Test education requires jenjang.
     */
    public function test_education_requires_jenjang(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.education.store'), [
                'nama_institusi' => 'Universitas ABC',
                'tahun_masuk' => 2018,
            ]);

        $response->assertSessionHasErrors('jenjang');
    }

    /**
     * Test education requires nama institusi.
     */
    public function test_education_requires_nama_institusi(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.education.store'), [
                'jenjang' => 's1',
                'tahun_masuk' => 2018,
            ]);

        $response->assertSessionHasErrors('nama_institusi');
    }

    /**
     * Test tahun lulus cannot be less than tahun masuk.
     */
    public function test_tahun_lulus_cannot_be_less_than_tahun_masuk(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.education.store'), [
                'jenjang' => 's1',
                'nama_institusi' => 'Universitas ABC',
                'tahun_masuk' => 2022,
                'tahun_lulus' => 2020,
            ]);

        $response->assertSessionHasErrors('tahun_lulus');
    }

    /**
     * Test mahasiswa can edit education.
     */
    public function test_mahasiswa_can_edit_education(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $education = EducationHistory::create([
            'profile_id' => $profile->id,
            'jenjang' => 'sma',
            'nama_institusi' => 'SMK Negeri 1',
            'tahun_masuk' => 2016,
            'tahun_lulus' => 2019,
        ]);

        $response = $this->actingAs($this->mahasiswaUser)
            ->patch(route('mahasiswa.profile.education.update', $education->id), [
                'jenjang' => 'sma',
                'nama_institusi' => 'SMK Negeri 2',
                'tahun_masuk' => 2016,
                'tahun_lulus' => 2019,
            ]);

        $response->assertRedirect(route('mahasiswa.profile.index'));
        $response->assertSessionHas('success', 'Riwayat pendidikan berhasil diperbarui.');

        $education->refresh();
        $this->assertEquals('SMK Negeri 2', $education->nama_institusi);
    }

    /**
     * Test mahasiswa can delete education.
     */
    public function test_mahasiswa_can_delete_education(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $education = EducationHistory::create([
            'profile_id' => $profile->id,
            'jenjang' => 'sma',
            'nama_institusi' => 'SMK Negeri 1',
            'tahun_masuk' => 2016,
            'tahun_lulus' => 2019,
        ]);

        $response = $this->actingAs($this->mahasiswaUser)
            ->delete(route('mahasiswa.profile.education.destroy', $education->id));

        $response->assertRedirect(route('mahasiswa.profile.index'));
        $response->assertSessionHas('success', 'Riwayat pendidikan berhasil dihapus.');
        $this->assertDatabaseMissing('education_histories', ['id' => $education->id]);
    }

    /**
     * Test user cannot edit other user's education.
     */
    public function test_user_cannot_edit_other_user_education(): void
    {
        $userA = $this->mahasiswaUser;
        $userB = User::factory()->create(['role' => Role::Mahasiswa]);

        $profileA = $this->createProfile($userA);
        $profileB = $this->createProfile($userB);

        $educationB = EducationHistory::create([
            'profile_id' => $profileB->id,
            'jenjang' => 's1',
            'nama_institusi' => 'Universitas B',
            'tahun_masuk' => 2018,
        ]);

        // User A tries to edit User B's education
        $response = $this->actingAs($userA)
            ->patch(route('mahasiswa.profile.education.update', $educationB->id), [
                'jenjang' => 's1',
                'nama_institusi' => 'Hacked University',
                'tahun_masuk' => 2018,
            ]);

        // Should return 404 because User A doesn't own this education
        $response->assertStatus(404);

        // Education should be unchanged
        $educationB->refresh();
        $this->assertEquals('Universitas B', $educationB->nama_institusi);
    }

    /**
     * Test user cannot delete other user's education.
     */
    public function test_user_cannot_delete_other_user_education(): void
    {
        $userA = $this->mahasiswaUser;
        $userB = User::factory()->create(['role' => Role::Mahasiswa]);

        $profileA = $this->createProfile($userA);
        $profileB = $this->createProfile($userB);

        $educationB = EducationHistory::create([
            'profile_id' => $profileB->id,
            'jenjang' => 's1',
            'nama_institusi' => 'Universitas B',
            'tahun_masuk' => 2018,
        ]);

        // User A tries to delete User B's education
        $response = $this->actingAs($userA)
            ->delete(route('mahasiswa.profile.education.destroy', $educationB->id));

        // Should return 404
        $response->assertStatus(404);

        // Education should still exist
        $this->assertDatabaseHas('education_histories', ['id' => $educationB->id]);
    }

    /**
     * Test multiple education can be added.
     */
    public function test_multiple_education_can_be_added(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);

        // Add SMA
        $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.education.store'), [
                'jenjang' => 'sma',
                'nama_institusi' => 'SMK Negeri 1',
                'tahun_masuk' => 2016,
                'tahun_lulus' => 2019,
            ]);

        // Add S1
        $this->actingAs($this->mahasiswaUser)
            ->post(route('mahasiswa.profile.education.store'), [
                'jenjang' => 's1',
                'nama_institusi' => 'Universitas ABC',
                'tahun_masuk' => 2019,
                'tahun_lulus' => 2023,
            ]);

        // Profile should have 2 education records
        $this->assertEquals(2, $profile->educationHistories()->count());
    }

    /**
     * Test guest cannot add education.
     */
    public function test_guest_cannot_add_education(): void
    {
        $response = $this->post(route('mahasiswa.profile.education.store'), [
            'jenjang' => 's1',
            'nama_institusi' => 'Universitas ABC',
            'tahun_masuk' => 2018,
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test guest cannot delete education.
     */
    public function test_guest_cannot_delete_education(): void
    {
        $profile = $this->createProfile($this->mahasiswaUser);
        $education = EducationHistory::create([
            'profile_id' => $profile->id,
            'jenjang' => 's1',
            'nama_institusi' => 'Universitas ABC',
            'tahun_masuk' => 2018,
        ]);

        $response = $this->delete(route('mahasiswa.profile.education.destroy', $education->id));
        $response->assertRedirect(route('login'));
    }

    // ============================================
    // REGRESSION TESTS
    // ============================================

    /**
     * Test existing profile page still works.
     */
    public function test_profile_page_still_works(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.profile.index'));

        $response->assertStatus(200);
    }

    /**
     * Test existing profile edit still works.
     */
    public function test_profile_edit_still_works(): void
    {
        $this->createProfile($this->mahasiswaUser);

        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.profile.edit'));

        $response->assertStatus(200);
    }

    /**
     * Test dashboard still works.
     */
    public function test_dashboard_still_works(): void
    {
        $response = $this->actingAs($this->mahasiswaUser)
            ->get(route('mahasiswa.dashboard'));

        $response->assertStatus(200);
    }
}
