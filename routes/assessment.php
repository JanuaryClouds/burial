<?php

use App\Http\Controllers\AssessmentController;
use Illuminate\Support\Facades\Route;

Route::controller(AssessmentController::class)
    ->name('assessment.')
    ->prefix('assessment')
    ->group(function () {
        Route::post('store/{application}', 'store')
            ->name('store');
    });
