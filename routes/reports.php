<?php

use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\ChequeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('permission:view-reports')
    ->name('reports.')
    ->prefix('reports')
    ->group(function () {
        Route::match(['get', 'post'], '/checks', [ReportController::class, 'cheques'])
            ->name('checks');
        Route::match(['get', 'post'], '/clients', [ReportController::class, 'clients'])
            ->name('clients');
        Route::match(['get', 'post'], '/beneficiaries', [ReportController::class, 'beneficiaries'])
            ->name('beneficiaries');

        Route::prefix('export')
            ->middleware('permission:create-reports')
            ->group(function () {
                Route::post('/clients/{startDate}/{endDate}', [ClientController::class, 'generatePdfReport'])
                    ->name('clients.pdf');

                Route::post('/beneficiaries/{startDate}/{endDate}', [BeneficiaryController::class, 'generatePdfReport'])
                    ->name('beneficiaries.pdf');

                Route::post('/checks/{startDate}/{endDate}', [ChequeController::class, 'generatePdfReport'])
                    ->name('checks.pdf');
            });
    });
