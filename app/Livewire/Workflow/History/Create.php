<?php

namespace App\Livewire\Workflow\History;

use App\Livewire\Forms\RemarkForm;
use App\Models\Application;
use App\Services\WorkflowHistoryService;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Create extends Component
{
    use HasPlaceholder;

    public Application $application;

    public Collection $stages;

    public bool $showForm = false;

    public bool $paused = false;

    public ?string $workflowLabel = null;

    public ?string $dateIn = null;

    public ?string $dateOut = null;

    public ?string $toStageUuid = null;

    public ?string $reason = null;

    public RemarkForm $remarkForm;

    #[Locked]
    public ?string $recommendationUuid = null;

    #[Locked]
    public ?string $expectedStageUuid = null;

    public function mount(Application $application): void
    {
        $this->application = $application;
        $this->refresh();
    }

    #[On('refreshWorkflowHistory')]
    #[On('refreshRecommendation')]
    public function refresh(): void
    {
        $this->reset('dateIn', 'dateOut', 'reason', 'toStageUuid');
        $this->application->refresh();
        $this->application->loadMissing(['activeRecommendation.workflow', 'workflowStage']);
        $this->recommendationUuid = $this->application->current_recommendation_uuid;
        $this->expectedStageUuid = $this->application->current_workflow_stage_uuid;
        $version = $this->application->currentWorkflow();
        $this->workflowLabel = $version?->label();
        $this->paused = $version !== null && ! $version->is_active;
        $currentStage = $this->application->currentStage();
        $position = $currentStage ? $currentStage->position : 0;
        $this->stages = $version ? $version->stages()->with('permission')->orderBy('position')->get()
            ->filter(fn ($stage) => $stage->position === $position + 1 || ($position > 0 && $stage->position < $position))
            ->filter(fn ($stage) => auth()->user()?->hasRole('superadmin') || ($stage->permission && auth()->user()?->can($stage->permission->name)))
            ->map(fn ($stage) => ['uuid' => $stage->uuid, 'name' => $stage->name]) : collect();
        $this->showForm = ! $this->paused && $this->stages->isNotEmpty() &&
            ! $this->application->closure()->exists() && ! $this->application->rejection()->exists() &&
            ! $this->application->cancellation()->exists() && ! $this->application->referral()->exists();
    }

    public function setDateInToNow(): void
    {
        $this->dateIn = Carbon::now()->format('Y-m-d\\TH:i:s');
        $this->dateOut = null;
    }

    public function setDateOutToNow(): void
    {
        $this->dateOut = Carbon::now()->format('Y-m-d\\TH:i:s');
    }

    public function submit(): void
    {
        app(WorkflowHistoryService::class)->store([
            'recommendation_uuid' => $this->recommendationUuid, 'expected_stage_uuid' => $this->expectedStageUuid,
            'to_stage_uuid' => $this->toStageUuid, 'date_in' => $this->dateIn, 'date_out' => $this->dateOut,
            'reason' => $this->reason, 'remark' => $this->remarkForm->content,
        ]);
        $this->remarkForm->reset();
        $this->refresh();
        $this->dispatch('refreshWorkflowHistory');
        $this->dispatch('notification:toast', ['type' => 'success', 'text' => 'Stage processing recorded.']);
    }

    public function render()
    {
        return view('livewire.workflow.history.create');
    }
}
