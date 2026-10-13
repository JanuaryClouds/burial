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

    public function mount(FuneralAssistanceType $funeralAssistanceType)
    {
        $this->funeralAssistanceType = $funeralAssistanceType;
        $this->workflows = Workflow::index()
            ->get()
            ->mapWithKeys(fn($workflow) => [$workflow->uuid => $workflow->name])
            ->toArray();

        $this->form->setFuneralAssistanceType($funeralAssistanceType);
    }

    public function save()
    {
        try {
            $this->form->validate();
        } catch (\Exception $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation')
            ]);
        }

        try {
            app(FuneralAssistanceTypeService::class)->update(
                $this->form->all(),
                $this->funeralAssistanceType,
            );

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => "Successfully updated funeral assistance type"
            ]);

            ActivityLoggerService::logSuccess('Successfully updated funeral assistance type', [
                'uuid' => $this->funeralAssistanceType->uuid,
            ]);
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown')
            ]);

            ActivityLoggerService::logException($th, "Failed to update funeral assistance type");

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.funeral-assistance-type.edit');
    }
}
