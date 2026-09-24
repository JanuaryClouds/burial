<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ImageController;
use App\Livewire\Assessment\Create as AssessmentCreate;
use App\Livewire\Recommendation\Create as RecommendationCreate;
use Illuminate\Support\Facades\Route;

Route::name('application.')
    ->prefix('application')
    ->group(function () {
        Route::get('/', [ApplicationController::class, 'index'])
            ->name('index');

        Route::get('/create', [ApplicationController::class, 'create'])
            ->middleware('can:create,\App\Models\Application')
            ->name('create');

        Route::get('/search', [ApplicationController::class, 'search'])
            ->middleware('can:viewAny,\App\Models\Application')
            ->name('search');

        Route::prefix('/{application}')
            ->group(function () {
                Route::get('', [ApplicationController::class, 'show'])
                    ->middleware('can:view,\App\Models\Application,application')
                    ->name('show');

                Route::get('/stop', [ApplicationController::class, 'stop'])
                    ->name('stop');

                Route::prefix('image')
                    ->name('image.')
                    ->controller(ImageController::class)
                    ->group(function () {
                        Route::get('/{filename}', 'get')
                            ->where('filename', '[a-zA-Z0-9_\-\.]+')
                            ->name('get');

                        Route::get('/{filename}/webView', 'webView')
                            ->where('filename', '[a-zA-Z0-9_\-\.]+')
                            ->name('webView');
                    });

                Route::name('assessment.')
                    ->prefix('assessment')
                    ->group(function () {
                        Route::get('/create', AssessmentCreate::class)
                            ->middleware('can:create,\App\Models\Assessment,application')
                            ->name('create');
                    });

                Route::name('recommendation.')
                    ->prefix('recommendation')
                    ->group(function () {
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
