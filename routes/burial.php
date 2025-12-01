<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    BurialAssistanceController,
    ClaimantChangeController,
};

// guest routes
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
        
        Route::post('/{id}/claimant-change', [ClaimantChangeController::class, 'store'])
            ->name('burial-assistance.claimant-change');
        
    });
