<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\ProcessLog;
use App\Models\BurialAssistance;
use App\Models\WorkflowStep;
use App\Models\Handler;

class ProcessLogController extends Controller
{
    public function add(Request $request, $id, $stepId) {
        try {
            $application = BurialAssistance::findOrFail($id);
            $step = WorkflowStep::findOrFail($stepId);
            $allSteps = WorkflowStep::all();
            // dd('Function hit!', $request->all());
            $validated = $request->validate([
                'date_in' => 'required|date',
                'date_out' => 'nullable|date|before_or_equal:date_in',
                'comments' => 'nullable|string',
                'extra_data' => 'sometimes|array',
                'added_by' => 'nullable|exists:users,id',
            ]);
            
            if ($application) {
                if ($application->status == 'pending') {
                    $application->status = 'processing';
                } elseif ($application->status == 'processing' && str_contains($step->description, 'Cheque available for pickup')) {
                    $application->status = 'approved';
                } elseif ($application->status == 'approved' && str_contains($step->description, 'Cheque claimed')) {
                    $application->status = 'released';
                }

                if ($application->initial_checker == null) {
                    $application->initial_checker = auth()->user()->id;
                }
    
                $application->update();
                if ($application->claimantChanges->where('status', 'approved')->first()) {
                    $claimant = $application->claimantChanges->where('status', 'approved')->first()->newClaimant;
                } else {
                    $claimant = $application->claimant;
                }
    
                $application->processLogs()->create([
                    'loggable_id' => $step->id,
                    'loggable_type' => WorkflowStep::class,
                    'is_progress_step' => true,
                    'burial_assistance_id' => $application->id,
                    'claimant_id' => $claimant->id,
                    'date_in' => $validated['date_in'],
                    'date_out'=> $validated['date_out'] ?? null,
                    'comments' => $validated['comments'] ?? null,
                    'extra_data' => $validated['extra_data'] ?? [],
                    'added_by' => $validated['added_by'] ?? auth()->user()->id
                ]);
    
                return back()->with('alertSuccess','Process log added successfully.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('alertError', $e->getMessage());
        }
    }

    public function delete($id, $stepId) {
        try {
            $application = BurialAssistance::findOrFail($id);
            if ($application) {
                $log = $application->processLogs()
                    ->whereHasMorph('loggable', [WorkflowStep::class], fn($q) => $q->where('order_no', $stepId))
                    ->with('loggable')
                    ->first();
                if ($log) {
                    $log->delete();
                    return redirect()->back()->with('success','Process log deleted successfully.');
                } else {
                    return redirect()->back()->with('error','Unable to find process log.');
                }
            } else {
                return redirect()->back()->with('error','Unable to find application.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('alertError', $e->getMessage());
        }
    }
}
