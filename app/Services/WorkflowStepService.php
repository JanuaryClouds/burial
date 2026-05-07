<?php

namespace App\Services;

use App\Models\BurialAssistance;
use App\Models\ClaimantChange;
use App\Models\ProcessLog;
use App\Models\WorkflowStep;

class WorkflowStepService
{
    /**
     * Summary of nextStep
     *
     * @param  BurialAssistance  $application  Burial Assistance Application to check the next step of
     * @return object|WorkflowStep|\Illuminate\Database\Eloquent\Model|null
     */
    public function nextStep(BurialAssistance $application)
    {
        if ($application->status == 'released') {
            return null;
        }

        $latestLog = ProcessLog::where('burial_assistance_id', $application->id)->latest()->first();
        $latestOrderNo = ($latestLog && $latestLog->loggable) ? (int) $latestLog->loggable->order_no : null;
        $stepOne = WorkflowStep::where('order_no', 1)->first();

        if ($latestLog && $latestLog->loggable instanceof WorkflowStep) {
            $next_step = WorkflowStep::where('order_no', $latestOrderNo + 1)->first();

            return $next_step ?? null;
        } elseif ($latestLog && $latestLog->loggable instanceof ClaimantChange) {
            if ($latestLog->loggable->status === 'approved') {
                return $stepOne;
            } else {
                $secondLog = ProcessLog::where('burial_assistance_id', $application->id)->latest()->skip(1)->first();
                if ($secondLog && $secondLog->loggable instanceof WorkflowStep && $secondLog->loggable->order_no !== null) {
                    return WorkflowStep::where('order_no', $secondLog->loggable->order_no + 1)->first();
                } else {
                    return $stepOne;
                }
            }
        } else {
            return $stepOne;
        }
    }

    public function progress(BurialAssistance $application, ?WorkflowStep $nextStep = null, int $totalSteps = 0)
    {
        if ($nextStep === null) {
            return 100;
        }

        if ($totalSteps === 0) {
            return 0;
        }

        $completed_steps = $nextStep->order_no - 1;

        return ($completed_steps / $totalSteps) * 100;
    }
}
