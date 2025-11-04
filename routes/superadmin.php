<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\UserController,
    DashboardController,
    RoleController,
    PermissionController,
    AssistanceController,
    CivilStatusController,
    EducationController,
    NationalityController,
    RelationshipController,
    ReligionController,
    SexController,
    DistrictController,
    BarangayController,
    ModeOfAssistanceController,
    ClientController,
    CmsController,
    SearchController,
    BurialAssistanceController,
    ReportController,
    DeceasedController,
    ClaimantController,
    UserRouteRestrictionController,
};

// super admin role
Route::get('/tracking-activity', [DashboardController::class, 'trackerEvents']);
Route::get('/superadmin/search', [SearchController::class, 'superadmin'])
    ->name('search');

Route::middleware('permission:manage-content')
    ->name('cms.')
    ->prefix('cms')
    ->group(function () {
        Route::get('/barangays', [CmsController::class, 'barangays'])
            ->name('barangays');
        Route::get('/relationships', [CmsController::class, 'relationships'])
            ->name('relationships');
        Route::get('/workflow', [CmsController::class, 'workflow'])
            ->name('workflow');
        Route::get('/handlers', [CmsController::class, 'handlers'])
            ->name('handlers');
        Route::get('/users', [CmsController::class, 'users'])
            ->name('users');
        Route::get('/religions', [CmsController::class, 'religions'])
            ->name('religions');
        
        Route::post('/{type}/store', [CmsController::class, 'storeContent'])
            ->name('store');
            
        Route::post('/{type}/{id}/update', [CmsController::class, 'updateContent'])
            ->name('update');
        
        Route::post('/{type}/{id}/delete', [CmsController::class, 'deleteContent'])
            ->name('delete');
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

Route::middleware('permission:manage-roles')
    ->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index'])
            ->name('permissions');
        
        Route::get('/roles', [RoleController::class, 'index'])
            ->name('roles');
        
        Route::post('/roles/store', [RoleController::class, 'store'])
            ->name('roles.store');
        
        Route::post('/roles/{id}/update', [RoleController::class, 'update'])
            ->name('roles.update');
    });

Route::resource('role', RoleController::class);
Route::resource('permission', PermissionController::class);
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
Route::resource('client', ClientController::class);