<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\InterviewController;
use Illuminate\Support\Facades\Route;

Route::resource('client', ClientController::class)
    ->only(['index', 'show']);

Route::prefix('clients')
    ->name('clients.')
    ->group(function () {

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
