<?php

use App\Http\Controllers\RecommendationController;
use Illuminate\Support\Facades\Route;

Route::controller(RecommendationController::class)
    ->prefix('recommendation')
    ->name('recommendation.')
    ->group(function () {
        Route::post('/store/{application}', 'store')
            ->name('store');
    });
