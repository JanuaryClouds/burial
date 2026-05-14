<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/image/{filename}', [ImageController::class, 'get'])
    ->where('filename', '[a-zA-Z0-9_\-\.]+')
    ->name('image');
