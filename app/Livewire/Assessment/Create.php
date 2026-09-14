<?php

namespace App\Livewire\Assessment;

use App\Models\Application;
use App\Models\Assessment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    public Application $application;

    public Assessment $assessment;

    #[Validate('required|string|max:65535')]
    public string $problem_presented;

    #[Validate('required|string|max:65535')]
    public string $swa;

    public function mount(Application $application)
    {
        $this->application = $application;
    }

    public function save()
    {
        $this->validate();

        try {
            DB::transaction(function() {
                $assessment = Assessment::updateOrCreate([
                    'application_uuid' => $this->application->uuid,
                    'problem_presented' => $this->problem_presented,
                    'swa' => $this->swa,
                ]);
        
                activity()
                    ->withProperties([
                        'assessment' => $assessment->uuid,
                        'application' => $this->application->uuid,
                        'ip' => request()->ip(),
                        'browser' => request()->userAgent(),
                    ])
                    ->causedBy(Auth::user()->id)
                    ->log('Created an assessment');
        
                $this->reset('problem_presented', 'swa');
                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Assessment created successfully',
                ]);
        
                $this->redirect(route('application.show', $this->application));
            });
        } catch (\Throwable $th) {
            if (app()->hasDebugModeEnabled()) {
                $this->dispatch('notification:alert', [
                    'type' => 'error',
                    'text' => $th->getMessage()
                ]);
            } else {
                $this->dispatch('notification:alert', [
                    'type' => 'error',
                    'text' => 'Something went wrong. Try again later.'
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.assessment.create');
    }
}
