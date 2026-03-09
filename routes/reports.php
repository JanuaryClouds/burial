<?php

use App\Http\Controllers\BurialAssistanceController;
use App\Http\Controllers\ChequeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FuneralAssistanceController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('permission:view-reports')
    ->name('reports.')
    ->prefix('reports')
    ->group(function () {
        Route::match(['get', 'post'], '/burial-assistances', [ReportController::class, 'burialAssistances'])
            ->name('burial-assistances');
        Route::match(['get', 'post'], '/cheques', [ReportController::class, 'cheques'])
            ->name('cheques');
        Route::match(['get', 'post'], '/funerals', [ReportController::class, 'funerals'])
            ->name('funerals');
        Route::match(['get', 'post'], '/clients', [ReportController::class, 'clients'])
            ->name('clients');

        Route::post('/export/clients/{startDate}/{endDate}', [ClientController::class, 'generatePdfReport'])
            ->name('clients.pdf');

        Route::post('/export/funeral-assistances/{startDate}/{endDate}', [FuneralAssistanceController::class, 'generatePdfReport'])
            ->name('funerals.pdf');

        Route::post('/export/burial-assistances/{startDate}/{endDate}', [BurialAssistanceController::class, 'generatePdfReport'])
            ->name('burial-assistances.pdf');

        Route::post('/export/cheques/{startDate}/{endDate}', [ChequeController::class, 'generatePdfReport'])
            ->name('cheques.pdf');
    });
