<?php

namespace App\Livewire\Assessment;

use App\Models\Application;
use App\Models\Assessment;
use App\Models\WorkflowHistory;
use App\Models\WorkflowStage;
use App\Services\WorkflowHistoryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    public Application $application;

    public Assessment $assessment;

    private WorkflowHistoryService $workflowHistoryServices;

    #[Validate('required|string|max:65535')]
    public string $problem_presented;

    #[Validate('required|string|max:65535')]
    public string $swa;

    public function boot(WorkflowHistoryService $workflowHistoryService)
    {
        $this->workflowHistoryServices = $workflowHistoryService;
    }

    public function mount(Application $application)
    {
        $this->application = $application;
    }

    public function save()
    {
        $this->validate();

        $assessment = Assessment::updateOrCreate([
            'application_uuid' => $this->application->uuid,
            'problem_presented' => $this->problem_presented,
            'swa' => $this->swa,
        ]);

        $assessmentStage = WorkflowStage::firstWhere('name', '=', 'Assessment');
        $nextStage = WorkflowStage::firstWhere('position', '=', $assessmentStage->position + 1);

        if (!$nextStage) {
            return;
        }

        if ($this->application->workflowHistory->firstWhere('from_stage_uuid', '=', $assessmentStage->uuid)) {
            return;
        }

        activity()
            ->withProperties([
                'assessment' => $assessment->uuid,
                'application' => $this->application->uuid,
                'ip' => request()->ip(),
                'browser' => request()->userAgent(),
            ])
            ->causedBy(Auth::user()->id)
            ->log('Created an assessment');

        $this->workflowHistoryServices->store([
            'application_uuid' => $this->application->uuid,
            'from_stage_uuid' => $assessmentStage->uuid,
            'to_stage_uuid' => $nextStage->uuid,
            'date_in' => now(),
            'date_out' => now(),
            'reason' => 'Assessment completed',
            'processed_by' => Auth::user()->id,
        ]);
        
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.assessment.create');
    }
}
