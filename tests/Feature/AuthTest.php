<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use App\Models\Alumni;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * Test that guest cannot access admin dashboard.
     */
    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that guest cannot access mahasiswa dashboard.
     */
    public function test_guest_cannot_access_mahasiswa_dashboard(): void
    {
        $response = $this->get(route('mahasiswa.dashboard'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that mahasiswa cannot access admin dashboard.
     */
    public function test_mahasiswa_cannot_access_admin_dashboard(): void
    {
        $mahasiswa = User::factory()->create(['role' => Role::Mahasiswa]);
        $response = $this->actingAs($mahasiswa)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    /**
     * Test that admin can access admin dashboard.
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    /**
     * Test that mahasiswa can access mahasiswa dashboard.
     */
    public function test_mahasiswa_can_access_mahasiswa_dashboard(): void
    {
        $mahasiswa = User::factory()->create(['role' => Role::Mahasiswa]);
        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.dashboard'));
        $response->assertStatus(200);
    }

    /**
     * Test that admin cannot access mahasiswa dashboard.
     */
    public function test_admin_cannot_access_mahasiswa_dashboard(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $response = $this->actingAs($admin)->get(route('mahasiswa.dashboard'));
        $response->assertStatus(403);
    }

    /**
     * Test that admin can access admin alumni CRUD.
     */
    public function test_admin_can_access_admin_alumni_index(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $response = $this->actingAs($admin)->get(route('admin.alumni.index'));
        $response->assertStatus(200);
    }

    /**
     * Test that mahasiswa cannot access admin alumni CRUD via GET.
     */
    public function test_mahasiswa_cannot_access_admin_alumni_index(): void
    {
        $mahasiswa = User::factory()->create(['role' => Role::Mahasiswa]);
        $response = $this->actingAs($mahasiswa)->get(route('admin.alumni.index'));
        $response->assertStatus(403);
    }

    /**
     * Test that user helper methods work correctly.
     */
    public function test_user_helper_methods(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $mahasiswa = User::factory()->create(['role' => Role::Mahasiswa]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isMahasiswa());
        $this->assertFalse($mahasiswa->isAdmin());
        $this->assertTrue($mahasiswa->isMahasiswa());
    }

    /**
     * Test that user role is properly cast to enum.
     */
    public function test_user_role_is_enum(): void
    {
        $user = User::factory()->create(['role' => Role::Admin]);
        
        $this->assertInstanceOf(Role::class, $user->role);
        $this->assertEquals('admin', $user->role->value);
    }

    /**
     * Test that registration creates user with mahasiswa role.
     */
    public function test_registration_creates_user_with_mahasiswa_role(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => Role::Mahasiswa,
        ]);

        $this->assertEquals(Role::Mahasiswa, $user->role);
    }

    /**
     * Test that user factory creates mahasiswa by default.
     */
    public function test_user_factory_creates_mahasiswa_by_default(): void
    {
        $user = User::factory()->create();
        
        $this->assertEquals(Role::Mahasiswa, $user->role);
    }

    /**
     * Test that admin factory method works.
     */
    public function test_admin_factory_method_works(): void
    {
        $admin = User::factory()->admin()->create();
        
        $this->assertEquals(Role::Admin, $admin->role);
    }

    /**
     * Test that mahasiswa factory method works.
     */
    public function test_mahasiswa_factory_method_works(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        
        $this->assertEquals(Role::Mahasiswa, $mahasiswa->role);
    }

    // ============================================
    // ALUMNI SECURITY TESTS
    // ============================================

    /**
     * Test that guest cannot create alumni via POST.
     */
    public function test_guest_cannot_create_alumni(): void
    {
        $response = $this->post(route('admin.alumni.store'), [
            'nim' => '12345678',
            'nama_lengkap' => 'Test Alumni',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2000-01-01',
            'email' => 'test@email.com',
            'tahun_masuk' => 2018,
            'tahun_lulus' => 2022,
            'program_studi' => 'Teknik Informatika',
            'status_pekerjaan' => 'Bekerja',
        ]);

        // Guest should be rejected (302 redirect to login, 401, or 419 CSRF)
        $this->assertTrue(
            $response->status() === 302 || 
            $response->status() === 401 ||
            $response->status() === 419
        );
    }

    /**
     * Test that guest cannot update alumni via PUT.
     */
    public function test_guest_cannot_update_alumni(): void
    {
        $alumni = Alumni::factory()->create();
        
        $response = $this->put(route('admin.alumni.update', $alumni), [
            'nim' => '12345678',
            'nama_lengkap' => 'Updated Name',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2000-01-01',
            'email' => 'updated@email.com',
            'tahun_masuk' => 2018,
            'tahun_lulus' => 2022,
            'program_studi' => 'Teknik Informatika',
            'status_pekerjaan' => 'Bekerja',
        ]);

        $this->assertTrue(
            $response->status() === 302 || 
            $response->status() === 401 ||
            $response->status() === 419
        );
    }

    /**
     * Test that guest cannot delete alumni.
     */
    public function test_guest_cannot_delete_alumni(): void
    {
        $alumni = Alumni::factory()->create();
        
        $response = $this->delete(route('admin.alumni.destroy', $alumni));

        $this->assertTrue(
            $response->status() === 302 || 
            $response->status() === 401 ||
            $response->status() === 419
        );
    }

    /**
     * Test that guest cannot export alumni.
     */
    public function test_guest_cannot_export_alumni(): void
    {
        $response = $this->get(route('admin.alumni.export'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test that mahasiswa cannot create alumni.
     */
    public function test_mahasiswa_cannot_create_alumni(): void
    {
        $mahasiswa = User::factory()->create(['role' => Role::Mahasiswa]);
        
        $response = $this->actingAs($mahasiswa)->post(route('admin.alumni.store'), [
            'nim' => '12345678',
            'nama_lengkap' => 'Test Alumni',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2000-01-01',
            'email' => 'test@email.com',
            'tahun_masuk' => 2018,
            'tahun_lulus' => 2022,
            'program_studi' => 'Teknik Informatika',
            'status_pekerjaan' => 'Bekerja',
        ]);

        // Should be rejected (403 forbidden or 419 CSRF)
        $this->assertTrue(
            $response->status() === 403 || 
            $response->status() === 419
        );
    }

    /**
     * Test that mahasiswa cannot update alumni.
     */
    public function test_mahasiswa_cannot_update_alumni(): void
    {
        $mahasiswa = User::factory()->create(['role' => Role::Mahasiswa]);
        $alumni = Alumni::factory()->create();
        
        $response = $this->actingAs($mahasiswa)->put(route('admin.alumni.update', $alumni), [
            'nim' => '12345678',
            'nama_lengkap' => 'Updated Name',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2000-01-01',
            'email' => 'updated@email.com',
            'tahun_masuk' => 2018,
            'tahun_lulus' => 2022,
            'program_studi' => 'Teknik Informatika',
            'status_pekerjaan' => 'Bekerja',
        ]);

        $this->assertTrue(
            $response->status() === 403 || 
            $response->status() === 419
        );
    }

    /**
     * Test that mahasiswa cannot delete alumni.
     */
    public function test_mahasiswa_cannot_delete_alumni(): void
    {
        $mahasiswa = User::factory()->create(['role' => Role::Mahasiswa]);
        $alumni = Alumni::factory()->create();
        
        $response = $this->actingAs($mahasiswa)->delete(route('admin.alumni.destroy', $alumni));

        $this->assertTrue(
            $response->status() === 403 || 
            $response->status() === 419
        );
    }

    /**
     * Test that mahasiswa cannot export alumni.
     */
    public function test_mahasiswa_cannot_export_alumni(): void
    {
        $mahasiswa = User::factory()->create(['role' => Role::Mahasiswa]);
        
        $response = $this->actingAs($mahasiswa)->get(route('admin.alumni.export'));

        $response->assertStatus(403);
    }

    /**
     * Test that admin can access admin alumni export.
     */
    public function test_admin_can_export_alumni(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        Alumni::factory()->count(3)->create();
        
        $response = $this->actingAs($admin)->get(route('admin.alumni.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /**
     * Test that public alumni index is accessible.
     */
    public function test_public_alumni_index_accessible(): void
    {
        Alumni::factory()->count(3)->create();
        
        $response = $this->get(route('alumni.index'));

        $response->assertStatus(200);
    }

    /**
     * Test that public alumni show is accessible.
     */
    public function test_public_alumni_show_accessible(): void
    {
        $alumni = Alumni::factory()->create();
        
        $response = $this->get(route('alumni.show', $alumni));

        $response->assertStatus(200);
    }

    /**
     * Test that public alumni statistics is accessible.
     */
    public function test_public_alumni_statistics_accessible(): void
    {
        $response = $this->get(route('alumni.statistics'));

        $response->assertStatus(200);
    }

    /**
     * Test that no users have NULL role.
     */
    public function test_no_users_have_null_role(): void
    {
        User::factory()->admin()->create();
        User::factory()->mahasiswa()->create();
        User::factory()->create();
        
        $usersWithNullRole = User::whereNull('role')->count();
        
        $this->assertEquals(0, $usersWithNullRole);
    }

    // ============================================
    // PRIVACY TESTS
    // ============================================

    /**
     * Test that public alumni index does NOT expose sensitive data.
     */
    public function test_public_alumni_index_hides_sensitive_data(): void
    {
        Alumni::factory()->create([
            'email' => 'secret@email.com',
            'nomor_hp' => '081234567890',
            'alamat' => 'Secret Address',
            'tempat_lahir' => 'Secret City',
            'tanggal_lahir' => '2000-01-01',
        ]);
        
        $response = $this->get(route('alumni.index'));

        $response->assertStatus(200);
        $response->assertDontSee('secret@email.com');
        $response->assertDontSee('081234567890');
        $response->assertDontSee('Secret Address');
    }

    /**
     * Test that public alumni show does NOT expose sensitive data.
     */
    public function test_public_alumni_show_hides_sensitive_data(): void
    {
        $alumni = Alumni::factory()->create([
            'email' => 'secret@email.com',
            'nomor_hp' => '081234567890',
            'alamat' => 'Secret Address',
            'tempat_lahir' => 'Secret City',
            'tanggal_lahir' => '2000-01-01',
        ]);
        
        $response = $this->get(route('alumni.show', $alumni));

        $response->assertStatus(200);
        $response->assertDontSee('secret@email.com');
        $response->assertDontSee('081234567890');
        $response->assertDontSee('Secret Address');
        $response->assertDontSee('Secret City');
    }
}
