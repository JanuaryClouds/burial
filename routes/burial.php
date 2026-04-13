<?php

use App\Http\Controllers\BurialAssistanceController;
use App\Http\Controllers\ClaimantChangeController;
use App\Http\Controllers\ProcessLogController;
use Illuminate\Support\Facades\Route;

Route::name('burial.')
    ->prefix('burial')
    ->group(function () {
        Route::get('/list/{status}', [BurialAssistanceController::class, 'index'])
            ->name('index');

        Route::get('/{id}', [BurialAssistanceController::class, 'show'])
            ->name('show');

        Route::post('/{id}/claimant-change/store', [ClaimantChangeController::class, 'store'])
            ->name('claimant-change.store');

        Route::post('/{id}/claimant-change/{change}/decision', [ClaimantChangeController::class, 'decide'])
            ->middleware('permission:edit-claimant-change-requests')
            ->name('claimant-change.decision');

        Route::get('/{id}/certificate', [BurialAssistanceController::class, 'certificate'])
            ->name('certificate');
    });

Route::name('process-logs.')
    ->prefix('process-logs')
    ->group(function () {
        Route::post('/{id}/addLog/{stepId}', [ProcessLogController::class, 'add'])
            ->middleware('permission:create-updates')
            ->name('store');

        Route::post('/{id}/delete', [ProcessLogController::class, 'delete'])
            ->middleware('permission:delete-updates')
            ->name('delete');
    });
