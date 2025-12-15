<?php

namespace App\Http\Controllers;

use App\Models\WorkflowStep;

class WorkflowController extends Controller
{
    public function index()
    {
        $page_title = 'Workflow';
        $resource = 'workflow';
        $data = WorkflowStep::select('id', 'description', 'handler_id')->get();

        return view('cms.index', compact('page_title', 'resource', 'data'));
    }

    public function edit(WorkflowStep $workflow)
    {
        $page_title = 'Workflow';
        $resource = 'workflow';
        $data = WorkflowStep::find($workflow->id)->select('id', 'description')->first();

        return view('cms.edit', compact('page_title', 'data', 'resource'));
    }
}
