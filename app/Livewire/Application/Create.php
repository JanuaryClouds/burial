<?php

namespace App\Livewire\Application;

use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\DocumentRequirement;
use App\Services\ActivityLoggerService;
use App\Services\ApplicationService;
use App\Services\ImageService;
use App\Services\ImageUploadValidator;
use App\Traits\Livewire\GuardsCitizenCreation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use GuardsCitizenCreation;
    use WithFileUploads;

    public Collection $clientOptions;

    public Collection $beneficiaryOptions;

    public Collection $documents;

    #[Rule('required|exists:clients,uuid')]
    public ?string $clientUuid = null;

    #[Rule('required|exists:beneficiaries,uuid')]
    public ?string $beneficiaryUuid = null;

    #[Rule('required|exists:relationships,id')]
    public string $relationshipId;

    public ?Client $client = null;

    public ?Beneficiary $beneficiary = null;

    public $images = [];

    public bool $agreedToTerms = false;

    // Private state lasts only for this Livewire request and cannot be supplied by the browser.
    private ?Collection $applicableDocumentCache = null;

    public function mount()
    {
        $this->clientOptions = Client::unused()
            ->with('user')
            ->where('user_id', '=', Auth::id())
            ->orderByDesc('created_at')
            ->get()
            ->mapWithKeys(fn ($client) => [$client->uuid => $client->fullname().' (created in '.Carbon::parse($client->created_at)->format('d M Y, h:i A').')']);

        $this->beneficiaryOptions = Beneficiary::whereDoesntHave('application')
            ->where('created_by', '=', Auth::id())
            ->orderBy('created_at')
            ->get()
            ->mapWithKeys(fn ($beneficiary) => [$beneficiary->uuid => $beneficiary->fullname().' (created in '.Carbon::parse($beneficiary->created_at)->format('d M Y, h:i A').')']);

        if (session()->has('client_uuid')) {
            $this->clientUuid = session('client_uuid');
        }

        if (session()->has('beneficiary_uuid')) {
            $this->beneficiaryUuid = session('beneficiary_uuid');
        }

        $this->documents = DocumentRequirement::index()
            ->where('is_active', true)
            ->get();
    }

    public function removeImage(string $key)
    {
        unset($this->images[$key]);
    }

    public function clearForm()
    {
        $this->reset([
            'clientUuid',
            'beneficiaryUuid',
            'relationshipId',
            'images',
            'client',
            'beneficiary',
        ]);

        $this->dispatch('notification:alert', [
            'type' => 'success',
            'title' => 'Form Cleared',
            'text' => 'The form has been cleared. You can now start a new application.',
        ]);
    }

    public function save()
    {
        try {
            $this->validate();
            $this->validateImages();
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        $imageService = App(ImageService::class);

        try {
            DB::transaction(function () use ($imageService) {
                $application = app(ApplicationService::class)->store(
                    Client::findOrFail($this->clientUuid),
                    Beneficiary::findOrFail($this->beneficiaryUuid),
                    (int) $this->relationshipId,
                );

                foreach ($this->images as $key => $file) {
                    if (! $file) {
                        continue;
                    }

                    $document = $this->applicableDocuments()->first(fn ($document) => $document->uploadKey() === $key);
                    abort_unless($document, 422);
                    $imageService->postDocument($application, $document, $file);
                }

                ActivityLoggerService::logSuccess('Successfully created application', [
                    'application_uuid' => $application->uuid,
                    'images_submitted' => count($this->images),
                ]);

                $this->reset();

                session()->forget(['client_uuid', 'beneficiary_uuid']);

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Your application has been submitted.',
                ]);

                $this->redirect(route('application.show', $application));
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to submit application');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.application.create', ['applicableDocuments' => $this->applicableDocuments()]);
    }

    private function applicableDocuments(): Collection
    {
        if ($this->applicableDocumentCache !== null) {
            return $this->applicableDocumentCache;
        }
        $beneficiary = $this->beneficiaryUuid
            ? Beneficiary::with('religion')->where('created_by', Auth::id())->find($this->beneficiaryUuid)
            : null;
        $isMuslim = $beneficiary?->religion?->name === 'Islam';

        // Re-read CMS configuration; public Livewire properties are not an authority.
        return $this->applicableDocumentCache = DocumentRequirement::index()->where('is_active', true)->get()
            ->filter(fn ($document) => ! $document->for_muslim || $isMuslim);
    }

    private function validateImages(): void
    {
        if (! is_array($this->images)) {
            throw ValidationException::withMessages(['images' => 'Please upload one image per document field.']);
        }
        $allowed = $this->applicableDocuments()->map(fn ($document) => $document->uploadKey())->all();
        foreach ($this->images as $key => $file) {
            if (! in_array($key, $allowed, true)) {
                throw ValidationException::withMessages(['images' => 'An uploaded document is no longer applicable. Please reload the form.']);
            }
            if ($file === null) {
                continue;
            }
            app(ImageUploadValidator::class)->validate($file, 'images.'.$key);
        }
    }
}
