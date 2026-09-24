<?php

namespace App\Livewire\Referral;

use App\Livewire\Forms\ReferralForm;
use App\Models\Application;
use App\Models\Referral;
use App\Models\WorkflowHistory;
use App\Services\ActivityLoggerService;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    use HasPlaceholder;

    public Application $application;

    public ReferralForm $form;

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
                Referral::create([
                    'application_uuid' => $this->application->uuid,
                    'referred_to' => $this->form->referred_to,
                    'reason' => $this->form->reason
                ]);

                if ($this->application->currentRecommendation()) {
                    $this->application->currentRecommendation()->update([
                        'status' => 'referred'
                    ]);
                }

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'title' => 'Application referred successfully',
                ]);

                ActivityLoggerService::logSuccess('Successfully referred an application', [
                    'application_uuid' => $this->application->uuid,
                    'reason' => $this->form->reason,
                    'referred_to' => $this->form->referred_to,
                    'rejected_by' => Auth::id()
                ]);

                $this->redirect(route('application.show', $this->application));
            });
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
