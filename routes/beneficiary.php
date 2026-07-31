<?php

use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\BeneficiaryFamilyController;
use Illuminate\Support\Facades\Route;

Route::name('beneficiary.')
    ->prefix('beneficiary')
    ->group(function () {
        Route::controller(BeneficiaryController::class)
            ->group(function () {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/create', 'create')
                    ->name('create');

                Route::post('/store', 'store')
                    ->name('store');

                Route::prefix('/{beneficiary}')
                    ->group(function () {
                        Route::get('', 'show')
                            ->name('show');
        
                        Route::get('/edit', 'edit')
                            ->middleware('can:edit,beneficiary')
                        ->name('edit');
                
                        Route::post('/update', 'update')
                            ->middleware('can:update,beneficiary')
                            ->name('update');
                    });
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
                    ->middleware('role:superadmin')
                    ->name('edit');
                    
                Route::post('/update', 'update')
                    ->middleware('role:superadmin')
                    ->middleware('role:superadmin')
                    ->name('update');
            });
    });
