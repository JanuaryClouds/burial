<?php

namespace App\Livewire\Rejection;

use App\Livewire\Forms\RejectionForm;
use App\Models\Application;
use App\Models\Recommendation;
use App\Models\Rejection;
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

    public RejectionForm $form;

    public function mount(Application $application)
    {
        $this->application = $application;

        if($this->application->rejection){
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
            DB::transaction(function () {
                Rejection::updateOrCreate([
                    'application_uuid' => $this->application->uuid,
                    'reason' => $this->form->reason,
                    'rejected_by' => Auth::id()
                ]);

                WorkflowHistory::create([
                    'recommendation_uuid' => $this->application->currentRecommendation()->uuid,
                    'from_stage_uuid' => $this->application->workflowStage->uuid,
                    'to_stage_uuid' => null,
                    'date_in' => now(),
                    'date_out' => now(),
                    'reason' => $this->form->reason,
                    'processed_by' => Auth::id()
                ]);

                ActivityLoggerService::logSuccess('Successfully rejected application', [
                    'application_uuid' => $this->application->uuid,
                    'reason' => $this->form->reason, 
                    'rejected_by' => Auth::user()
                ]);

                $this->dispatch('refreshWorkflowHistory');

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Successfully rejected the application'
                ]);

                $this->js('window.location.reload();');
            });
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
