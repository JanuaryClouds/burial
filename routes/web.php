<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\BurialAssistanceController;
use App\Http\Controllers\CitizenAccessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CitizenAccessController::class, 'index'])
    ->name('landing.page');

Route::get('/sso/callback', [CitizenAccessController::class, 'sso'])
    ->name('sso');

Route::match(['get', 'post'], '/sso/logout', [CitizenAccessController::class, 'logout'])
    ->name('sso.logout');

// Route::get('/test/component/{id}', [TestController::class, 'get'])
//     ->name('test.component');

Route::get('/login', [UserController::class, 'loginPage'])
    ->name('login');
Route::post('/login/check', [UserController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.check');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

require __DIR__.'/guest.php';

Route::middleware(['auth'])
    ->group(function () {
        Route::get('/checksession', [UserController::class, 'checkSession'])
            ->name('checksession');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/applications/export', [ExportController::class, 'applications'])
            ->middleware('permission:create-reports')
            ->name('applications.export.all');

        Route::post('/application/{id}/reject/toggle', [BurialAssistanceController::class, 'toggleReject'])
            ->middleware('permission:create-updates')
            ->name('application.reject.toggle');

        Route::get('/activity-logs', [ActivityLogController::class, 'index'])
            ->middleware('permission:view-logs')
            ->name('activity.logs');

        require __DIR__.'/client.php';
        require __DIR__.'/beneficiary.php';
        require __DIR__.'/burial.php';
        require __DIR__.'/funeral.php';
        require __DIR__.'/reports.php';
        require __DIR__.'/superadmin.php';
    });
