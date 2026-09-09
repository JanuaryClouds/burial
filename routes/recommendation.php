<?php

use App\Http\Controllers\RecommendationController;
use App\Livewire\Recommendation\Create;
use Illuminate\Support\Facades\Route;

Route::prefix('recommendation')
    ->name('recommendation.')
    ->group(function () {
        Route::prefix('/{application}')
            ->group(function () {
                Route::get('/create', Create::class)
                    ->name('create');
            });
    });
