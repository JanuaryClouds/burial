<?php

use App\Http\Controllers\AssistanceController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\BurialAssistanceController;
use App\Http\Controllers\CivilStatusController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\FuneralAssistanceController;
use App\Http\Controllers\HandlerController;
use App\Http\Controllers\ModeOfAssistanceController;
use App\Http\Controllers\NationalityController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RelationshipController;
use App\Http\Controllers\ReligionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SexController;
use App\Http\Controllers\UserRouteRestrictionController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

// super admin role
Route::get('/tracking-activity', [DashboardController::class, 'trackerEvents']);
Route::get('/superadmin/search', [SearchController::class, 'superadmin'])
    ->name('search');

Route::middleware('permission:manage-content')
    ->group(function () {
        Route::resource('client', ClientController::class)
            ->only(['update']);
        Route::resource('burial', BurialAssistanceController::class)
            ->only(['update']);
        Route::resource('funeral', FuneralAssistanceController::class)
            ->only(['update']);
        Route::resource('workflow', WorkflowController::class)
            ->only(['index', 'edit', 'update']);
        Route::resource('handler', HandlerController::class)
            ->only(['index', 'edit', 'update']);
        Route::resource('user', UserController::class)
            ->only(['index', 'store', 'edit', 'update']);
        Route::resource('role', RoleController::class)
            ->only(['index', 'store', 'edit', 'update']);
        Route::resource('permission', PermissionController::class)
            ->only(['index']);
        Route::resource('assistance', AssistanceController::class);
        Route::resource('civil', CivilStatusController::class);
        Route::resource('education', EducationController::class);
        Route::resource('nationality', NationalityController::class);
        Route::resource('relationship', RelationshipController::class);
        Route::resource('religion', ReligionController::class);
        Route::resource('sex', SexController::class);
        Route::resource('district', DistrictController::class);
        Route::resource('barangay', BarangayController::class);
        Route::resource('moa', ModeOfAssistanceController::class);
    });

Route::middleware('permission:manage-content')
    ->name('cms.')
    ->prefix('cms')
    ->group(function () {
        // Route::get('/barangays', [CmsController::class, 'barangays'])
        //     ->name('barangays');
        // Route::get('/relationships', [CmsController::class, 'relationships'])
        //     ->name('relationships');
        // Route::get('/workflow', [CmsController::class, 'workflow'])
        //     ->name('workflow');
        // Route::get('/handlers', [CmsController::class, 'handlers'])
        //     ->name('handlers');
        // Route::get('/users', [CmsController::class, 'users'])
        //     ->name('users');
        // Route::get('/religions', [CmsController::class, 'religions'])
        //     ->name('religions');

        // Route::post('/{type}/store', [CmsController::class, 'storeContent'])
        //     ->name('store');

        // Route::post('/{type}/{id}/update', [CmsController::class, 'updateContent'])
        //     ->name('update');

        // Route::post('/{type}/{id}/delete', [CmsController::class, 'deleteContent'])
        //     ->name('delete');
    });

Route::middleware('permission:manage-accounts')
    ->prefix('users')
    ->name('users.')
    ->group(function () {
        Route::get('/{userId}', [UserRouteRestrictionController::class, 'manage'])
            ->name('manage');

        Route::post('/{userId}/restrictions/edit', [UserRouteRestrictionController::class, 'update'])
            ->name('restrictions.update');
    });

// Route::middleware('permission:manage-roles')
//     ->group(function () {
//         Route::get('/permissions', [PermissionController::class, 'index'])
//             ->name('permissions');

//         Route::get('/roles', [RoleController::class, 'index'])
//             ->name('roles');

//         Route::post('/roles/store', [RoleController::class, 'store'])
//             ->name('roles.store');

//         Route::post('/roles/{id}/update', [RoleController::class, 'update'])
//             ->name('roles.update');
//     });
