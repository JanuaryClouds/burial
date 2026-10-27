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
    public function boot(): void
    {
        abort_unless(auth()->user()?->isInternal(), 403);
    }

    use GuardsApplicationView;
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

    public array $customValues = [];

    #[Locked]
    public array $customFields = [];

    public RemarkForm $remarkForm;

    #[Locked]
    public ?string $recommendationUuid = null;

    #[Locked]
    public ?string $expectedStageUuid = null;

    public function mount(Application $application): void
    {
        $this->application = $application;
        if ($application->relationLoaded('activeRecommendation')) {
            $this->initializeForm();
        } else {
            $this->refresh();
        }
    }

    #[On('refreshWorkflowHistory')]
    #[On('refreshRecommendation')]
    public function refresh(): void
    {
        $this->application = Application::findOrFail($this->application->uuid);
        $this->initializeForm();
    }

    private function initializeForm(): void
    {
        $this->reset('dateIn', 'dateOut', 'reason', 'toStageUuid', 'customValues', 'customFields');
        $this->resetValidation();
        $this->application->loadMissing(['activeRecommendation.workflow', 'workflowStage', 'closure', 'rejection', 'cancellation', 'referral']);
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
            $this->application->isActive();
    }

    public function setDateInToNow(): void
    {
        $this->dateIn = Carbon::now()->format('Y-m-d\\TH:i:s');
        $this->dateOut = null;
    }

    public function updatedToStageUuid(): void
    {
        $this->reset('customValues', 'customFields');
        $this->resetValidation();
        if (! $this->toStageUuid || ! $this->stages->contains('uuid', $this->toStageUuid)) {
            return;
        }
        $version = $this->application->currentWorkflow();
        $stage = $version?->stages()->with('customFields')->whereKey($this->toStageUuid)->first();
        $this->customFields = $stage?->customFields->map(fn ($field) => $field->only([
            'uuid', 'label', 'help_text', 'input_type', 'required',
        ]))->all() ?? [];
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
            'custom_values' => $this->customValues,
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
