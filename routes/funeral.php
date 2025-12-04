<?php

use App\Http\Controllers\FuneralAssistanceController;
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
    TestController,
    CitizenAccessController,
};

Route::prefix('funeral')
    ->name('funeral.')
    ->group(function () {
        Route::get('', [FuneralAssistanceController::class, 'index'])
            ->name('index');
        
        Route::get('/{id}', [FuneralAssistanceController::class, 'view'])
            ->name('view');
        
        Route::get('/{id}/approved', [FuneralAssistanceController::class, 'approve'])
            ->name('approved');
        
        Route::get('/{id}/forwarded', [FuneralAssistanceController::class, 'forward'])
            ->name('forwarded');
            
        Route::get('/{id}/certificate', [FuneralAssistanceController::class, 'certificate'])
            ->name('certificate');
    });
