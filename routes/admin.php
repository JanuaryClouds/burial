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
Route::middleware('role:admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::post('/logout', [UserController::class, 'logout'])
            ->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'admin'])
            ->name('dashboard');
        // Route::get('/admin/search', [SearchController::class, 'admin'])
        //     ->name('search');

        // Burial Applications
        Route::get('/applications/pending', [BurialAssistanceController::class, 'pending'])
        ->name('applications.pending');
        Route::get('/applications/processing', [BurialAssistanceController::class, 'processing'])
            ->name('applications.processing');
        Route::get('/applications/approved', [BurialAssistanceController::class, 'approved'])
            ->name('applications.approved');
        Route::get('/applications/released', [BurialAssistanceController::class, 'released'])
            ->name('applications.released');
        Route::get('/applications/history', [BurialAssistanceController::class, 'history'])
            ->name('applications.history');

        Route::post('/applications/{id}/claimant-change/{change}/decision', [ClaimantChangeController::class, 'decide'])
            ->name('application.claimant-change.decision');

        Route::get('/applications/{id}', [BurialAssistanceController::class, 'manage'])
            ->name('applications.manage');
        
        Route::get('/applications/{id}/reject', [BurialAssistanceController::class, 'reject'])
            ->name('applications.reject');
            
        Route::post('/applications/{id}/addLog/{stepId}', [ProcessLogController::class, 'add'])
            ->name('application.addLog');

        Route::post('/applications/{id}/delete/{stepId}', [ProcessLogController::class,'delete'])
            ->name('application.deleteLog');

        Route::post('/applications/{id}/swa/save', [BurialAssistanceController::class, 'saveSwa'])
            ->name('applications.swa.save');

        // Route::resource('assistance', AssistanceController::class);
    });