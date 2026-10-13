<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModeOfAssistanceRequest;
use App\Models\ModeOfAssistance;
use App\Services\DatatableService;
use App\Services\ModeOfAssistanceService;
use Illuminate\Support\Facades\Auth;

class ModeOfAssistanceController extends Controller
{
    public function __construct(
        protected ModeOfAssistanceService $modeOfAssistanceServices,
        protected DatatableService $datatableService
    ) {}

    public function index()
    {
        $data = ModeOfAssistance::index()
            ->get()
            ->map(function ($modeOfAssistance) {
                return [
                    'id' => $modeOfAssistance->id,
                    'name' => $modeOfAssistance->name,
                    'show_route' => route('mode_of_assistances.edit', $modeOfAssistance->id),
                ];
            });

        if (request()->expectsJson()) {
            return $this->datatableService->ajax($data);
        }

        return view('mode-of-assistance.index', [
            'pageTitle' => 'Mode of Assistances',
            'columns' => $this->datatableService->getColumns($data),
        ]);
    }

    public function edit(ModeOfAssistance $modeOfAssistance)
    {
        return view('mode-of-assistance.edit', [
            'pageTitle' => 'Edit ' . $modeOfAssistance->name,
            'modeOfAssistance' => $modeOfAssistance
        ]);
    }
}
