<?php

namespace App\Livewire\FuneralAssistanceType;

use App\Livewire\Forms\FuneralAssistanceTypeForm;
use App\Models\Workflow;
use App\Services\ActivityLoggerService;
use App\Services\FuneralAssistanceTypeService;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;

class Create extends Component
{
    use HasPlaceholder;

    public FuneralAssistanceTypeForm $form;

    public ?array $workflows = null;

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
    }

    public function mount()
    {
        $this->workflows = Workflow::selectable()->orderBy('name')->get()->mapWithKeys(fn (Workflow $workflow) => [$workflow->uuid => $workflow->label()])->all();
    }

    public function clearForm()
    {
        $this->form->reset();
    }

    public function save()
    {
        $this->form->validate();

        try {
            $funeralAssistanceType = app(FuneralAssistanceTypeService::class)->store($this->form->all());

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => 'Successfully created new type of funeral assistance',
            ]);

            ActivityLoggerService::logSuccess('Successfully created new type of funeral assistance', [
                'uuid' => $funeralAssistanceType->uuid,
            ]);

            $this->form->reset();
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to save new type of funeral assistance');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.funeral-assistance-type.create');
    }
}
