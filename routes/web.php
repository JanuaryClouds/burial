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
};

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', [UserController::class, 'loginPage'])
    ->name('login.page');
Route::post('/login/check', [UserController::class, 'login'])
    ->name('login');

Route::middleware(['auth'])
    ->group(function () {
        
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
            });

        // admin role
        Route::middleware('role: admin')
            ->prefix('admin')
            ->name('admin.')
            ->group(function () {
                Route::post('/logout', [UserController::class, 'logout'])
                    ->name('logout');
                Route::get('/dashboard', [DashboardController::class, 'index'])
                    ->name('dashboard');
            });
        // user role
        Route::middleware('role:user')
            ->prefix('user')
            ->name('user.')
            ->group(function () {
                Route::post('/logout', [UserController::class, 'logout'])
                    ->name('logout');
                Route::get('/dashboard', [DashboardController::class, 'index'])
                    ->name('dashboard');
            });
    });