<?php

use App\Http\Controllers\APIController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('api.check')
    ->name('api.')
    ->group(function () {
        Route::get('/burial-assistances', [APIController::class, 'getBurialAssistances']);
        Route::get('/claimants', [APIController::class, 'claimants']);
        Route::get('/deceased', [APIController::class, 'deceased']);
        Route::get('/cheques', [APIController::class, 'cheques']);
        Route::get('/process-logs', [APIController::class, 'processLogs']);

        Route::post('/test/component/post', [TestController::class, 'post'])
            ->name('test.component.post');
    });
