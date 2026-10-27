<?php

namespace App\Livewire\Referral;

use App\Livewire\Forms\ReferralForm;
use App\Models\Application;
use App\Services\ActivityLoggerService;
use App\Services\ApplicationProcessingService;
use App\Traits\Livewire\GuardsOfficeForm;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Create extends Component
{
    use GuardsOfficeForm;

    public function bootOfficeAccess(): void
    {
        abort_unless(auth()->user()?->canOfficeAction('Create Referral'), 403);
    }

    use HasPlaceholder;

    public Application $application;

    #[Locked]
    public ?string $expectedAssignmentUuid = null;

    public ReferralForm $form;

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
                $this->application->uuid, $this->expectedAssignmentUuid, 'refer', $this->form->all()
            );
            $this->redirect(route('application.show', $this->application));
        } catch (\Throwable $th) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Unable to refer an application');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.referral.create');
    }
}
