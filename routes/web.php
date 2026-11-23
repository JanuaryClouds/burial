<?php

use App\Http\Controllers\Auth\InvitationController;
use App\Http\Controllers\Auth\MfaController;
use App\Http\Controllers\Auth\PasswordRecoveryController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\CitizenAccessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\WalkInIntakeController;
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
    ->name('login.check');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::middleware(RecoveryPrivacy::class)->group(function () {
    Route::get('/admin/mfa', [MfaController::class, 'show'])->name('mfa.show')->block(30, 10);
    Route::post('/admin/mfa', [MfaController::class, 'submit'])->name('mfa.submit')->block(30, 10);
    Route::match(['get', 'post'], '/profile/mfa', [MfaController::class, 'manage'])
        ->middleware('auth')->name('mfa.manage')->block(30, 10);
});

Route::middleware([RecoveryPrivacy::class, 'guest', 'throttle:invitation-accept'])->group(function () {
    Route::get('/admin/invitations/{token}', [InvitationController::class, 'form'])->name('invitation.form');
    Route::post('/admin/invitations/{token}', [InvitationController::class, 'accept'])->name('invitation.accept');
});

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
        Route::prefix('walk-in')->name('walk-in.')->middleware('can:walk-in-enabled')->controller(WalkInIntakeController::class)->group(function () {
            Route::get('claim', 'claimForm')->middleware(['can:view-walk-in-claim', RecoveryPrivacy::class])->name('claim');
            Route::post('claim', 'claim')->middleware(['can:claim-walk-in', 'throttle:walk-in-claim', RecoveryPrivacy::class])->name('claim.store');
            Route::middleware('can:walk-in-intake')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::get('receipt', 'receipt')->middleware(RecoveryPrivacy::class)->name('receipt');
                Route::get('{intake}/edit', 'edit')->name('edit');
                Route::post('{intake}/claim-code', 'replaceCode')->middleware(['throttle:5,1', RecoveryPrivacy::class])->name('code.replace');
            });
        });
        require __DIR__.'/client.php';
        require __DIR__.'/beneficiary.php';
        require __DIR__.'/referral.php';
        require __DIR__.'/interview.php';
        require __DIR__.'/reports.php';
        require __DIR__.'/superadmin.php';
    });
