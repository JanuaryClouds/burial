<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkflowRequest;
use App\Http\Requests\UpdateWorkflowRequest;
use App\Models\Workflow;
use App\Services\DataTableService;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;

class WorkflowController extends Controller
{
    public function __construct(
        private readonly WorkflowService $services,
        private readonly DataTableService $dataTableServices,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('workflow.index', [
            'pageTitle' => 'Workflows',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('workflow.create', [
            'pageTitle' => 'Create Workflow',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workflow $workflow)
    {
        return view('workflow.edit', [
            'workflow' => $workflow,
            'pageTitle' => 'Edit '.$workflow->name,
        ]);
    }
}
