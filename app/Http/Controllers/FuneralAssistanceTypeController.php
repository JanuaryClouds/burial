<?php

namespace App\Http\Controllers;

use App\Models\FuneralAssistanceType;
use App\Models\Workflow;
use App\Services\DataTableService;

class FuneralAssistanceTypeController extends Controller
{
    public function __construct(
        protected DataTableService $dataTableServices,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = FuneralAssistanceType::index()
            ->get()
            ->map(function ($item) {
                return [
                    'uuid' => $item->uuid,
                    'name' => $item->name,
                    'description' => $item->description,
                    'workflow' => $item->workflow,
                    'show_route' => route('funeral_assistance_types.edit', $item->uuid),
                ];
            });

        if (request()->expectsJson()) {
            return $this->dataTableServices->ajax($data);
        }

        return view('funeral-assistance-types.index', [
            'pageTitle' => 'Funeral Assistance Types',
            'columns' => $this->dataTableServices->getColumns($data),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FuneralAssistanceType $funeralAssistanceType)
    {
        return view('funeral-assistance-types.edit', [
            'pageTitle' => 'Edit Funeral Assistance Type',
            'funeralAssistanceType' => $funeralAssistanceType,
            'workflows' => Workflow::index()->get(),
        ]);
    }
}
