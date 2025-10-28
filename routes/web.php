<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\UserController,
    ClientController,
    ExportController,
    ReportController,
    DeceasedController,
    ClaimantController,
    BurialAssistanceController,
    ChequeController,
};

// Route::get('/', function () {
//     return view('landingpage');
// })->name('landing.page');

Route::get('/', function () {
    return view('metronics.landing');
})->name('landing.page');

Route::get('/login', [UserController::class, 'loginPage'])
    ->name('login.page');
Route::post('/login/check', [UserController::class, 'login'])
    ->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

require __DIR__ . '/guest.php';
require __DIR__ . '/api.php';

Route::middleware(['auth', 'route.access'])
    ->group(function () {
        Route::get('/client/latest-tracking', [ClientController::class, 'getLatestTracking'])->name('client.latest-tracking');

        Route::get('/applications/export', [ExportController::class,'applications'])
            ->name('applications.export.all');

        Route::match(['get', 'post'], '/reports/burial-assistances', [ReportController::class, 'burialAssistances'])
            ->name('reports.burial-assistances');
        Route::match(['get', 'post'], '/reports/deceased', [ReportController::class, 'deceased'])
            ->name('reports.deceased');
        Route::match(['get', 'post'], '/reports/claimants', [ReportController::class, 'claimants'])
            ->name('reports.claimants');
        Route::match(['get', 'post'], '/reports/cheques', [ReportController::class, 'cheques'])
            ->name('reports.cheques');

        Route::post('/reports/export/burial-assistances/{startDate}/{endDate}', [BurialAssistanceController::class, 'generatePdfReport'])
            ->name('reports.burial-assistances.pdf');

        Route::post('/reports/export/deceased/{startDate}/{endDate}', [DeceasedController::class, 'generatePdfReport'])
            ->name('reports.deceased.pdf');

        Route::post('/reports/export/claimant/{startDate}/{endDate}', [ClaimantController::class, 'generatePdfReport'])
            ->name('reports.claimant.pdf');

        Route::post('/reports/export/cheques/{startDate}/{endDate}', [ChequeController::class, 'generatePdfReport'])
            ->name('reports.cheques.pdf');

        Route::post('/assignments/{id}/reject/toggle', [BurialAssistanceController::class, 'toggleReject'])
            ->name('assignments.reject.toggle');

        require __DIR__ . '/superadmin.php';
        require __DIR__ . '/admin.php';
        require __DIR__ . '/user.php';
    });