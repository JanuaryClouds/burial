<?php

use App\Http\Controllers\ApplicationController;
use App\Livewire\Application\Create;
use App\Livewire\Application\Index;
use App\Livewire\Application\Search;
use App\Livewire\Application\Show;
use Illuminate\Support\Facades\Route;

Route::name('application.')
    ->prefix('applications')
    ->group(function () {
        Route::get('/', [ApplicationController::class, 'index'])
            ->name('index');

        Route::livewire('/create', Create::class)
            ->name('create');

        Route::livewire('/search', Search::class)
            ->name('search');

        Route::prefix('/{application}')
            ->group(function() {
                Route::livewire('', Show::class)
                    ->name('show');

                Route::controller(ApplicationController::class)
                    ->group(function () {
                        Route::get('/tracker-slip', 'codes')
                            ->name('tracker-slip');
        
                        Route::get('/print', 'print')
                            ->name('print');
        
                        Route::get('/certificate', 'certificate')
                            ->name('certificate');
                    });
            });
    });

// Route::controller(ApplicationController::class)
//     ->name('application.')
//     ->prefix('applications')
//     ->group(function () {
//         Route::get('/', 'index')
//             ->name('index');

//         Route::get('/create', 'create')
//             ->name('create');

//         Route::get('/search', 'search')
//             ->name('search');

//         Route::post('/store', 'store')
//             ->middleware('throttle:5,1')
//             ->name('store');

//         Route::prefix('/{application}')
//             ->group(function () {
//                 // Route::get('', 'show')
//                 //     ->name('show');

//                 Route::get('/tracker-slip', 'codes')
//                     ->name('tracker-slip');

//                 Route::get('/print', 'print')
//                     ->name('print');

//                 Route::get('/certificate', 'certificate')
//                     ->name('certificate');
//             });
//     });
