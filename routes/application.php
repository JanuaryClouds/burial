<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DocumentTemplateController;
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
                    ->middleware('can:view,application')
                    ->name('show');

                Route::get('/change-claimant', [ApplicationController::class, 'changeClaimant'])
                    ->middleware('can:changeClaimant,application')
                    ->name('change-claimant');

                Route::get('/close', [ApplicationController::class, 'close'])
                    ->middleware('can:view,application')
                    ->name('close');

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
                        Route::get('/templates/{template}/download', [DocumentTemplateController::class, 'download'])
                            ->middleware(['can:view,application', 'can:office-access'])
                            ->name('templates.download');

                        Route::get('/tracker-slip', 'codes')
                            ->middleware('can:view,application')
                            ->name('tracker-slip');

                        Route::get('/print', 'print')
                            ->middleware(['can:view,application', 'can:office-access'])
                            ->name('print');

                        Route::get('/certificate', 'certificate')
                            ->middleware(['can:view,application', 'can:office-access'])
                            ->name('certificate');
                    });
            });
    });
