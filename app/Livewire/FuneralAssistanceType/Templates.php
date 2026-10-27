<?php

namespace App\Livewire\FuneralAssistanceType;

use App\Models\FuneralAssistanceType;
use App\Services\DocumentPlaceholderRegistry;
use App\Services\DocumentTemplateService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class Templates extends Component
{
    use WithFileUploads;

    #[Locked]
    public FuneralAssistanceType $funeralAssistanceType;

    #[Locked]
    public ?string $replacingUuid = null;

    public string $name = '';

    public $upload;

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
    }

    public function mount(FuneralAssistanceType $funeralAssistanceType): void
    {
        $this->funeralAssistanceType = $funeralAssistanceType;
    }

    public function replace(string $uuid): void
    {
        $template = $this->funeralAssistanceType->documentTemplates()->findOrFail($uuid);
        $this->resetValidation();
        $this->upload = null;
        $this->name = $template->name;
        $this->replacingUuid = $uuid;
    }

    public function cancel(): void
    {
        $this->reset('name', 'upload', 'replacingUuid');
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate(['name' => 'required|string|max:255', 'upload' => 'required|file|max:5120']);
        try {
            app(DocumentTemplateService::class)->save($this->funeralAssistanceType, $this->name, $this->upload, $this->replacingUuid);
            $this->cancel();
            session()->flash('templateStatus', 'Document template saved.');
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (ModelNotFoundException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            Log::error('Document template save failed.', ['exception_type' => $exception::class]);
            $this->addError('upload', 'The template could not be saved. Please try again.');
        }
    }

    public function remove(string $uuid): void
    {
        app(DocumentTemplateService::class)->remove($this->funeralAssistanceType, $uuid);
        if ($this->replacingUuid === $uuid) {
            $this->cancel();
        }
        session()->flash('templateStatus', 'Document template removed.');
    }

    public function render()
    {
        return view('livewire.funeral-assistance-type.templates', [
            'templates' => $this->funeralAssistanceType->documentTemplates()->get(),
            'placeholders' => app(DocumentPlaceholderRegistry::class)->definitions(),
        ]);
    }
}
