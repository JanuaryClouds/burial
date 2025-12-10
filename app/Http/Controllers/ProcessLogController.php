<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\ProcessLog;
use App\Models\BurialAssistance;
use App\Models\WorkflowStep;
use App\Models\Handler;
use Illuminate\Support\Facades\Crypt;
use Storage;
use Str;

class ProcessLogController extends Controller
{
    public function add(Request $request, $id, $stepId) {
        try {
            $application = BurialAssistance::findOrFail($id);
            $step = WorkflowStep::findOrFail($stepId);
            // dd('Function hit!', $request->all(), $request->file());
            $validated = $request->validate([
                'date_in' => 'required|date',
                'date_out' => 'nullable|date|before_or_equal:date_in',
                'comments' => 'nullable|string',
                'extra_data' => 'sometimes|array',
                'added_by' => 'nullable|exists:users,id',
            ]);
            if ($application) {
                if ($application->status == 'pending') {
                    $application->update(['status' => 'processing']);
                }

                if ($step->order_no == 9) {
                    $chequeValidated = $request->validate([
                        'extra_data.OBR.oBR_number' => 'required|string',
                        'extra_data.OBR.date' => 'required|string',
                    ]);

                    $application->cheque()->create([
                        'id' => Str::uuid(),
                        'burial_assistance_id' => $application->id,
                        'claimant_id' => $application->claimantChanges->where('status', 'approved')->first()->newClaimant->id ?? $application->claimant_id,
                        'obr_number' => $chequeValidated['extra_data']['OBR']['oBR_number'],
                    ]);
                }

                if ($step->order_no == 10) {
                    $application->latestCheque()->update([
                        'dv_number' => $request['extra_data']['dv_number'] ?? null,
                    ]);
                }

                if ($step->order_no == 11) {
                    $application->latestCheque()->update([
                        'cheque_number' => $request['extra_data']['cheque_number'],
                        'amount' => $request['extra_data']['amount'],
                    ]);
                }
                
                if ($step->order_no == 12) {
                    $application->latestCheque()->update([
                        'date_issued' => $request['extra_data']['date_issued'],
                    ]);
                    $application->update(['status' => 'approved']);
                }
                
                if ($step->order_no == 13) {
                    $application->latestCheque()->update([
                        'status' => 'claimed',
                        'date_claimed' => $validated['date_in'],
                    ]);
                    if ($request->file('cheque-image-proof')) {
                        $extension = $request->file('cheque-image-proof')->getClientOriginalExtension();
                        $filename = $application->latestCheque->id . '-cheque-proof.' . $extension;
                        $path = "burial-assistance/{$application->tracking_no}/";
                        Storage::disk('local')->put($path . $filename, Crypt::encrypt(file_get_contents($request->file('cheque-image-proof'))));
                        $application->update(['status' => 'released']);
                    } else {
                        return redirect()->back()->with('info', 'Please upload a photo of the cheque.');
                    }
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
                    'id' => Str::uuid(),
                    'loggable_id' => $step->id,
                    'loggable_type' => WorkflowStep::class,
                    'is_progress_step' => true,
                    'burial_assistance_id' => $application->id,
                    'claimant_id' => $claimant->id,
                    'date_in' => $validated['date_in'],
                    'date_out'=> $validated['date_out'] ?? null,
                    'comments' => $validated['comments'] ?? null,
                    'extra_data' => $validated['extra_data'] ?? [],
                    'added_by' => auth()->user()->id,
                ]);
    
                return back()->with('success','Process log added successfully.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
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
                    if (class_basename($log->loggable) === 'WorkflowStep' && $log->loggable->order_no == 9) {
                        $application->lastestCheque()->delete();
                        $application->update(['status' => 'processing']);
                    }

                    if (class_basename($log->loggable) === 'WorkflowStep' && $log->loggable->order_no == 10) {
                        $application->latestCheque()->update(['dv_number' => null]);
                    }

                    if (class_basename($log->loggable) === 'WorkflowStep' && $log->loggable->order_no == 11) {
                        $application->latestCheque()->update(['cheque_number' => null]);
                        $application->latestCheque()->update(['amount' => null]);
                    }

                    if (class_basename($log->loggable) === 'WorkflowStep' && $log->loggable->order_no == 12) {
                        $application->latestCheque()->update(['date_issued' => null]);
                        $application->update(['status' => 'processing']);
                    }

                    if (class_basename($log->loggable) === 'WorkflowStep' && $log->loggable->order_no == 13) {
                        $application->latestCheque()->update([
                            'status' => 'issued',
                            'date_claimed' => null
                        ]);
                        $application->update(['status' => 'approved']);
                    }

                    $log->delete();
                    return redirect()->back()->with('success','Process log deleted successfully.');
                } else {
                    return redirect()->back()->with('error','Unable to find process log.');
                }
            } else {
                return redirect()->back()->with('error','Unable to find application.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
