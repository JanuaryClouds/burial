<?php

namespace App\Http\Controllers;

use App\Models\ModeOfAssistance;
use App\Services\DataTableService;
use App\Services\ModeOfAssistanceService;

class ModeOfAssistanceController extends Controller
{
    public function __construct(
        protected ModeOfAssistanceService $modeOfAssistanceServices,
        protected DataTableService $dataTableService
    ) {}

    public function index()
    {
        return view('mode-of-assistance.index', [
            'pageTitle' => 'Mode of Assistances',
        ]);
    }

    public function edit(ModeOfAssistance $modeOfAssistance)
    {
        return view('mode-of-assistance.edit', [
            'pageTitle' => 'Edit '.$modeOfAssistance->name,
            'modeOfAssistance' => $modeOfAssistance,
        ]);
    }
}
