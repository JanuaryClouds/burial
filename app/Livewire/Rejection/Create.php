<?php

namespace App\Livewire\Rejection;

use App\Livewire\Forms\RejectionForm;
use App\Models\Application;
use App\Services\ActivityLoggerService;
use App\Services\ApplicationProcessingService;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Create extends Component
{
    use HasPlaceholder;

    public Application $application;

    #[Locked]
    public ?string $expectedAssignmentUuid = null;

    public RejectionForm $form;

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->expectedAssignmentUuid = $application->current_claimant_assignment_uuid;

        if ($this->application->rejection) {
            $this->form->setRejection($this->application->rejection);
        }
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
            app(ApplicationProcessingService::class)->outcome(
                $this->application->uuid, $this->expectedAssignmentUuid, 'reject', $this->form->all()
            );
            $this->redirect(route('application.show', $this->application));
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Unable to reject an application');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.rejection.create');
    }
}
