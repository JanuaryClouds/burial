<?php

namespace App\Livewire\Cancellation;

use App\Livewire\Forms\CancellationForm;
use App\Models\Application;
use App\Models\Cancellation;
use App\Services\ActivityLoggerService;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    use HasPlaceholder;

    public Application $application;

    public CancellationForm $form;

    public function mount(Application $application)
    {
        $this->application = $application;
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
            DB::transaction(function () {
                Cancellation::create([
                    'application_uuid' => $this->application->uuid,
                    'reason' => $this->form->reason,
                    'cancelled_by' => Auth::id(),
                ]);

                if ($this->application->currentRecommendation()) {
                    $this->application->currentRecommendation()->update([
                        'status' => 'cancelled',
                    ]);
                }

                ActivityLoggerService::logSuccess('Successfully cancelled an application', [
                    'application_uuid' => $this->application->uuid,
                    'reason' => $this->form->reason,
                    'rejected_by' => Auth::id(),
                ]);

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'This application has been cancelled.',
                ]);

                $this->redirect(route('application.show', $this->application));
            });
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
