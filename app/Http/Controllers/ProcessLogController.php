<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcessLog;
use App\Models\BurialAssistance;
use App\Models\WorkflowStep;
use App\Models\Handler;

class ProcessLogController extends Controller
{
    public function add(Request $request, $id, $stepId) {
        $application = BurialAssistance::findOrFail($id);
        $step = WorkflowStep::findOrFail($stepId);
        // dd('Function hit!', $request);
        $validated = $request->validate([
            'date_in' => 'required|date',
            'date_out' => 'nullable|date|before_or_equal:date_in',
            'comments' => 'nullable|string',
            'extra_data' => 'sometimes|string',
        ]);
        
        if ($application) {
            $application->status = 'processing';
            $application->update();
            $application->processLogs()->create([
                'loggable_id' => $step->id,
                'loggable_type' => WorkflowStep::class,
                'is_progress_step' => true,
                'burial_assistance_id' => $application->id,
                'claimant_id' => $application->claimant->id,
                'date_in' => $validated['date_in'],
                'date_out'=> $validated['date_out'] ?? null,
                'comments' => $validated['comments'] ?? null,
                'extra_data' => $validated['extra_data'] ?? [],
            ]);

            return back()->with('success','Process log added successfully.');
        }
        return redirect()->back()->with('error','Unable to save process log.');
    }
}
