<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\UserController,
    ClientController,
    ExportController,
    ReportController,
    DeceasedController,
    ClaimantController,
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

        Route::get('/reports/burial-assistances', [ReportController::class, 'burialAssistances'])
            ->name('reports.burial-assistances');
        Route::match(['get', 'post'], '/reports/deceased', [ReportController::class, 'deceased'])
            ->name('reports.deceased');
        Route::match(['get', 'post'], '/reports/claimants', [ReportController::class, 'claimants'])
            ->name('reports.claimants');

        Route::post('/reports/export/deceased/{startDate}/{endDate}', [DeceasedController::class, 'generatePdfReport'])
            ->name('reports.deceased.pdf');

        Route::post('/reports/export/claimant/{startDate}/{endDate}', [ClaimantController::class, 'generatePdfReport'])
            ->name('reports.claimant.pdf');

        require __DIR__ . '/superadmin.php';
        require __DIR__ . '/admin.php';
        require __DIR__ . '/user.php';
    });