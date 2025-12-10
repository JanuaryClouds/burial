<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    BurialAssistanceController,
    ClaimantChangeController,
    ProcessLogController,
};

Route::name('burial.')
    ->prefix('burial')
    ->group(function () {
        Route::get('', [BurialAssistanceController::class, 'view'])
            ->name('view');
        
        Route::post('/store', [BurialAssistanceController::class, 'store'])
            ->name('store');
        
        Route::get('/tracker/{uuid}', [BurialAssistanceController::class, 'tracker'])
            ->name('tracker');
        
        // Route::get('/{uuid}/claimant-change', [ClaimantChangeController::class, 'form'])
        //     ->name('claimant-change');

        // Route::post('/{uuid}/claimant-change/store', [ClaimantChangeController::class, 'store'])
        //     ->name('claimant-change.store');

        // Route::post('/{uuid}/claimant-change/{change}/decision', [ClaimantChangeController::class, 'decide'])
        //     ->name('claimant-change.decision');

        Route::middleware(['auth'])
            ->group(function () {
                
                Route::get('/{id}', [BurialAssistanceController::class, 'show'])
                    ->name('show');
                
                Route::post('/{id}/claimant-change/{change}/decision', [ClaimantChangeController::class, 'decide'])
                    ->name('claimant-change.decision');
                    
                Route::post('/{id}/addLog/{stepId}', [ProcessLogController::class, 'add'])
                    ->middleware('permission:add-updates')
                    ->name('addLog');
                    
                Route::post('/{id}/delete/{stepId}', [ProcessLogController::class,'delete'])
                    ->middleware('permission:delete-updates')
                    ->name('deleteLog');

                Route::post('/{id}/swa/save', [BurialAssistanceController::class, 'saveSwa'])
                    ->name('swa.save');
                    
                Route::get('/{id}/certificate', [BurialAssistanceController::class, 'certificate'])
                ->name('certificate');

                Route::get('/list/{status}', [BurialAssistanceController::class, 'index'])
                    ->name('index');
            });
    });
