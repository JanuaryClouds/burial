<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    BurialAssistanceController,
    ClaimantChangeController,
};

// guest routes
Route::name('guest.')
    ->group(function () {
        Route::get('/burial-assistance', [BurialAssistanceController::class, 'view'])
            ->name('burial-assistance.view');
        
        Route::post('/burial-assistance/store', [BurialAssistanceController::class, 'store'])
            ->name('burial-assistance.store');
        
        Route::post('/burial-assistance/tracker', [BurialAssistanceController::class, 'track'])
            ->name('burial-assistance.tracker');

        Route::get('/burial-assistance/tracker/{code}', [BurialAssistanceController::class, 'trackPage'])
            ->name('burial-assistance.track-page');
        
        Route::post('/burial-assistance/{id}/claimant-change', [ClaimantChangeController::class, 'store'])
            ->name('burial-assistance.claimant-change');
        
        Route::get('/burial/request', function () {
            return view('guest.burial_request');
        })->name('burial.request');
    });
