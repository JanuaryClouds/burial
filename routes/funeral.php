<?php

use App\Http\Controllers\FuneralAssistanceController;
use Illuminate\Support\Facades\Route;

Route::prefix('funeral')
    ->name('funeral.')
    ->group(function () {
        Route::get('', [FuneralAssistanceController::class, 'index'])
            ->name('index');

        /**
         * @param  int  $id  ID of the funeral assistance request
         */
        Route::get('/{id}', [FuneralAssistanceController::class, 'show'])
            ->name('show');

        /**
         * @param  int  $id  ID of the funeral assistance request
         */
        Route::get('/{id}/approved', [FuneralAssistanceController::class, 'approve'])
            ->name('approved');

        /**
         * @param  int  $id  ID of the funeral assistance request
         */
        Route::get('/{id}/forwarded', [FuneralAssistanceController::class, 'forward'])
            ->name('forwarded');

        /**
         * @param  int  $id  ID of the funeral assistance request
         */
        Route::get('/{id}/certificate', [FuneralAssistanceController::class, 'certificate'])
            ->name('certificate');
    });
