<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ClientController,
};

Route::get('/general-intake-form', [ClientController::class, 'create'])
    ->name('general.intake.form');

Route::post('/general-intake-form/store', [ClientController::class, 'store'])
    ->name('general.intake.form.store');

Route::get('/history', [ClientController::class, 'history'])
    ->name('client.history');

