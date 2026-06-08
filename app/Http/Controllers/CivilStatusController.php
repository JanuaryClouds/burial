<?php

namespace App\Http\Controllers;

use App\Services\CivilStatusService;

class CivilStatusController extends Controller
{
    protected $civilStatusServices;

    public function __construct(CivilStatusService $civilStatusServices)
    {
        $this->civilStatusServices = $civilStatusServices;
    }
}
