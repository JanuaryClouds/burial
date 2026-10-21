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
        return view('funeral-assistance-type.index', [
            'pageTitle' => 'Funeral Assistance Types',
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
