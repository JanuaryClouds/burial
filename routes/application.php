<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::controller(ApplicationController::class)
    ->name('application.')
    ->prefix('application')
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');

        Route::get('/{application}', 'show')
            ->name('show');
    });