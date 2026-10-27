<?php

use App\Http\Controllers\ReferralController;
use Illuminate\Support\Facades\Route;

Route::controller(ReferralController::class)
    ->name('referral.')
    ->middleware('can:view-record-index')
    ->prefix('referral')
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');
    });
