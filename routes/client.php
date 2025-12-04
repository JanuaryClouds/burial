<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ClientController,
    InterviewController,
};


Route::prefix('clients')
    ->name('clients.')
    ->group(function () {
        Route::get('', [ClientController::class, 'index'])
            ->name('index');
        
        Route::get('/{id}', [ClientController::class, 'view'])
            ->name('view');
        
        Route::get('/{id}/gis-form', [ClientController::class, 'generateGISForm'])
            ->name('gis-form');
        
        Route::post('/{id}/schedule', [InterviewController::class, 'store'])
            ->name('interview.schedule.store');
        
        Route::post('/{id}/schedule/done', [InterviewController::class, 'done'])
            ->name('interview.schedule.done');
        
        Route::post('/{id}/assessment', [ClientController::class, 'assessment'])
            ->name('assessment.store');
        
        Route::post('/{id}/recommendation', [ClientController::class, 'recommendedService'])
            ->name('recommendation.store');
    });
