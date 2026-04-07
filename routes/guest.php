<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/{id}/documents/{filename}', [ImageController::class, 'stream'])
    ->name('clients.documents');

Route::get('/image/{filename}', [ImageController::class, 'get'])
    ->where('filename', '[a-zA-Z0-9_\-\.]+')
    ->name('image');
