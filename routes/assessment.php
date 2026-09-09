<?php

use App\Http\Controllers\AssessmentController;
use App\Livewire\Assessment\Create;
use Illuminate\Support\Facades\Route;

Route::name('assessment.')
    ->prefix('assessment')
    ->group(function () {
        Route::get('/{application}/create', Create::class)
            ->middleware('can:create, \App\Models\Assessment')
            ->name('create');
    });
