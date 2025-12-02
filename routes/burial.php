<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    BurialAssistanceController,
    ClaimantChangeController,
};

// TODO: Update routes
Route::name('burial.')
    ->prefix('burial')
    ->group(function () {
        Route::get('/', [BurialAssistanceController::class, 'view'])
            ->name('view');
        
        Route::post('/store', [BurialAssistanceController::class, 'store'])
            ->name('store');
        
        Route::get('/tracker/{uuid}', [BurialAssistanceController::class, 'tracker'])
            ->name('tracker');
        
        Route::get('/{uuid}/claimant-change', [ClaimantChangeController::class, 'form'])
            ->name('claimant-change');

        Route::post('/{uuid}/claimant-change/store', [ClaimantChangeController::class, 'store'])
            ->name('claimant-change.store');

        Route::post('/{uuid}/claimant-change/{change}/decision', [ClaimantChangeController::class, 'decide'])
            ->name('claimant-change.decision');
    });
