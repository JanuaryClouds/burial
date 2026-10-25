<?php

namespace App\Livewire\DocumentRequirement;

use App\Models\DocumentRequirement;
use App\Services\ActivityLoggerService;
use App\Services\DocumentRequirementService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public DocumentRequirement $document;

    #[Validate]
    public string $name;

    #[Validate('nullable|string|max:65535')]
    public ?string $description = null;

    #[Validate('required|string|max:255')]
    public string $source;

    #[Validate('required|boolean')]
    public bool $isRequired;

    #[Validate('required|boolean')]
    public bool $forMuslim;

    #[Validate('required|boolean')]
    public bool $isActive;

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('document_requirements')->ignore($this->document->id),
            ],
        ];
    }

    public function mount(DocumentRequirement $document)
    {
        $this->document = $document;
        $this->name = $document->name;
        $this->description = $document->description;
        $this->source = $document->source;
        $this->isRequired = $document->is_required;
        $this->forMuslim = $document->for_muslim;
        $this->isActive = $document->is_active;
    }

    public function update()
    {
        try {
            $this->validate();
        } catch (\Throwable $th) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        try {
            DB::transaction(function () {
                app(DocumentRequirementService::class)->update(
                    $this->document,
                    $this->all(),
                );

                ActivityLoggerService::logSuccess('Successfully updated a document requirement', [
                    'id' => $this->document->id,
                    'name' => $this->name,
                ]);

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Successfully updated a document requirement.',
                ]);
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Unable to update document requirement');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.document-requirement.edit');
    }
}
