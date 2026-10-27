<?php

namespace App\Livewire\Closure;

use App\Livewire\Forms\ClosureForm;
use App\Models\Application;
use App\Services\ActivityLoggerService;
use App\Services\ApplicationProcessingService;
use App\Traits\Livewire\GuardsOfficeForm;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Create extends Component
{
    use GuardsOfficeForm;

    public function bootOfficeAccess(): void
    {
        abort_unless(auth()->user()?->canOfficeAction('Create Closure'), 403);
    }

    public Application $application;

    #[Locked]
    public ?string $expectedAssignmentUuid = null;

    public ClosureForm $form;

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->expectedAssignmentUuid = $application->current_claimant_assignment_uuid;

        if ($application->closure) {
            $this->form->setClosure($application);
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
                $this->application->uuid, $this->expectedAssignmentUuid, 'close', $this->form->all()
            );
            $this->redirect(route('application.show', $this->application));
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to close an application');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.closure.create');
    }
}
