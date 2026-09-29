<?php

use App\Http\Controllers\AlumniController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AlumniController as AdminAlumniController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TracerQuestionController;
use App\Http\Controllers\Admin\TracerStudyController as AdminTracerStudyController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\ProfileController;
use App\Http\Controllers\Mahasiswa\ProfileContactController;
use App\Http\Controllers\Mahasiswa\EducationHistoryController;
use App\Http\Controllers\Mahasiswa\SkillController;
use App\Http\Controllers\Mahasiswa\AchievementController;
use App\Http\Controllers\Mahasiswa\TracerStudyController as MahasiswaTracerStudyController;
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

    // Admin alumni CRUD routes (using Profile model)
    // Note: route parameter renamed to 'alumni' for URL compatibility but uses Profile model
    Route::get('/alumni', [AdminAlumniController::class, 'index'])->name('alumni.index');
    Route::get('/alumni/create', [AdminAlumniController::class, 'create'])->name('alumni.create');
    Route::post('/alumni', [AdminAlumniController::class, 'store'])->name('alumni.store');
    Route::get('/alumni/{profile}', [AdminAlumniController::class, 'show'])->name('alumni.show');
    Route::get('/alumni/{profile}/edit', [AdminAlumniController::class, 'edit'])->name('alumni.edit');
    Route::put('/alumni/{profile}', [AdminAlumniController::class, 'update'])->name('alumni.update');
    Route::delete('/alumni/{profile}', [AdminAlumniController::class, 'destroy'])->name('alumni.destroy');

    // Admin Tracer Study routes
    Route::get('/tracer-study', [AdminTracerStudyController::class, 'index'])->name('tracer-study.index');
    Route::get('/tracer-study/{profile}', [AdminTracerStudyController::class, 'show'])->name('tracer-study.show');

    // Admin Statistics routes
    Route::get('/statistics/tracer-study', [StatisticsController::class, 'tracerStudy'])->name('statistics.tracer-study');
    Route::get('/statistics/question', [StatisticsController::class, 'questionStatistics'])->name('statistics.question');

    // Admin Data Mahasiswa routes
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');

    // Admin Kuesioner (Question Management) routes
    Route::get('/kuesioner', [TracerQuestionController::class, 'index'])->name('kuesioner.index');
    Route::get('/kuesioner/create', [TracerQuestionController::class, 'create'])->name('kuesioner.create');
    Route::post('/kuesioner', [TracerQuestionController::class, 'store'])->name('kuesioner.store');
    Route::get('/kuesioner/{question}/edit', [TracerQuestionController::class, 'edit'])->name('kuesioner.edit');
    Route::put('/kuesioner/{question}', [TracerQuestionController::class, 'update'])->name('kuesioner.update');
    Route::delete('/kuesioner/{question}', [TracerQuestionController::class, 'destroy'])->name('kuesioner.destroy');
    Route::patch('/kuesioner/{question}/toggle-status', [TracerQuestionController::class, 'toggleStatus'])->name('kuesioner.toggle-status');
});

// Mahasiswa routes
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Profile Contact routes
    Route::get('/profile/contact/edit', [ProfileContactController::class, 'edit'])->name('profile.contact.edit');
    Route::patch('/profile/contact', [ProfileContactController::class, 'update'])->name('profile.contact.update');

    // Education History routes
    Route::post('/profile/education', [EducationHistoryController::class, 'store'])->name('profile.education.store');
    Route::get('/profile/education/{education}/edit', [EducationHistoryController::class, 'edit'])->name('profile.education.edit');
    Route::patch('/profile/education/{education}', [EducationHistoryController::class, 'update'])->name('profile.education.update');
    Route::delete('/profile/education/{education}', [EducationHistoryController::class, 'destroy'])->name('profile.education.destroy');

    // Skill routes
    Route::post('/profile/skills', [SkillController::class, 'store'])->name('profile.skills.store');
    Route::get('/profile/skills/{skill}/edit', [SkillController::class, 'edit'])->name('profile.skills.edit');
    Route::patch('/profile/skills/{skill}', [SkillController::class, 'update'])->name('profile.skills.update');
    Route::delete('/profile/skills/{skill}', [SkillController::class, 'destroy'])->name('profile.skills.destroy');

    // Achievement routes
    Route::post('/profile/achievements', [AchievementController::class, 'store'])->name('profile.achievements.store');
    Route::get('/profile/achievements/{achievement}/edit', [AchievementController::class, 'edit'])->name('profile.achievements.edit');
    Route::patch('/profile/achievements/{achievement}', [AchievementController::class, 'update'])->name('profile.achievements.update');
    Route::delete('/profile/achievements/{achievement}', [AchievementController::class, 'destroy'])->name('profile.achievements.destroy');

    // Tracer Study routes
    Route::get('/tracer-study', [MahasiswaTracerStudyController::class, 'index'])->name('tracer-study.index');
    Route::get('/tracer-study/create', [MahasiswaTracerStudyController::class, 'create'])->name('tracer-study.create');
    Route::post('/tracer-study/save', [MahasiswaTracerStudyController::class, 'save'])->name('tracer-study.save');
    Route::post('/tracer-study/submit', [MahasiswaTracerStudyController::class, 'submit'])->name('tracer-study.submit');
});

// PUBLIC Alumni routes (read-only, no sensitive data)
// IMPORTANT: Keep specific routes before wildcard routes
Route::get('/alumni/statistics', [AlumniController::class, 'statistics'])->name('alumni.statistics');
Route::get('/alumni', [AlumniController::class, 'index'])->name('alumni.index');
Route::get('/alumni/{profile}', [AlumniController::class, 'show'])->name('alumni.show');
