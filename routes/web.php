<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\BurialAssistanceController;
use App\Http\Controllers\CitizenAccessController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CitizenAccessController::class, 'index'])
    ->name('landing.page');

Route::get('/test/component/{id}', [TestController::class, 'get'])
    ->name('test.component');

Route::post('/test/component/post', [TestController::class, 'post'])
    ->name('test.component.post');

Route::get('/login', [UserController::class, 'loginPage'])
    ->name('login.page');
Route::post('/login/check', [UserController::class, 'login'])
    ->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

require __DIR__.'/guest.php';
require __DIR__.'/client.php';
require __DIR__.'/burial.php';
require __DIR__.'/funeral.php';
require __DIR__.'/api.php';

Route::middleware(['auth'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/client/latest-tracking', [ClientController::class, 'getLatestTracking'])->name('client.latest-tracking');

        Route::get('/applications/export', [ExportController::class, 'applications'])
            ->name('applications.export.all');

        Route::post('/application/{id}/reject/toggle', [BurialAssistanceController::class, 'toggleReject'])
            ->middleware('permission:reject-applications')
            ->name('application.reject.toggle');

        Route::get('/activity-logs', [ActivityLogController::class, 'index'])
            ->middleware('permission:view-logs')
            ->name('activity.logs');

        require __DIR__.'/reports.php';
        require __DIR__.'/superadmin.php';
        require __DIR__.'/admin.php';
        require __DIR__.'/user.php';
    });
