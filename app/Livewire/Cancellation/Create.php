<?php

namespace App\Livewire\Cancellation;

use App\Livewire\Forms\CancellationForm;
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

    public CancellationForm $form;

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->expectedAssignmentUuid = $application->current_claimant_assignment_uuid;
    }

    public function save()
    {
        try {
            $this->form->validate();
        } catch (\Exception $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'title' => 'Unable to save application',
                'message' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        try {
            app(ApplicationProcessingService::class)->outcome(
                $this->application->uuid, $this->expectedAssignmentUuid, 'cancel', $this->form->all()
            );
            $this->redirect(route('application.show', $this->application));
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Unable to cancel an application');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.cancellation.create');
    }
}
