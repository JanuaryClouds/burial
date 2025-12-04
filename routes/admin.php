<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    BurialAssistanceController,
    ProcessLogController,
    ClaimantChangeController,
    FuneralAssistanceController,
};

Route::middleware('permission:manage-assignments')
    ->group(function () {
        Route::get('/assignments', [BurialAssistanceController::class, 'assignments'])
            ->name('assignments');
        
        Route::post('/assignments/{id}/assign', [BurialAssistanceController::class, 'assign'])
            ->name('assignments.assign');
    });