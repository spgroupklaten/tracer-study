<?php

use App\Http\Controllers\AlumniController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Alumni statistics route - MUST be before resource routes
Route::get('/alumni/statistics', [AlumniController::class, 'statistics'])->name('alumni.statistics');

// Alumni export route
Route::get('/alumni/export', [AlumniController::class, 'export'])->name('alumni.export');

// Alumni routes - placed after specific routes
Route::resource('alumni', AlumniController::class)->parameters([
    'alumni' => 'alumni'
]);
