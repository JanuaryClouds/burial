<?php

namespace App\Livewire\Workflow;

use App\Livewire\Forms\WorkflowForm;
use App\Services\ActivityLoggerService;
use App\Services\WorkflowService;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;

class Create extends Component
{
    use HasPlaceholder;

    public WorkflowForm $form;

    public function clear()
    {
        $this->form->reset();
    }

    public function save()
    {
        try {
            $this->form->validate();
        } catch (\Throwable $th) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        try {
            $workflow = app(WorkflowService::class)->store($this->form->all());

            ActivityLoggerService::logSuccess('Workflow created successfully', [
                'name' => $workflow->name,
            ]);

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => 'Workflow created successfully',
            ]);

            $this->form->reset();
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to create workflow');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.workflow.create');
    }
}
