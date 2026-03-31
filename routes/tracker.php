<?php

use App\Http\Controllers\TrackingCodeController;
use Illuminate\Support\Facades\Route;

Route::name('tracker.')
    ->prefix('tracker')
    ->group(function () {
        Route::get('', [TrackingCodeController::class, 'show'])
            ->name('show');

        Route::post('/store', [TrackingCodeController::class, 'store'])
            ->name('store');

        Route::post('/match', [TrackingCodeController::class, 'match'])
            ->name('match')
            ->middleware('throttle:60,1');
    });
