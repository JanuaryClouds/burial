<?php

use App\Http\Controllers\ClientController;
use App\Livewire\Client\Create;
use App\Livewire\Client\Show;
use Illuminate\Support\Facades\Route;

Route::name('client.')
    ->controller(ClientController::class)
    ->prefix('client')
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');

        Route::get('/create', 'create')
            ->middleware('can:create,App\Models\Client')
            ->name('create');

        Route::prefix('/{client}')
            ->group(function () {
                Route::get('', 'show')
                    ->middleware('can:view,App\Models\Client,client')
                    ->name('show');

                Route::get('/edit', [ClientController::class, 'edit'])
                    ->middleware('can:update,client')
                    ->name('edit');
            });
    });
