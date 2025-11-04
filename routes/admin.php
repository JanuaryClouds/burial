<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\UserController,
    DashboardController,
    AssistanceController,
    SearchController,
    BurialAssistanceController,
    ProcessLogController,
    ClaimantChangeController,
};

// admin role
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Route::get('/admin/search', [SearchController::class, 'admin'])
        //     ->name('search');

        // Burial Applications
        // Route::get('/applications/pending', [BurialAssistanceController::class, 'pending'])
        // ->name('applications.pending');
        // Route::get('/applications/processing', [BurialAssistanceController::class, 'processing'])
        //     ->name('applications.processing');
        // Route::get('/applications/approved', [BurialAssistanceController::class, 'approved'])
        //     ->name('applications.approved');
        // Route::get('/applications/released', [BurialAssistanceController::class, 'released'])
        //     ->name('applications.released');

        // Route::resource('assistance', AssistanceController::class);
    });

Route::get('applications/{status}', [BurialAssistanceController::class, 'applications'])
    ->name('applications');
    
Route::name('application.')
    ->prefix('application')
    ->group(function () {
        Route::get('/{id}', [BurialAssistanceController::class, 'manage'])
            ->name('manage');
        
        Route::post('{id}/claimant-change/{change}/decision', [ClaimantChangeController::class, 'decide'])
            ->name('claimant-change.decision');
            
        Route::post('/{id}/addLog/{stepId}', [ProcessLogController::class, 'add'])
            ->name('addLog');

        Route::post('/{id}/delete/{stepId}', [ProcessLogController::class,'delete'])
            ->name('deleteLog');

        Route::post('/{id}/swa/save', [BurialAssistanceController::class, 'saveSwa'])
            ->name('swa.save');
    });
        
Route::middleware('permission:assign')
    ->group(function () {
        Route::get('/assignments', [BurialAssistanceController::class, 'assignments'])
            ->name('assignments');
        
        Route::post('/assignments/{id}/assign', [BurialAssistanceController::class, 'assign'])
            ->name('assignments.assign');
    });