<?php

namespace App\Livewire\Closure;

use App\Livewire\Forms\ClosureForm;
use App\Models\Application;
use App\Services\ActivityLoggerService;
use App\Services\ClosureService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public Application $application;

    public ClosureForm $form;

    public function mount(Application $application)
    {
        $this->application = $application;

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
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.validation')
            ]);
        }

        try {
            DB::transaction(function () {
                app(ClosureService::class)->store(
                    $this->form->all(),
                    $this->application
                );

                ActivityLoggerService::logSuccess('Successfully closed an application', [
                    'application_uuid' => $this->application->uuid
                ]);

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Successfully closed this application'
                ]);
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown')
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
