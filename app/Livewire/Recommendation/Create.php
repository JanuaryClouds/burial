<?php

namespace App\Livewire\Recommendation;

use App\Livewire\Forms\RecommendationForm;
use App\Models\Application;
use App\Models\FuneralAssistanceType;
use App\Models\ModeOfAssistance;
use App\Models\Recommendation;
use App\Models\Workflow;
use App\Services\RecommendationService;
use App\Traits\Livewire\GuardsOfficeForm;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Create extends Component
{
    use GuardsOfficeForm;

    public function bootOfficeAccess(): void
    {
        abort_unless(auth()->user()?->canOfficeAction('Create Recommendation'), 403);
    }

    public Application $application;

    public array $funeralAssistanceTypes = [];

    public array $modeOfAssistances = [];

    public array $workflows = [];

    public bool $createNew = false;

    public RecommendationForm $form;

    #[Locked]
    public ?string $expectedRecommendationUuid = null;

    #[Locked]
    public ?string $expectedAssignmentUuid = null;

    public function boot(): void
    {
        $this->funeralAssistanceTypes = FuneralAssistanceType::orderBy('name')->pluck('name', 'uuid')->all();
        $this->modeOfAssistances = ModeOfAssistance::orderBy('name')->pluck('name', 'id')->all();
        $this->workflows = Workflow::selectable()->orderBy('name')->get()->mapWithKeys(fn ($workflow) => [$workflow->uuid => $workflow->label()])->all();
    }

    public function mount(Application $application): void
    {
        Gate::authorize('create', [Recommendation::class, $application]);
        $this->application = $application;
        $this->expectedAssignmentUuid = $application->current_claimant_assignment_uuid;
        $this->expectedRecommendationUuid = $application->current_recommendation_uuid;
    }

    public function updatedFormFuneralAssistanceTypeUuid(): void
    {
        $suggestion = FuneralAssistanceType::whereKey($this->form->funeralAssistanceTypeUuid)->value('workflow_uuid');
        $this->form->workflowUuid = $suggestion && array_key_exists($suggestion, $this->workflows) ? $suggestion : null;
        $this->resetValidation('form.workflowUuid');
    }

    public function save(): void
    {
        $this->form->validate();
        app(RecommendationService::class)->store([
            'funeral_assistance_type_uuid' => $this->form->funeralAssistanceTypeUuid,
            'workflow_uuid' => $this->form->workflowUuid,
            'amount_extended' => $this->form->amountExtended,
            'mode_of_assistance_id' => $this->form->modeOfAssistanceId,
            'expected_recommendation_uuid' => $this->expectedRecommendationUuid,
            'expected_assignment_uuid' => $this->expectedAssignmentUuid,
        ], $this->application->uuid);
        $this->refresh();
        $this->createNew = false;
        $this->dispatch('refreshRecommendation');
        $this->dispatch('refreshWorkflowHistory');
        $this->dispatch('notification:toast', ['type' => 'success', 'text' => 'Recommendation saved. Its workflow starts at the beginning.']);
    }

    #[On('refreshRecommendation')]
    public function refresh(): void
    {
        $this->form->reset();
        $this->application->refresh();
        $this->expectedRecommendationUuid = $this->application->current_recommendation_uuid;
    }

    public function render()
    {
        $this->application->loadMissing('activeRecommendation');

        return view('livewire.recommendation.create');
    }
}
