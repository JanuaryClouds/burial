<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Http\Requests\StoreWorkflowRequest;
use App\Http\Requests\UpdateWorkflowRequest;
use App\Services\DatatableService;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;

class WorkflowController extends Controller
{
    public function __construct(
        private readonly WorkflowService $services,
        private readonly DatatableService $datatableServices,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workflows = $this->services->index();

        if (request()->expectsJson()) {
            return $this->datatableServices->ajax($workflows);
        }

        return view('workflow.index', [
            'workflows' => $workflows,
            'page_title' => 'Workflows',
            'columns' => $this->datatableServices->getColumns($workflows),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('workflow.create', [
            'page_title' => 'Create Workflow',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkflowRequest $request)
    {
        try {
            $workflow = $this->services->store($request->validated());
            activity()
                ->withProperties(['ip' => request()->ip(), 'browser' => request()->userAgent(), 'workflow' => $workflow->uuid])
                ->causedBy(Auth::user())
                ->log('Created a new workflow: '.$workflow->uuid);

            return redirect()
                ->route('workflow.show', $workflow)
                ->with('success', 'Created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', app()->hasDebugModeEnabled() ? $e->getMessage() : 'An error occurred. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Workflow $workflow)
    {
        return view('workflow.show', [
            'workflow' => $workflow,
            'page_title' => $workflow->name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workflow $workflow)
    {
        return view('workflow.edit', [
            'workflow' => $workflow,
            'page_title' => $workflow->name,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkflowRequest $request, Workflow $workflow)
    {
        try {
            $this->services->update($request->validated(), $workflow);
            activity()
                ->withProperties(['ip' => request()->ip(), 'browser' => request()->userAgent(), 'workflow' => $workflow->uuid])
                ->causedBy(Auth::user())
                ->log('Updated the workflow details: '.$workflow->uuid);
            return redirect()
                ->route('workflow.show', $workflow)
                ->with('success', 'Updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', app()->hasDebugModeEnabled() ? $e->getMessage() : 'Unable to update workflow. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workflow $workflow)
    {
        //
    }
}
