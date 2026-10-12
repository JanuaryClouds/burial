<?php

use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\FuneralAssistanceTypeController;
use App\Http\Controllers\ModeOfAssistanceController;
use App\Http\Controllers\NationalityController;
use App\Http\Controllers\RelationshipController;
use App\Http\Controllers\ReligionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SexController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::middleware('role:superadmin')
    ->group(function () {
        Route::controller(FuneralAssistanceTypeController::class)
            ->prefix('funeral-assistance-types')
            ->name('funeral_assistance_types.')
            ->group(function () {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/{funeral-assistance-type}/edit', 'edit')
                    ->name('edit');
            });

        Route::controller(ModeOfAssistanceController::class)
            ->prefix('mode-of-assistances')
            ->name('mode_of_assistances.')
            ->group(function () {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/{mode-of-assistance}/edit', 'edit')
                    ->name('edit');
            });

        Route::controller(RoleController::class)
            ->prefix('role')
            ->name('role.')
            ->group(function () {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/{role}/edit', 'edit')
                    ->name('edit');
            });

        Route::controller(UserController::class)
            ->prefix('user')
            ->name('user.')
            ->group(function () {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/{user}/edit', 'edit')
                    ->name('edit');
            });
    });

Route::controller(SystemController::class)
    ->prefix('system')
    ->name('system.')
    ->middleware('role:superadmin')
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');

        Route::prefix('/logs')
            ->name('logs.')
            ->group(function () {
                Route::get('/', 'logs')
                    ->name('index');

                Route::get('/{log}', 'showLog')
                    ->name('show');
            });

        Route::get('/settings', 'settings')
            ->name('settings');
    });
