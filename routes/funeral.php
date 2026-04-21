<?php

use App\Http\Controllers\FuneralAssistanceController;
use App\Models\FuneralAssistance;
use Illuminate\Support\Facades\Route;

Route::prefix('funeral')
    ->name('funeral.')
    ->group(function () {
        Route::get('', [FuneralAssistanceController::class, 'index'])
            ->name('index');

        Route::get('/{id}', [FuneralAssistanceController::class, 'show'])
            ->can('view', FuneralAssistance::class)
            ->name('show');

        Route::get('/{id}/approved', [FuneralAssistanceController::class, 'approve'])
            ->can('update', FuneralAssistance::class)
            ->name('approved');

        Route::get('/{id}/forwarded', [FuneralAssistanceController::class, 'forward'])
            ->can('update', FuneralAssistance::class)
            ->name('forwarded');

        Route::get('/{id}/certificate', [FuneralAssistanceController::class, 'certificate'])
            ->name('certificate');
    });
