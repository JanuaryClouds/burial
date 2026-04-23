<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessLogRequest;
use App\Models\BurialAssistance;
use App\Models\ProcessLog;
use App\Models\WorkflowStep;
use App\Services\ImageService;
use App\Services\NotificationService;
use App\Services\ProcessLogService;
use Exception;

class ProcessLogController extends Controller
{
    protected $processLogServices;

    protected $imageServices;

    protected $notificationServices;

    public function __construct(ProcessLogService $processLogService, ImageService $imageService, NotificationService $notificationService)
    {
        $this->processLogServices = $processLogService;
        $this->imageServices = $imageService;
        $this->notificationServices = $notificationService;
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

                if ($step->order_no == 12) {
                    $citizenUuid = $application->originalClaimant()?->client?->user?->citizen_uuid;
                    if ($citizenUuid) {
                        $this->notificationServices->send(
                            $citizenUuid,
                            'cheque',
                            'Cheque is ready to be picked up',
                            'A cheque for your burial assistance application is ready to be picked up. Please come to the Taguig City Hall CSWDO Office.'
                        );
                    }
                }

                if ($step->order_no == 13) {
                    $latestCheque = $application->latestCheque();
                    if (! $latestCheque) {
                        return redirect()->back()->with('error', 'Unable to find latest cheque.');
                    }
                    $latestCheque->update([
                        'status' => 'claimed',
                        'date_claimed' => $request['date_in'],
                    ]);
                    $application->update(['status' => 'released']);
                    if ($request->file('cheque-image-proof') && app()->isProduction()) {
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

    public function delete($id)
    {
        try {
            $log = ProcessLog::findOrFail($id);
            if ($log) {
                $this->processLogServices->delete($log, $log->burialAssistance);

                return redirect()->back()->with('success', 'Process log deleted successfully.');
            } else {
                return redirect()->back()->with('error', 'Unable to find process log.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
