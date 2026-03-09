<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessLogRequest;
use App\Models\BurialAssistance;
use App\Models\WorkflowStep;
use App\Services\DatatableService;
use App\Services\ImageService;
use App\Services\ProcessLogService;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Storage;

class ProcessLogController extends Controller
{
    protected $processLogServices;
    protected $imageServices;

    public function __construct(ProcessLogService $processLogService, ImageService $imageService)
    {
        $this->processLogServices = $processLogService;
        $this->imageServices = $imageService;
    }

    public function add(ProcessLogRequest $request, $id, $stepId)
    {
        try {
            $application = BurialAssistance::findOrFail($id);
            $step = WorkflowStep::findOrFail($stepId);
            $validated = $request->validated();

            if ($application) {
                $this->processLogServices->create(
                    $application,
                    $application->claimantChanges->where('status', 'approved')->first()->newClaimant ?? $application->claimant,
                    $step,
                    $validated
                );

                if ($step->order_no == 13) {
                    $latestCheque = $application->latestCheque();
                    if (!$latestCheque) {
                        return redirect()->back()->with('error', 'Unable to find latest cheque.');
                    }
                    $latestCheque->update([
                        'status' => 'claimed',
                        'date_claimed' => $request['date_in'],
                    ]);
                    if ($request->file('cheque-image-proof')) {
                        $extension = $request->file('cheque-image-proof')->getClientOriginalExtension();
                        $this->imageServices->post($application->claimant->client->tracking_no.'-cheque-proof', $request->file('cheque-image-proof'));
                        $application->update(['status' => 'released']);
                    } else {
                        return redirect()->back()->with('info', 'Please upload a photo of the cheque.');
                    }
                }

                return back()->with('success', 'Process log added successfully.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function delete($id, $stepId)
    {
        try {
            $application = BurialAssistance::findOrFail($id);
            if ($application) {
                $log = $application->processLogs()
                    ->whereHasMorph('loggable', [WorkflowStep::class], fn ($q) => $q->where('order_no', $stepId))
                    ->with('loggable')
                    ->first();
                if ($log) {
                    $this->processLogService->delete($log, $application);

                    return redirect()->back()->with('success', 'Process log deleted successfully.');
                } else {
                    return redirect()->back()->with('error', 'Unable to find process log.');
                }
            } else {
                return redirect()->back()->with('error', 'Unable to find application.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
