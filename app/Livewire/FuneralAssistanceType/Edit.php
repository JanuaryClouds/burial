<?php

namespace App\Livewire\FuneralAssistanceType;

use App\Livewire\Forms\FuneralAssistanceTypeForm;
use App\Models\FuneralAssistanceType;
use App\Models\Workflow;
use App\Services\ActivityLoggerService;
use App\Services\FuneralAssistanceTypeService;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;

class Edit extends Component
{
    use HasPlaceholder;

    public ?FuneralAssistanceType $funeralAssistanceType = null;

    public FuneralAssistanceTypeForm $form;

    public ?array $workflows = null;

    public ?string $unavailableWorkflowName = null;

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
    }

    public function mount(FuneralAssistanceType $funeralAssistanceType)
    {
        $this->funeralAssistanceType = $funeralAssistanceType;
        $this->workflows = Workflow::selectable()->orderBy('name')->get()->mapWithKeys(fn (Workflow $workflow) => [$workflow->uuid => $workflow->label()])->all();
        $assignedWorkflow = $funeralAssistanceType->workflow()->first();
        if ($assignedWorkflow && ! $assignedWorkflow->isSelectable()) {
            $this->unavailableWorkflowName = $assignedWorkflow->label();
        }

        $this->form->setFuneralAssistanceType($funeralAssistanceType);
    }

    public function save()
    {
        $this->form->validate();

        try {
            app(FuneralAssistanceTypeService::class)->update(
                $this->form->all(),
                $this->funeralAssistanceType,
            );

            $this->unavailableWorkflowName = null;

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => 'Successfully updated funeral assistance type',
            ]);

            ActivityLoggerService::logSuccess('Successfully updated funeral assistance type', [
                'uuid' => $this->funeralAssistanceType->uuid,
            ]);
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to update funeral assistance type');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.funeral-assistance-type.edit');
    }
}
