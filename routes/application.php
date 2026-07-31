<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::controller(ApplicationController::class)
    ->name('application.')
    ->prefix('applications')
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');

        Route::get('/create', 'create')
            ->name('create');

        Route::post('/store', 'store')
            ->middleware('throttle:5,1')
            ->name('store');

        Route::prefix('/{application}')
            ->group(function () {
                Route::get('', 'show')
                    ->name('show');
        
                Route::get('/print', 'print')
                    ->name('print');
        
                Route::get('/certificate', 'certificate')
                    ->name('certificate');
            });
    });