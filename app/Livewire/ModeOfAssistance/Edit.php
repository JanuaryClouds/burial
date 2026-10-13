<?php

namespace App\Livewire\ModeOfAssistance;

use App\Livewire\Forms\ModeOfAssistanceForm;
use App\Models\ModeOfAssistance;
use App\Services\ActivityLoggerService;
use App\Services\ModeOfAssistanceService;
use Livewire\Component;

class Edit extends Component
{
    public ModeOfAssistanceForm $form;

    public ?ModeOfAssistance $modeOfAssistance;

    public function mount(ModeOfAssistance $modeOfAssistance)
    {
        $this->modeOfAssistance = $modeOfAssistance;

        $this->form->setModeOfAssistance($modeOfAssistance);
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
            app(ModeOfAssistanceService::class)->update(
                $this->form->all(),
                $this->modeOfAssistance
            );

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => "Successfully updated mode of assistance"
            ]);

            ActivityLoggerService::logSuccess('Successfully updated mode of assistance', [
                'id' => $this->modeOfAssistance->id,
            ]);

            $this->form->reset();
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown')
            ]);

            ActivityLoggerService::logException($th, "Failed to update mode of assistance");

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.mode-of-assistance.edit');
    }
}
