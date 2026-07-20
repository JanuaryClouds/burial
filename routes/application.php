<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::controller(ApplicationController::class)
    ->name('application.')
    ->prefix('applications')
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');

        Route::get('/{application}', 'show')
            ->name('show');

        Route::get('/{application}/print', 'print')
            ->name('print');

        Route::get('/{application}/certificate', 'certificate')
            ->name('certificate');
    });