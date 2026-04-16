<?php

use App\Http\Controllers\BeneficiaryController;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Route;

Route::get('/beneficiary', [BeneficiaryController::class, 'index'])
    ->can('view', Beneficiary::class)
    ->name('beneficiary.index');
