<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::name('client.')
    ->prefix('clients')
    ->controller(ClientController::class)
    ->group(function() {
        Route::get('/', 'index')
            ->name('index');

            Route::get('/create', 'create')
                ->name('create');
    
            Route::post('/store', 'store')
                ->name('store');

        Route::prefix('/{client}')
            ->group(function() {
                Route::get('', 'show')
                    ->name('show');

                Route::get('/edit', 'edit')
                    ->middleware('can:edit,client')
                    ->name('edit');
                    
                Route::post('/update', 'update')
                    ->middleware('can:update,client')
                    ->name('update');
            });
    });

