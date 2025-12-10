<?php

use App\Http\Controllers\FuneralAssistanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ClientController,
    ReportController,
    DeceasedController,
    ClaimantController,
    BurialAssistanceController,
    ChequeController,
};

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
        Route::match(['get', 'post'], '/funerals', [ReportController::class, 'funerals'])
            ->name('funerals');
        Route::match(['get', 'post'], '/clients', [ReportController::class, 'clients'])
            ->name('clients');
        Route::post('/generate', [ReportController::class, 'generate'])
            ->name('reports.generate');

        Route::post('/export/clients/{startDate}/{endDate}', [ClientController::class, 'generatePdfReport'])
            ->name('clients.pdf');

        Route::post('/export/funeral-assistances/{startDate}/{endDate}', [FuneralAssistanceController::class, 'generatePdfReport'])
            ->name('funerals.pdf');

        Route::post('/export/burial-assistances/{startDate}/{endDate}', [BurialAssistanceController::class, 'generatePdfReport'])
            ->name('burial-assistances.pdf');

        Route::post('/export/deceased/{startDate}/{endDate}', [DeceasedController::class, 'generatePdfReport'])
            ->name('deceased.pdf');

        Route::post('/export/claimant/{startDate}/{endDate}', [ClaimantController::class, 'generatePdfReport'])
            ->name('claimants.pdf');

        Route::post('/export/cheques/{startDate}/{endDate}', [ChequeController::class, 'generatePdfReport'])
            ->name('cheques.pdf');
    });