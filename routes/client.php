<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\ReferralController;
use App\Models\Interview;
use Illuminate\Support\Facades\Route;

Route::resource('client', ClientController::class)
    ->only(['index', 'show']);

Route::get('/general-intake-form', [ClientController::class, 'create'])
    ->name('general.intake.form');

Route::post('/general-intake-form/store', [ClientController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('general.intake.form.store');

Route::get('/history', [ClientController::class, 'history'])
    ->name('history');

Route::get('/interviews', [InterviewController::class, 'index'])
    ->can('view', Interview::class)
    ->name('interview.index');

Route::prefix('clients')
    ->name('clients.')
    ->group(function () {
        Route::get('/{id}/gis-form', [ClientController::class, 'generateGISForm'])
            ->middleware('permission:view-clients')
            ->name('gis-form');

        Route::post('/{id}/schedule', [InterviewController::class, 'store'])
            ->can('create', Interview::class)
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
