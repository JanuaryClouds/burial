<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\UserController,
    DashboardController,
    AssistanceController,
    SearchController,
    BurialAssistanceController,
    ProcessLogController,
    ClientController,
    ClaimantChangeController,
    InterviewController,
    FuneralAssistanceController,
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

Route::get('/clients', [ClientController::class, 'index'])
    ->name('clients');

Route::get('/clients/{id}', [ClientController::class, 'view'])
    ->name('clients.view');

Route::get('/clients/{id}/gis-form', [ClientController::class, 'generateGISForm'])
    ->name('clients.gis-form');

Route::post('/clients/{id}/schedule', [InterviewController::class, 'store'])
    ->name('clients.interview.schedule.store');

Route::post('/clients/{id}/schedule/done', [InterviewController::class, 'done'])
    ->name('clients.interview.schedule.done');

Route::post('/cleints/{id}/assessment', [ClientController::class, 'assessment'])
    ->name('clients.assessment.store');

Route::post('/clients/{id}/recommendation', [ClientController::class, 'recommendedService'])
    ->name('clients.recommendation.store');
    
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
            
        Route::get('/{id}/certificate', [BurialAssistanceController::class, 'certificate'])
            ->name('certificate');
    });

Route::get('/funeral-assistances', [FuneralAssistanceController::class, 'index'])
    ->name('funeral-assistances');

Route::get('/funeral-assistances/{id}', [FuneralAssistanceController::class, 'view'])
    ->name('funeral-assistances.view');

Route::get('/funeral-assistances/{id}/approved', [FuneralAssistanceController::class, 'approve'])
    ->name('funeral-assistances.view.approved');

Route::get('/funeral-assistances/{id}/forwarded', [FuneralAssistanceController::class, 'forward'])
    ->name('funeral-assistances.view.forwarded');
    
Route::get('/funeral-assistances/{id}/certificate', [FuneralAssistanceController::class, 'certificate'])
    ->name('funeral-assistances.view.certificate');

Route::middleware('permission:manage-assignments')
    ->group(function () {
        Route::get('/assignments', [BurialAssistanceController::class, 'assignments'])
            ->name('assignments');
        
        Route::post('/assignments/{id}/assign', [BurialAssistanceController::class, 'assign'])
            ->name('assignments.assign');
    });