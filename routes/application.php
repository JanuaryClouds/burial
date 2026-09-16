<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ImageController;
use App\Livewire\Application\Create;
use App\Livewire\Application\Index;
use App\Livewire\Application\Search;
use App\Livewire\Application\Show;
use App\Livewire\Assessment\Create as AssessmentCreate;
use App\Livewire\Recommendation\Create as RecommendationCreate;
use Illuminate\Support\Facades\Route;

Route::name('application.')
    ->prefix('application')
    ->group(function () {
        Route::get('/', [ApplicationController::class, 'index'])
            ->name('index');

        Route::get('/create', Create::class)
            ->middleware('can:create,\App\Models\Application')
            ->name('create');

        Route::get('/search', Search::class)
            ->middleware('can:viewAny,\App\Models\Application')
            ->name('search');

        Route::prefix('/{application}')
            ->group(function() {
                Route::get('', Show::class)
                    ->middleware('can:view,\App\Models\Application,application')
                    ->name('show');

                Route::get('/image/{filename}', [ImageController::class, 'get'])
                    ->where('filename', '[a-zA-Z0-9_\-\.]+')
                    ->name('image');

                Route::get('/image/{filename}/webView', [ImageController::class, 'webView'])
                    ->where('filename', '[a-zA-Z0-9_\-\.]+')
                    ->name('image.webView');

                Route::name('assessment.')
                    ->prefix('assessment')
                    ->group(function() {
                        Route::get('/create', AssessmentCreate::class)
                            ->middleware('can:create,\App\Models\Assessment,application')
                            ->name('create');
                    });

                Route::name('recommendation.')
                    ->prefix('recommendation')
                    ->group(function() {
                        Route::get('/create', RecommendationCreate::class)
                            ->middleware('can:create,\App\Models\Recommendation,application')
                            ->name('create');
                    });

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
