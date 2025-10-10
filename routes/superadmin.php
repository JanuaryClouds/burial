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
Route::middleware('role:superadmin')
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::post('/logout', [UserController::class, 'logout'])
            ->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'superadmin'])
            ->name('dashboard');
        Route::get('/tracking-activity', [DashboardController::class, 'trackerEvents']);
        Route::get('/superadmin/search', [SearchController::class, 'superadmin'])
            ->name('search');

        Route::post('/reports/generate', [ReportController::class, 'generate'])
            ->name('reports.generate');

        Route::get('/assignments', [BurialAssistanceController::class, 'assignments'])
            ->name('assignments');
        Route::post('/assignments/{id}/assign', [BurialAssistanceController::class, 'assign'])
            ->name('assignments.assign');

        Route::get('/cms/barangays', [CmsController::class, 'barangays'])
            ->name('cms.barangays');
        Route::get('/cms/requests', [CmsController::class, 'burialAssistanceRequests'])
            ->name('cms.requests');
        Route::get('/cms/services', [CmsController::class, 'burialServices'])
            ->name('cms.services');
        Route::get('/cms/providers', [CmsController::class, 'burialServiceProviders'])
            ->name('cms.providers');
        Route::get('/cms/relationships', [CmsController::class, 'relationships'])
            ->name('cms.relationships');
        Route::get('/cms/workflow', [CmsController::class, 'workflow'])
            ->name('cms.workflow');
        Route::get('/cms/handlers', [CmsController::class, 'handlers'])
            ->name('cms.handlers');
        Route::get('/cms/users', [CmsController::class, 'users'])
            ->name('cms.users');
        Route::get('/cms/religions', [CmsController::class, 'religions'])
            ->name('cms.religions');

        Route::post('/cms/{type}/store', [CmsController::class, 'storeContent'])
            ->name('cms.store');
            
        Route::post('/cms/{type}/{id}/update', [CmsController::class, 'updateContent'])
            ->name('cms.update');

        Route::post('/cms/{type}/{id}/delete', [CmsController::class, 'deleteContent'])
            ->name('cms.delete');
            
        Route::get('/users/{userId}', [UserRouteRestrictionController::class, 'manage'])
            ->name('user.manage');

        Route::post('/users/{userId}/restrictions/edit', [UserRouteRestrictionController::class, 'update'])
            ->name('user.restrictions.update');

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
    });