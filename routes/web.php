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
};

Route::get('/', function () {
    return view('auth.login');
});

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
                Route::get('/dashboard', [DashboardController::class, 'index'])
                    ->name('dashboard');
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
                Route::get('/burial/history', [BurialServiceController::class, 'history'])
                ->name('burial.history');
                Route::get('/burial/new', [BurialServiceController::class, 'new'])->name('burial.new');
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