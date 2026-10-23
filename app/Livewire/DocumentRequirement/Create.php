<?php

namespace App\Livewire\DocumentRequirement;

use App\Livewire\Forms\DocumentRequirementForm;
use App\Services\ActivityLoggerService;
use App\Services\DocumentRequirementService;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    use HasPlaceholder;

    public DocumentRequirementForm $form;

    public function clear()
    {
        $this->form->reset();
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
                $newDocument = app(DocumentRequirementService::class)
                    ->store($this->form->all());

                ActivityLoggerService::logSuccess('Successfully created new document to submit', [
                    'id' => $newDocument->id,
                    'document_name' => $newDocument->name,
                ]);

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Successfully created new document to submit',
                ]);

                $this->form->reset();
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Unable to create new document requirement');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.document-requirement.create');
    }
}
