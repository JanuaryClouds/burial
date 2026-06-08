<?php

namespace App\Http\Controllers;

use App\Services\AssistanceService;

class AssistanceController extends Controller
{
    protected $assistanceServices;

    public function __construct(AssistanceService $assistanceServices)
    {
        $this->assistanceServices = $assistanceServices;
    }
}
