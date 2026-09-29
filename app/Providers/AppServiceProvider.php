<?php

namespace App\Providers;

use App\Models\Profile;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Explicitly bind the profile route parameter
        Route::bind('profile', function ($value) {
            return Profile::findOrFail($value);
        });
    }
}
