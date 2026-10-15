<?php

namespace App\Livewire\ModeOfAssistance;

use App\Livewire\Forms\ModeOfAssistanceForm;
use App\Services\ActivityLoggerService;
use App\Services\ModeOfAssistanceService;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;

class Create extends Component
{
    use HasPlaceholder;

    public ModeOfAssistanceForm $form;

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
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);
        }

        try {
            $modeOfAssistance = app(ModeOfAssistanceService::class)->store($this->form->all());

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => 'Successfully created new mode of assistance',
            ]);

            ActivityLoggerService::logSuccess('Successfully created new mode of assistance', [
                'id' => $modeOfAssistance->id,
            ]);

            $this->form->reset();
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to save new mode of assistance');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.mode-of-assistance.create');
    }
}
