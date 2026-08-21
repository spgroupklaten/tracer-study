<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that the application boots successfully.
     */
    public function test_application_boots(): void
    {
        $this->assertTrue(true);
    }

    /**
     * Test that main routes are properly registered.
     */
    public function test_main_routes_are_registered(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('home'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('login'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('register'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('dashboard'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('alumni.index'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.dashboard'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('mahasiswa.dashboard'));
    }

    /**
     * Test that the public alumni route is accessible.
     */
    public function test_public_alumni_route_accessible(): void
    {
        $response = $this->get('/alumni');
        $response->assertStatus(200);
    }

    /**
     * Test that the alumni statistics route is accessible.
     */
    public function test_alumni_statistics_route_accessible(): void
    {
        $response = $this->get('/alumni/statistics');
        $response->assertStatus(200);
    }
}
