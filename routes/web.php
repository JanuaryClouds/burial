<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\CitizenAccessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CitizenAccessController::class, 'index'])
    ->name('landing.page');

Route::get('/sso/callback', [CitizenAccessController::class, 'sso'])
    ->middleware('throttle:5,1')
    ->name('sso');

Route::match(['get', 'post'], '/sso/logout', [CitizenAccessController::class, 'logout'])
    ->name('sso.logout');

Route::get('/admin/login', [UserController::class, 'loginPage'])
    ->name('admin.login');

Route::post('/login/check', [UserController::class, 'login'])
    ->middleware('throttle:3,1')
    ->name('login.check');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'active.check'])
    ->group(function () {
        Route::resource('/user', UserController::class)
            ->only(['edit', 'update']);

        Route::get('/checksession', [UserController::class, 'checkSession'])
            ->name('checksession');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/applications/export', [ExportController::class, 'applications'])
            ->middleware('permission:create-reports')
            ->name('applications.export.all');

        Route::get('/activity-logs', [ActivityLogController::class, 'index'])
            ->middleware('permission:view-logs')
            ->name('activity.logs');

        Route::get('/image/{filename}', [ImageController::class, 'get'])
            ->where('filename', '[a-zA-Z0-9_\-\.]+')
            ->name('image');

        require __DIR__.'/client.php';
        require __DIR__.'/beneficiary.php';
        require __DIR__.'/burial.php';
        require __DIR__.'/funeral.php';
        require __DIR__.'/reports.php';
        require __DIR__.'/superadmin.php';
    });
