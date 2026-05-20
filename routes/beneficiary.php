<?php

use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\BeneficiaryFamilyController;
use App\Models\BeneficiaryFamily;
use Illuminate\Support\Facades\Route;

Route::name('beneficiary.')
    ->prefix('beneficiary')
    ->group(function () {
        Route::get('/', [BeneficiaryController::class, 'index'])
            ->name('index');
        
        Route::get('/{id}', [BeneficiaryController::class, 'show'])
            ->name('show');

        Route::put('/{id}/update', [BeneficiaryController::class, 'update'])
            ->middleware('role:superadmin')
            ->name('update');

        });

Route::name('beneficiary.family.')
    ->prefix('family')
    ->group(function () {
        Route::get('/{familyId}', [BeneficiaryFamilyController::class, 'show'])
            ->name('show');
        
        Route::put('/{familyId}', [BeneficiaryFamilyController::class, 'update'])
            ->middleware('role:superadmin')
            ->name('update');
    });