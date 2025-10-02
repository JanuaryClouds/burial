<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\UserController,
    ClientController,
    ExportController,
};

Route::get('/', function () {
    return view('landingpage');
})->name('landing.page');

Route::get('/login', [UserController::class, 'loginPage'])
    ->name('login.page');
Route::post('/login/check', [UserController::class, 'login'])
    ->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

require __DIR__ . '/guest.php';

Route::middleware(['auth'])
    ->group(function () {
        Route::get('/client/latest-tracking', [ClientController::class, 'getLatestTracking'])->name('client.latest-tracking');

        Route::get('/applications/export', [ExportController::class,'applications'])
            ->name('applications.export.all');

        require __DIR__ . '/superadmin.php';
        require __DIR__ . '/admin.php';
        require __DIR__ . '/user.php';
    });