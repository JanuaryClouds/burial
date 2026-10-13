<?php

namespace App\Livewire\FuneralAssistanceType;

use App\Livewire\Forms\FuneralAssistanceTypeForm;
use App\Models\Workflow;
use App\Services\ActivityLoggerService;
use App\Services\FuneralAssistanceTypeService;
use Livewire\Component;

class Create extends Component
{
    public FuneralAssistanceTypeForm $form;

    public ?array $workflows = null;

    public function mount()
    {
        $this->workflows = Workflow::index()
            ->get() 
            ->mapWithKeys(fn($workflow) => [$workflow->uuid => $workflow->name])
            ->toArray();
    }

    public function clearForm()
    {
        $this->form->reset();
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
            $funeralAssistanceType = app(FuneralAssistanceTypeService::class)->store($this->form->all());

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => "Successfully created new type of funeral assistance"
            ]);

            ActivityLoggerService::logSuccess('Successfully created new type of funeral assistance', [
                'uuid' => $funeralAssistanceType->uuid,
            ]);

            $this->form->reset();
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown')
            ]);

            ActivityLoggerService::logException($th, "Failed to save new type of funeral assistance");

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.funeral-assistance-type.create');
    }
}
