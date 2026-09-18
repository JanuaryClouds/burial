<?php

namespace App\Livewire\Recommendation;

use App\Livewire\Forms\RecommendationForm;
use App\Models\Application;
use App\Models\FuneralAssistanceType;
use App\Models\ModeOfAssistance;
use App\Models\Recommendation;
use App\Models\WorkflowHistory;
use App\Models\WorkflowStage;
use App\Services\ActivityLoggerService;
use App\Services\WorkflowHistoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    public Application $application;

    public array $funeralAssistanceTypes = [];

    public array $modeOfAssistances = [];

    public bool $createNew = false;

    public RecommendationForm $form;

    public function boot()
    {
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
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        try {
            DB::transaction(function() {
                $recommendation = Recommendation::create([
                    'application_uuid' => $this->application->uuid,
                    'funeral_assistance_type_uuid' => $this->form->funeralAssistanceTypeUuid,
                    'amount_extended' => $this->form->amountExtended,
                    'mode_of_assistance_id' => $this->form->modeOfAssistanceId,
                    'recommended_by' => Auth::user()->id,
                ]);
        
                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'title' => 'Recommendation created successfully',
                ]);

                ActivityLoggerService::logSuccess('Successfully saved recommendation', [
                    'recommendation_uuid' => $recommendation->uuid
                ]);
        
                $this->reset('createNew');
                
                $this->dispatch('refreshRecommendation');
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to create a recommendation');

            report($th);
        }
    }

    #[On('refreshRecommendation')]
    public function refresh()
    {
        $this->reset('funeralAssistanceTypeUuid', 'amountExtended', 'modeOfAssistanceId');
        $this->application->refresh();
    }

    public function render()
    {
        return view('livewire.recommendation.create');
    }
}
