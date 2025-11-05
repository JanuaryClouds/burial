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
    ActivityLogController,
    DashboardController,
};

// Route::get('/', function () {
//     return view('landingpage');
// })->name('landing.page');

Route::get('/', function () {
    return view('landing');
})->name('landing.page');

Route::get('/login', [UserController::class, 'loginPage'])
    ->name('login.page');
Route::post('/login/check', [UserController::class, 'login'])
    ->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

require __DIR__ . '/guest.php';
require __DIR__ . '/api.php';

Route::middleware(['auth',])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/client/latest-tracking', [ClientController::class, 'getLatestTracking'])->name('client.latest-tracking');

        Route::get('/applications/export', [ExportController::class,'applications'])
            ->name('applications.export.all');

        Route::middleware('permission:view-reports')
            ->name('reports.')
            ->prefix('reports')
            ->group(function () {
                Route::match(['get', 'post'], '/burial-assistances', [ReportController::class, 'burialAssistances'])
                    ->name('burial-assistances');
                Route::match(['get', 'post'], '/deceased', [ReportController::class, 'deceased'])
                    ->name('deceased');
                Route::match(['get', 'post'], '/claimants', [ReportController::class, 'claimants'])
                    ->name('claimants');
                Route::match(['get', 'post'], '/cheques', [ReportController::class, 'cheques'])
                    ->name('cheques');
                Route::post('/generate', [ReportController::class, 'generate'])
                    ->name('reports.generate');
        
                Route::post('/export/burial-assistances/{startDate}/{endDate}', [BurialAssistanceController::class, 'generatePdfReport'])
                    ->name('burial-assistances.pdf');
        
                Route::post('/export/deceased/{startDate}/{endDate}', [DeceasedController::class, 'generatePdfReport'])
                    ->name('deceased.pdf');
        
                Route::post('/export/claimant/{startDate}/{endDate}', [ClaimantController::class, 'generatePdfReport'])
                    ->name('claimant.pdf');
        
                Route::post('/export/cheques/{startDate}/{endDate}', [ChequeController::class, 'generatePdfReport'])
                    ->name('cheques.pdf');
        });

        Route::post('/application/{id}/reject/toggle', [BurialAssistanceController::class, 'toggleReject'])
            ->middleware('permission:reject-applications')
            ->name('application.reject.toggle');

        Route::get('/activity-logs', [ActivityLogController::class, 'index'])
            ->middleware('permission:view-logs')
            ->name('activity.logs');

        require __DIR__ . '/superadmin.php';
        require __DIR__ . '/admin.php';
        require __DIR__ . '/user.php';
    });