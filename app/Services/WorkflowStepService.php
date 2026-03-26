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

        $latest_log = ProcessLog::where('burial_assistance_id', $application->id)->latest()->first();
        if ($latest_log && $latest_log->loggable instanceof WorkflowStep) {
            $next_step = WorkflowStep::where('order_no', $latest_log->loggable->order_no + 1)->first();

            return $next_step ?? null;
        } elseif ($latest_log && $latest_log->loggable instanceof ClaimantChange) {
            if ($latest_log->loggable->status === 'approved') {
                return WorkflowStep::where('order_no', 1)->first();
            } else {
                $second_log = ProcessLog::where('burial_assistance_id', $application->id)->latest()->skip(1)->first();
                if ($second_log && $second_log->loggable instanceof WorkflowStep) {
                    return WorkflowStep::where('order_no', $second_log->loggable->order_no + 1)->first();
                } else {
                    return WorkflowStep::where('order_no', 1)->first();
                }
            }
        } else {
            return WorkflowStep::where('order_no', 1)->first();
        }
    }

    public function progress(BurialAssistance $application)
    {
        $total_workflow_steps = WorkflowStep::count();
        if ($total_workflow_steps === 0) {
            return 0;
        }

        $next_step = $this->nextStep($application);
        if ($next_step === null) {
            return 100;
        }

        $completed_steps = $next_step->order_no - 1;

        return ($completed_steps / $total_workflow_steps) * 100;
    }
}
