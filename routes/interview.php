<?php

use App\Http\Controllers\InterviewController;
use Illuminate\Support\Facades\Route;

Route::controller(InterviewController::class)
    ->name('interview.')
    ->prefix('interview')
    ->group(function () {
        Route::get('/', 'index')->name('index');

        Route::post('/store/{client}', 'store')->name('store');
    });
