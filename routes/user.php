<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\UserController,
    DashboardController,
};

// user role
Route::middleware('role:user')
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::post('/logout', [UserController::class, 'logout'])
            ->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'user'])
            ->name('dashboard');
    });