<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\UserController,
    DashboardController,
    RoleController,
    PermissionController,
};

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', [UserController::class, 'loginPage'])
    ->name('login.page');
Route::post('/login/check', [UserController::class, 'login'])
    ->name('login');

Route::middleware(['auth'])
    ->group(function () {
        
        // super admin role
        Route::middleware('role:superadmin')
            ->prefix('superadmin')
            ->name('superadmin.')
            ->group(function () {
                Route::post('/logout', [UserController::class, 'logout'])
                    ->name('logout');
                Route::get('/dashboard', [DashboardController::class, 'index'])
                    ->name('dashboard');
                Route::resource('role', RoleController::class);
                Route::resource('permission', PermissionController::class);
            });

        // admin role
        Route::middleware('role: admin')
            ->prefix('admin')
            ->name('admin.')
            ->group(function () {
                Route::post('/logout', [UserController::class, 'logout'])
                    ->name('logout');
                Route::get('/dashboard', [DashboardController::class, 'index'])
                    ->name('dashboard');
            });
        // user role
        Route::middleware('role:user')
            ->prefix('user')
            ->name('user.')
            ->group(function () {
                Route::post('/logout', [UserController::class, 'logout'])
                    ->name('logout');
                Route::get('/dashboard', [DashboardController::class, 'index'])
                    ->name('dashboard');
            });
    });