<?php

namespace App\Livewire\Assessment;

use App\Livewire\Forms\AssessmentForm;
use App\Models\Application;
use App\Models\Assessment;
use App\Services\ActivityLoggerService;
use App\Services\AssessmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Create extends Component
{
    public Application $application;

    #[Locked]
    public ?string $expectedAssignmentUuid = null;

    public Assessment $assessment;

    public AssessmentForm $form;

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->expectedAssignmentUuid = $application->current_claimant_assignment_uuid;
    }

    public function save()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        try {
            DB::transaction(function () {
                $assessment = app(AssessmentService::class)->store([
                    ...$this->form->all(), 'expected_assignment_uuid' => $this->expectedAssignmentUuid,
                ], $this->application->uuid);

                $this->form->reset();
                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Assessment created successfully',
                ]);

                ActivityLoggerService::logSuccess('Successfully created Assessment', [
                    'assessment_uuid' => $assessment->uuid,
                    'application_uuid' => $this->application->uuid,
                ]);

                $this->redirect(route('application.show', $this->application));
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to create assessment');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.assessment.create');
    }
}
