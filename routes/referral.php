<?php

use App\Http\Controllers\ReferralController;
use Illuminate\Support\Facades\Route;

Route::controller(ReferralController::class)
    ->name('referral.')
    ->prefix('referral')
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');

        Route::post('/{application}/store', 'store')
            ->name('store');
    });
