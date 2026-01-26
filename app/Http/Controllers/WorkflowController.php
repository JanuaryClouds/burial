<?php

namespace App\Http\Controllers;

use App\Models\WorkflowStep;

class WorkflowController extends Controller
{
    public function index()
    {
        $page_title = 'Workflow';
        $resource = 'workflowstep';
        $data = WorkflowStep::select('id', 'description', 'handler_id')->get();

        return view('cms.index', compact('page_title', 'resource', 'data'));
    }

    public function edit(WorkflowStep $workflow)
    {
        $page_title = 'Workflow';
        $resource = 'workflowstep';
        $data = WorkflowStep::find($workflow->id);

        return view('cms.edit', compact('page_title', 'data', 'resource'));
    }
}
