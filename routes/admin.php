<?php

use App\Http\Controllers\BurialAssistanceController;
use Illuminate\Support\Facades\Route;

Route::middleware('permission:manage-assignments')
    ->group(function () {
        Route::get('/assignments', [BurialAssistanceController::class, 'assignments'])
            ->name('assignments');

        Route::post('/assignments/{id}/assign', [BurialAssistanceController::class, 'assign'])
            ->name('assignments.assign');
    });
