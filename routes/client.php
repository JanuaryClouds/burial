<?php

use App\Http\Controllers\ClientController;
use App\Livewire\Client\Create;
use Illuminate\Support\Facades\Route;

Route::name('client.')
    ->prefix('client')
    ->group(function () {
        Route::get('/', [ClientController::class, 'index'])
            ->name('index');

        Route::get('/create', Create::class)
            ->middleware('can:create,App\Models\Client')
            ->name('create');

        Route::prefix('/{client}')
            ->group(function () {
                Route::get('', [ClientController::class, 'show'])
                    ->name('show');

                Route::get('/edit', [ClientController::class, 'edit'])
                    ->middleware('can:update,client')
                    ->name('edit');

                Route::post('/update', [ClientController::class, 'update'])
                    ->middleware('can:update,client')
                    ->name('update');
            });
    });
