<?php

use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\WorkflowHistoryController;
use App\Http\Controllers\WorkflowStageController;
use App\Http\Controllers\WorkflowTransitionController;
use Illuminate\Support\Facades\Route;

Route::prefix('workflows/')
    ->name('workflow.')
    ->group(function () {
        Route::controller(WorkflowController::class)
            ->group(function () {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/create', 'create')
                    ->name('create');

                Route::post('/store', 'store')
                    ->name('store');

                Route::prefix('/{workflow}')
                    ->group(function () {
                        Route::get('', 'show')
                            ->name('show');

                        Route::get('/edit', 'edit')
                            ->name('edit');

                        Route::post('/update', 'update')
                            ->name('update');

                        Route::post('/destroy', 'destroy')
                            ->name('destroy');
                    });
            });

        Route::controller(WorkflowStageController::class)
            ->prefix('stages/')
            ->name('stage.')
            ->group(function () {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('create', 'create')
                    ->name('create');

                Route::post('/store', 'store')
                    ->name('store');

                Route::prefix('/{stage}')
                    ->group(function () {
                        Route::get('', 'show')
                            ->name('show');

                        Route::get('/edit', 'edit')
                            ->name('edit');
        
                        Route::post('/update', 'update')
                            ->name('update');
        
                        Route::post('/destroy', 'destroy')
                            ->name('destroy');
                    });
            });

        Route::controller(WorkflowTransitionController::class)
            ->prefix('transitions/')
            ->name('transition.')
            ->group(function () {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/create', 'create')
                    ->name('create');

                Route::post('/store', 'store')
                    ->name('store');

                Route::prefix('/{transition}')
                    ->group(function () {
                        Route::get('', 'show')
                            ->name('show');

                        Route::get('/edit', 'edit')
                            ->name('edit');
        
                        Route::post('/update', 'update')
                            ->name('update');
        
                        Route::post('/destroy', 'destroy')
                            ->name('destroy');
                    });
            });

        Route::controller(WorkflowHistoryController::class)
            ->prefix('histories/')
            ->name('history.')
            ->group(function () {
                Route::get('/index')
                    ->name('index');

                Route::get('/create')
                    ->name('create');

                Route::post('/store', 'store')
                    ->name('store');

                Route::prefix('/{history}')
                    ->group(function () {
                        Route::get('', 'show')
                            ->name('show');

                        Route::get('/edit', 'edit')
                            ->name('edit');
        
                        Route::post('/update', 'update')
                            ->name('update');
        
                        Route::post('/destroy', 'destroy')
                            ->name('destroy');
                    });
            });
    });