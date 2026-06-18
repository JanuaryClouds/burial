<?php

namespace App\Http\Controllers;

use App\Services\CivilStatusService;

class CivilStatusController extends Controller
{
    public function __construct(
        protected CivilStatusService $civilStatusServices
    ) {}

    
}
