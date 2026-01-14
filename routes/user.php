<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// user role
Route::middleware('role:user')
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'user'])
            ->name('dashboard');
    });
