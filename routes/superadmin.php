<?php

use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\BurialAssistanceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\FuneralAssistanceController;
use App\Http\Controllers\HandlerController;
use App\Http\Controllers\ModeOfAssistanceController;
use App\Http\Controllers\NationalityController;
use App\Http\Controllers\RelationshipController;
use App\Http\Controllers\ReligionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SexController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::middleware('role:superadmin')
    ->group(function () {
        Route::resource('client', ClientController::class)
            ->only(['update']);
        Route::resource('burial', BurialAssistanceController::class)
            ->only(['update']);
        Route::resource('funeral', FuneralAssistanceController::class)
            ->only(['update']);
        Route::resource('workflowstep', WorkflowController::class)
            ->only(['index', 'edit', 'update']);
        Route::resource('handler', HandlerController::class)
            ->only(['index', 'edit', 'update']);
        // Route::resource('assistance', AssistanceController::class);
        // Route::resource('civil', CivilStatusController::class);
        Route::resource('education', EducationController::class)
            ->only(['index', 'store', 'edit', 'update', 'destroy']);
        Route::post('/education/{id}/restore', [EducationController::class, 'restore'])
            ->name('education.restore');

        Route::resource('nationality', NationalityController::class)
            ->only(['index', 'store', 'edit', 'update', 'destroy']);
        Route::post('/nationality/{id}/restore', [NationalityController::class, 'restore'])
            ->name('nationality.restore');

        Route::resource('relationship', RelationshipController::class)
            ->only(['index', 'store', 'edit', 'update', 'destroy']);
        Route::post('/relationship/{id}/restore', [RelationshipController::class, 'restore'])
            ->name('relationship.restore');

        Route::resource('religion', ReligionController::class)
            ->only(['index', 'store', 'edit', 'update', 'destroy']);
        Route::post('/religion/{id}/restore', [ReligionController::class, 'restore'])
            ->name('religion.restore');
        // Route::resource('sex', SexController::class);
        // Route::resource('district', DistrictController::class);
        // Route::resource('moa', ModeOfAssistanceController::class);
        Route::resource('user', UserController::class)
            ->only(['index', 'store']);
        // Route::resource('user', UserController::class)
        //     ->only(['store']);
    });

Route::resource('role', RoleController::class)
    ->only(['index', 'store', 'edit', 'update'])
    ->middleware(['permission:edit-roles']);

Route::name('system.')
    ->prefix('system')
    ->middleware('role:superadmin')
    ->group(function () {
        Route::get('/', [SystemSettingController::class, 'index'])
            ->name('index');

        Route::post('/update', [SystemSettingController::class, 'update'])
            ->name('update');
    });
