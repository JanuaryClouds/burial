<?php

use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\BeneficiaryFamilyController;
use Illuminate\Support\Facades\Route;

Route::name('beneficiary.')
    ->prefix('beneficiary')
    ->controller(BeneficiaryController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');

        Route::get('/create', 'create')
            ->name('create');

        // Route::post('/store', 'store')
        //     ->name('store');

        Route::prefix('/{beneficiary}')
            ->group(function () {
                Route::get('', 'show')
                    ->middleware('can:view,\App\Models\Beneficiary,beneficiary')
                    ->name('show');

                Route::get('/edit', 'edit')
                    ->middleware('can:update,\App\Models\Beneficiary,beneficiary')
                    ->name('edit');
            });

        Route::name('family.')
            ->controller(BeneficiaryFamilyController::class)
            ->prefix('family/')
            ->group(function () {
                Route::prefix('{member}')
                    ->group(function () {
                        Route::get('/edit', 'edit')
                            ->middleware('can:update,\App\Models\BeneficiaryFamily,member')
                            ->name('edit');
                    });
            });
    });
