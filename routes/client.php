<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\ReferralController;
use Illuminate\Support\Facades\Route;

Route::resource('client', ClientController::class)
    ->middleware('permission:view-clients')
    ->only(['index', 'show']);

Route::get('/general-intake-form', [ClientController::class, 'create'])
    ->name('general.intake.form');

Route::post('/general-intake-form/store', [ClientController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('general.intake.form.store');

Route::get('/history', [ClientController::class, 'history'])
    ->name('client.history');

Route::prefix('clients')
    ->name('clients.')
    ->group(function () {
        Route::get('/{id}/gis-form', [ClientController::class, 'generateGISForm'])
            ->name('gis-form');

        Route::post('/{id}/schedule', [InterviewController::class, 'store'])
            ->middleware('permission:create-interview-schedules')
            ->name('interview.schedule.store');

        Route::post('/{id}/schedule/done', [InterviewController::class, 'done'])
            ->name('interview.schedule.done');

        Route::post('/{id}/assessment', [ClientController::class, 'assessment'])
            ->middleware('permission:create-assessments')
            ->name('assessment.store');

        Route::post('/{id}/recommendation', [ClientController::class, 'recommendedService'])
            ->middleware('permission:create-recommendations')
            ->name('recommendation.store');

        Route::post('/{id}/referral', [ReferralController::class, 'store'])
            ->middleware('permission:create-referrals')
            ->name('referral.store');
    });
