<?php

use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\BeneficiaryFamilyController;
use App\Livewire\Beneficiary\Create;
use Illuminate\Support\Facades\Route;

Route::name('beneficiary.')
    ->prefix('beneficiary')
    ->group(function () {
        Route::get('/', [BeneficiaryController::class, 'index'])
            ->name('index');

        Route::get('/create', Create::class)
            ->name('create');

        // Route::post('/store', 'store')
        //     ->name('store');

        Route::prefix('/{beneficiary}')
            ->group(function () {
                Route::get('', [BeneficiaryController::class, 'show'])
                    ->name('show');

                Route::get('/edit', [BeneficiaryController::class, 'edit'])
                    ->middleware('can:update,beneficiary')
                    ->name('edit');

                Route::post('/update', [BeneficiaryController::class, 'update'])
                    ->middleware('can:update,beneficiary')
                    ->name('update');
            });
    });

Route::name('family.')
    ->controller(BeneficiaryFamilyController::class)
    ->prefix('family/')
    ->group(function () {
        Route::get('', 'create')
            ->name('create');

        Route::post('/store', 'store')
            ->name('store');

        Route::prefix('{member}')
            ->group(function () {
                Route::get('', 'show')
                    ->name('show');

                Route::get('/edit', 'edit')
                    ->middleware('can:update,member')
                    ->name('edit');

                Route::post('/update', 'update')
                    ->middleware('can:update,member')
                    ->name('update');
            });
    });
