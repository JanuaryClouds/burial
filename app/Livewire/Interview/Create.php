<?php

namespace App\Livewire\Interview;

use App\Models\Client;
use App\Models\Interview;
use App\Models\WorkflowStage;
use App\Services\InterviewService;
use App\Services\WorkflowHistoryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    public Client $client;

    private InterviewService $services;

    private WorkflowHistoryService $workflowHistoryServices;

    #[Validate('required|date')]
    public string $schedule;

    public function boot(InterviewService $interviewService, WorkflowHistoryService $workflowHistoryService)
    {
        $this->services = $interviewService;
        $this->workflowHistoryServices = $workflowHistoryService;
    }

    public function mount(Client $client)
    {
        $this->client = $client;
    }

    public function save()
    {
        $this->validate();

        if ($this->client->interviews->where('schedule', '>', now())->count() > 0) {
            return;
        }

        $this->services->store(
            ['schedule' => $this->schedule],
            $this->client->uuid
        );

        $interviewStage = WorkflowStage::firstWhere('name', 'Interview');
        $nextStage = WorkflowStage::firstWhere('position', $interviewStage->position + 1);

        if (!$nextStage) {
            return;
        }

        if ($this->client->application->workflowHistory->firstWhere('from_stage_uuid', '=', $interviewStage->uuid)) {
            return;
        }

        $this->workflowHistoryServices->store([
            'application_uuid' => $this->client->application->uuid,
            'from_stage_uuid' => $interviewStage->uuid,
            'to_stage_uuid' => $nextStage->uuid,
            'date_in' => now(),
            'date_out' => now(),
            'reason' => 'Interview scheduled.',
            'processed_by' => Auth::user()->id,
        ]);

        $this->reset('schedule');
        $this->dispatch('interviewCreated');
    }

    public function render()
    {
        return view('livewire.interview.create');
    }
}
