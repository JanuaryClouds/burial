<?php

namespace App\Livewire\Recommendation;

use App\Models\Application;
use App\Models\FuneralAssistanceType;
use App\Models\ModeOfAssistance;
use App\Models\Recommendation;
use App\Models\WorkflowHistory;
use App\Models\WorkflowStage;
use App\Services\WorkflowHistoryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    public Application $application;

    public array $funeralAssistanceTypes = [];

    public array $modeOfAssistances = [];

    private WorkflowHistoryService $workflowHistoryServices;

    #[Rule('required|exists:funeral_assistance_types,uuid')]
    public ?string $funeralAssistanceTypeUuid = null;

    #[Rule('required|numeric|min:0')]
    public ?int $amountExtended = null;

    #[Rule('required|exists:mode_of_assistances,id')]
    public ?int $modeOfAssistanceId = null;

    public function boot(WorkflowHistoryService $workflowHistoryService)
    {
        $this->workflowHistoryServices = $workflowHistoryService;
        $this->funeralAssistanceTypes = FuneralAssistanceType::query()
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->uuid => $item->name,
                ];
            })
            ->toArray();
        $this->modeOfAssistances = ModeOfAssistance::query()
            ->get()
            ->mapWithKeys(function ($mode) {
                return [
                    $mode->id => $mode->name,
                ];
            })
            ->toArray();
    }

    public function mount(Application $application)
    {
        $this->application = $application;
    }

    public function save()
    {
        $this->validate();

        $recommendation = Recommendation::create([
            'application_uuid' => $this->application->uuid,
            'funeral_assistance_type_uuid' => $this->funeralAssistanceTypeUuid,
            'amount_extended' => $this->amountExtended,
            'mode_of_assistance_id' => $this->modeOfAssistanceId,
            'recommended_by' => Auth::user()->id,
        ]);
        
        activity()
            ->withProperties([
                'recommendation' => $recommendation->uuid,
                'application' => $this->application->uuid,
                'ip' => request()->ip(),
                'browser' => request()->userAgent(),
            ])
            ->causedBy(Auth::user()->id)
            ->log('Created a recommendation');

        $this->dispatch('refreshWorkflowHistory');
    }

    public function render()
    {
        return view('livewire.recommendation.create');
    }
}
