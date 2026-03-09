<?php

use App\Http\Controllers\FuneralAssistanceController;
use Illuminate\Support\Facades\Route;

Route::prefix('funeral')
    ->name('funeral.')
    ->group(function () {
        Route::get('', [FuneralAssistanceController::class, 'index'])
            ->name('index');
        
        Route::get('/{id}', [FuneralAssistanceController::class, 'show'])
            ->name('show');

        Route::get('/{id}/approved', [FuneralAssistanceController::class, 'approve'])
            ->name('approved');
        
        Route::get('/{id}/forwarded', [FuneralAssistanceController::class, 'forward'])
            ->name('forwarded');

        Route::get('/{id}/certificate', [FuneralAssistanceController::class, 'certificate'])
            ->name('certificate');
    });
