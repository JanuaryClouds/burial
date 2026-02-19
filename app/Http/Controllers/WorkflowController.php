<?php

namespace App\Http\Controllers;

use App\Models\WorkflowStep;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function index()
    {
        $page_title = 'Workflow';
        $resource = 'workflowstep';
        $data = WorkflowStep::select('id', 'description')->get();

        return view('cms.index', compact('page_title', 'resource', 'data'));
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
            ->performedOn($workflow)
            ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
            ->log('Updated a workflow');

        return redirect()->route('workflowstep.index')->with('success', 'Workflow updated successfully');
    }
}
