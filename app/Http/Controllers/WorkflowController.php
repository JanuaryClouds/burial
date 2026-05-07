<?php

namespace App\Http\Controllers;

use App\Models\WorkflowStep;
use App\Services\DatatableService;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    protected $datatableServices;

    public function __construct(DatatableService $datatableService)
    {
        $this->datatableServices = $datatableService;
    }

    public function index()
    {
        $page_title = 'Workflow';
        $resource = 'workflowstep';
        $data = WorkflowStep::select('id', 'description')
            ->get()
            ->map(function ($workflow) {
                return [
                    'id' => $workflow->id,
                    'description' => $workflow->description,
                    'show_route' => route('workflowstep.edit', $workflow->id),
                ];
            });
        $columns = $this->datatableServices->getColumns($data, ['id', 'show_route']);

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $data->values(),
            ]);
        }

        return view('cms.index', compact('page_title', 'resource', 'data', 'columns'));
    }

    public function edit($id)
    {
        $page_title = 'Workflow';
        $resource = 'workflowstep';
        $data = WorkflowStep::select('id', 'description')->findOrFail($id);

        return view('cms.edit', compact('page_title', 'data', 'resource'));
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'description' => 'required',
        ]);
        $workflow = WorkflowStep::findOrFail($id);
        $workflow->update($request->only(['description']));
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent'), 'workflow' => $workflow->id])
            ->log('Updated a workflow');

        return redirect()->route('workflowstep.index')->with('success', 'Workflow updated successfully');
    }
}
