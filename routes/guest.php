<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/general-intake-form', [ClientController::class, 'create'])
    ->name('general.intake.form');

Route::post('/general-intake-form/store', [ClientController::class, 'store'])
    ->name('general.intake.form.store');

Route::get('/history', [ClientController::class, 'history'])
    ->name('client.history');

Route::get('/{id}/documents/{filename}', [ImageController::class, 'stream'])
    ->name('clients.documents');
