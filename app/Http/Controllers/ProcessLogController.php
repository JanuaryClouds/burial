<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessLogRequest;
use App\Models\BurialAssistance;
use App\Models\ProcessLog;
use App\Models\SystemSetting;
use App\Models\WorkflowStep;
use App\Services\ImageService;
use App\Services\NotificationService;
use App\Services\ProcessLogService;
use App\Services\SmsService;
use Exception;

class ProcessLogController extends Controller
{
    protected $processLogServices;

    protected $imageServices;

    protected $notificationServices;

    protected $smsServices;

    public function __construct(ProcessLogService $processLogService, ImageService $imageService, NotificationService $notificationService, SmsService $smsServices)
    {
        $this->processLogServices = $processLogService;
        $this->imageServices = $imageService;
        $this->notificationServices = $notificationService;
        $this->smsServices = $smsServices;
    }

    public function add(ProcessLogRequest $request, $id, $stepId)
    {
        try {
            $application = BurialAssistance::findOrFail($id);
            $step = WorkflowStep::findOrFail($stepId);
            $validated = $request->validated();

            if ($application) {
                $ip = request()->ip();
                $browser = request()->header('User-Agent');
                $citizenUuid = $application->originalClaimant()?->client?->user?->citizen_uuid;

                $this->processLogServices->create(
                    $application,
                    $application->currentClaimant(),
                    $step,
                    $validated
                );

                if ($step->order_no == 12) {
                    activity()
                        ->causedBy(auth()->user())
                        ->withProperties(['ip' => $ip, 'browser' => $browser, 'burialAssistance' => $application->id])
                        ->log('Cheque is ready to be picked up');

                    if ($citizenUuid) {
                        $this->notificationServices->send(
                            $citizenUuid,
                            'cheque',
                            'Cheque is ready to be picked up',
                            'A cheque for your burial assistance application is ready to be picked up. Please come to the Taguig City Hall CSWDO Office.'
                        );

                        $claimant = $application->currentClaimant();
                        $beneficiary = $application->beneficiary();

                        if ($claimant && $claimant?->contact_number && $beneficiary) {
                            $department_email = SystemSetting::first()?->department_email;
                            $this->smsServices->send(
                                $claimant->contact_number,
                                'Magandang araw! '.$claimant->fullname().', Ang tseke para sa Burial Assistance para kay'
                                .$beneficiary->fullname().' ay maaari nang kuhain sa opisina ng CSWDO, sa Taguig City Hall, Gen. Luna St., Tuktukan, Taguig City. Magdala ng dalawang valid Government ID para sa beripikasyon. Para sa karagdagang detalye, maaaring makipag-ugnayan sa '
                                .$department_email.'. Maraming salamat po.'
                            );
                        }
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
                        $this->imageServices->post($application->originalClaimant()?->client?->tracking_no.'-cheque-proof', $request->file('cheque-image-proof'));
                        $application->update(['status' => 'released']);
                    } else {
                        return redirect()->back()->with('info', 'Please upload a photo of the cheque.');
                    }

                    if ($citizenUuid) {
                        $this->notificationServices->send(
                            $citizenUuid,
                            'cheque',
                            'Cheque has been claimed',
                            'A cheque for your burial assistance application has been claimed. If this is a mistake, please contact Taguig City CSWDO immediately.'
                        );

                        activity()
                            ->causedBy(auth()->user())
                            ->withProperties(['ip' => $ip, 'browser' => $browser, 'burialAssistance' => $application->id])
                            ->log('Cheque has been claimed');
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
