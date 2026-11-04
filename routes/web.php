<?php

use App\Http\Controllers\Auth\PasswordRecoveryController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\CitizenAccessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Middleware\RecoveryPrivacy;
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

Route::controller(PasswordRecoveryController::class)->middleware(RecoveryPrivacy::class)->group(function () {
    Route::get('/admin/forgot-password', 'forgot')->name('password.request');
    Route::post('/admin/forgot-password', 'send')->name('password.email');
    Route::get('/admin/reset-password/{token}', 'form')->name('password.reset');
    Route::post('/admin/reset-password', 'reset')->middleware('throttle:password-reset')->name('password.update');
});

Route::middleware(['auth', 'active.check'])
    ->group(function () {
        Route::view('/profile/password', 'user.password')->name('user.password');
        Route::get('/user/{user}', [UserController::class, 'show'])
            ->name('user.show');

        Route::get('/user/{user}/edit', [UserController::class, 'edit'])
            ->middleware('can:update,user')
            ->name('user.edit');

        Route::get('/checksession', [UserController::class, 'checkSession'])
            ->name('checksession');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/applications/export', [ExportController::class, 'applications'])
            ->middleware('can:create-reports')
            ->middleware('throttle:document-generation')
            ->name('applications.export.all');

        require __DIR__.'/application.php';
        require __DIR__.'/client.php';
        require __DIR__.'/beneficiary.php';
        require __DIR__.'/referral.php';
        require __DIR__.'/interview.php';
        require __DIR__.'/reports.php';
        require __DIR__.'/superadmin.php';
    });
