<?php

use App\Http\Controllers\AlumniController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AlumniController as AdminAlumniController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes (Breeze)
require __DIR__.'/auth.php';

// Dashboard dispatcher - redirects based on role
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Admin alumni export - MUST be before resource routes to avoid {alumni} parameter capture
    Route::get('/alumni/export', [AdminAlumniController::class, 'export'])->name('alumni.export');
    
    // Admin alumni CRUD routes (protected)
    Route::resource('alumni', AdminAlumniController::class)->parameters([
        'alumni' => 'alumni'
    ]);
});

// Mahasiswa routes
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
});

// PUBLIC Alumni routes (read-only, no sensitive data)
// IMPORTANT: Keep specific routes before wildcard routes
Route::get('/alumni/statistics', [AlumniController::class, 'statistics'])->name('alumni.statistics');
Route::get('/alumni', [AlumniController::class, 'index'])->name('alumni.index');
Route::get('/alumni/{alumni}', [AlumniController::class, 'show'])->name('alumni.show');
