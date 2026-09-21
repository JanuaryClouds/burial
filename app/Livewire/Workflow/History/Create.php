<?php

namespace App\Livewire\Workflow\History;

use App\Models\Application;
use App\Models\WorkflowHistory;
use App\Models\WorkflowStage;
use App\Services\ActivityLoggerService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Create extends Component
{
    public Application $application;

    public ?WorkflowStage $toStage;

    public Collection $stages;

    public bool $revertToRecommendation = false;

    public bool $showDateOut = false;

    public bool $showForm = false;

    #[Rule('required|date')]
    public string $dateIn;

    #[Rule('required|uuid|exists:workflow_stages,uuid')]
    public ?string $toStageUuid;

    #[Rule('required|date|after_or_equal:dateIn')]
    public ?string $dateOut;

    #[Rule('nullable|string')]
    public ?string $reason;

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->stages = $this->loadStages($application);

        if ($this->stages && $this->application->toStage()) {
            $this->showForm = Auth::user()->can($this->application->toStage()->permission->name) || Auth::user()->hasRole('superadmin');
        }
    }

    #[On('refreshWorkflowHistory')]
    public function refresh()
    {
        $this->reset('dateIn', 'dateOut', 'reason', 'toStageUuid');
        $this->application->fresh();
        $this->stages = $this->loadStages($this->application);

        if ($this->stages) {
            $this->showForm = Auth::user()->can($this->application->toStage()->permission->name) || Auth::user()->hasRole('superadmin');
        }
    }

    private function loadStages(Application $application): Collection
    {
        return $application->currentWorkflow()->stages
            ->when($application->currentStage(), function ($stages) use ($application) {
                $currentPosition = $application->currentStage()->position;

                return $stages->filter(function ($stage) use ($currentPosition) {
                    return $stage->position < $currentPosition || $stage->position === $currentPosition + 1;
                });
            })
            ->when($application->currentStage() === null, function ($stages) {
                return $stages->where('position', '=', 1);
            })
            ->sortBy('position')
            ->map(function ($stage) use ($application) {
                $name = $stage->name;

                if ($application->currentStage() && $stage->position === $application->currentStage()->position + 1) {
                    $name = 'Next Stage - '.$name;
                }

                return [
                    'name' => $name,
                    'uuid' => $stage->uuid,
                ];
            });
    }

    public function setDateInToNow()
    {
        $this->dateIn = Carbon::now()->format('Y-m-d\TH:i:s');
        $this->dateOut = null;
    }

    public function setDateOutToNow()
    {
        $this->dateOut = Carbon::now()->format('Y-m-d\TH:i:s');
    }

    public function submit()
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
            DB::transaction(function () {
                if (Auth::user()->can($this->application->toStage()->permission->name) || ! Auth::user()->hasRole('superadmin')) {
                    $this->dispatch('refreshWorkflowHistory');
                    $this->dispatch('notification:alert', [
                        'type' => 'warning',
                        'title' => 'Unauthorized',
                        'text' => 'You are not authorized to log this stage',
                    ]);

                    return;
                }

                $workflowHistory = WorkflowHistory::create([
                    'recommendation_uuid' => $this->application->currentRecommendation()->uuid,
                    'from_stage_uuid' => $this->application->previousHistory() ? $this->application->previousHistory()->to_stage_uuid : null,
                    'to_stage_uuid' => $this->toStageUuid,
                    'date_in' => $this->dateIn,
                    'date_out' => $this->dateOut,
                    'reason' => $this->reason ?? null,
                    'processed_by' => Auth::id(),
                ]);

                $this->application->current_workflow_stage_uuid = $this->toStageUuid;
                $this->application->save();

                ActivityLoggerService::logSuccess('Succesfully created workflow history', [
                    'application_uuid' => $this->application->uuid,
                    'recommendation_uuid' => $workflowHistory->recommendation->uuid,
                    'workflow_history_uuid' => $workflowHistory->uuid,
                ]);

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'title' => 'History created successfully',
                ]);
                $this->dispatch('refreshWorkflowHistory');
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to create workflow history');

            report($th);
        }
    }

    public function placeholder()
    {
        return view('components.card.loading');
    }

    public function render()
    {
        return view('livewire.workflow.history.create');
    }
}
