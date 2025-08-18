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
    BurialServiceController,
    BurialServiceProviderController,
    BurialAssistanceRequestController,
    CmsController
};

Route::get('/', function () {
    return view('landingpage');
})->name('landing.page');

Route::get('/burial/request', function () {
    return view('guest.burial_request');
})->name('guest.burial.request');

Route::get('/burial/request/success', function () {
    return view('guest.request_submit_success');
})->name('guest.request.submit.success');

Route::post('/burial/request/tracker', [BurialAssistanceRequestController::class, 'track'])
    ->name('guest.request.tracker');

Route::post('/burial/request/store', [BurialAssistanceRequestController::class, 'store'])
    ->name('burial.request.store');

Route::get('/login', [UserController::class, 'loginPage'])
    ->name('login.page');
Route::post('/login/check', [UserController::class, 'login'])
    ->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::middleware(['auth'])
    ->group(function () {
        Route::get('/client/latest-tracking', [ClientController::class, 'getLatestTracking'])->name('client.latest-tracking');
        
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

                Route::post('/cms/{type}/store', [CmsController::class, 'storeContent'])
                    ->name('cms.store');
                    
                Route::post('/cms/{type}/{id}/update', [CmsController::class, 'updateContent'])
                    ->name('cms.update');

                Route::delete('/cms/{type}/{id}/delete', [CmsController::class, 'deleteContent'])
                    ->name('cms.delete');

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

        // admin role
        Route::middleware('role:admin')
            ->prefix('admin')
            ->name('admin.')
            ->group(function () {
                Route::post('/logout', [UserController::class, 'logout'])
                    ->name('logout');
                Route::get('/dashboard', [DashboardController::class, 'admin'])
                    ->name('dashboard');
                    
                // Burial Service
                Route::get('/burial/history', [BurialServiceController::class, 'history'])
                    ->name('burial.history');
                Route::get('/burial/service/new', [BurialServiceController::class, 'new'])
                    ->name('burial.new');
                Route::get('/burial/services/xlsx', [BurialServiceController::class,'exportXlsx'])
                    ->name('burial.service.xlsx');
                Route::get('/burial/service/{id}', [BurialServiceController::class, 'view'])
                    ->name('burial.view');
                Route::post('/burial/service/store', [BurialServiceController::class, 'store'])
                    ->name('burial.store');
                Route::post('/burial/service/{id}/contact', [BurialServiceController::class, 'contact'])
                    ->name('burial.service.contact');
                Route::get('/burial/services/{id}/export', [BurialServiceController::class, 'exportPdf'])
                    ->name('burial.service.print');
                    
                // Burial Service Providers
                Route::get('/burial/providers', [BurialServiceController::class, 'providers'])
                ->name('burial.providers');
                Route::get('/burial/providers/xlsx', [BurialServiceProviderController::class, 'exportXslx'])
                    ->name('burial.provider.xlsx');
                Route::get('/burial/new/provider', [BurialServiceProviderController::class, 'newProvider'])
                ->name('burial.new.provider');
                Route::post('/burial/new/provider/store', [BurialServiceProviderController::class, 'store'])
                    ->name('burial.new.provider.store');
                Route::get('/burial/providers/{id}', [BurialServiceProviderController::class, 'view'])
                    ->name('burial.provider.view');
                Route::put('/burial/providers/{id}/update', [BurialServiceProviderController::class, 'update'])
                    ->name('burial.provider.update');
                Route::post('/burial/providers/{id}/contact', [BurialServiceProviderController::class, 'contact'])
                    ->name('burial.provider.contact');
                Route::get('/burial/providers/{id}/export', [BurialServiceProviderController::class, 'exportPdf'])
                    ->name('burial.provider.print');
                    
                    // Burial Assistance Requests
                Route::get('/burial/requests', [BurialAssistanceRequestController::class, 'index'])
                    ->name('burial.requests');
                Route::get('/burial/requests/xlsx', [BurialAssistanceRequestController::class, 'exportXlsx'])
                    ->name('burial.request.xlsx');
                Route::get('/burial/requests/{uuid}', [BurialAssistanceRequestController::class, 'view'])
                    ->name('burial.request.view');
                Route::put('/burial/requests/{uuid}/update', [BurialAssistanceRequestController::class, 'updateStatus'])
                    ->name('burial.request.update');
                Route::post('/burial/requests/{uuid}/contact', [BurialAssistanceRequestController::class, 'contact'])
                    ->name('burial.request.contact');
                Route::get('/burial/requests/{uuid}/export', [BurialAssistanceRequestController::class, 'exportPdf'])
                    ->name('burial.request.print');

                // Requests to Service
                Route::get('/burial/request/{uuid}/toService', [BurialServiceController::class, 'requestToService'])
                    ->name('burial.request.to.service');
                Route::post('/burial/request/{uuid}/toService/store', [BurialServiceController::class, 'saveRequestAsServiced'])
                    ->name('burial.request.to.service.store');

                
                Route::resource('assistance', AssistanceController::class);
            });

        // user role
        Route::middleware('role:user')
            ->prefix('user')
            ->name('user.')
            ->group(function () {
                Route::post('/logout', [UserController::class, 'logout'])
                    ->name('logout');
                Route::get('/dashboard', [DashboardController::class, 'user'])
                    ->name('dashboard');
            });
    });