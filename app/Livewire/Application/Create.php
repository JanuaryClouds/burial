<?php

namespace App\Livewire\Application;

use App\Models\ApplicationSubmissionDraft;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\DocumentRequirement;
use App\Services\ApplicationSubmissionService;
use App\Services\FileserverPolicy;
use App\Services\ImageUploadValidator;
use App\Traits\Livewire\GuardsCitizenCreation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
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

    #[Locked]
    public ?string $draftUuid = null;

    #[Locked]
    public ?int $draftVersion = null;

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

        $draft = ApplicationSubmissionDraft::where('user_id', Auth::id())->whereNull('application_uuid')
            ->when($this->clientUuid, fn ($query) => $query->where('client_uuid', $this->clientUuid))
            ->when($this->beneficiaryUuid, fn ($query) => $query->where('beneficiary_uuid', $this->beneficiaryUuid))->latest()->first();
        if ($draft) {
            $this->clientUuid = $draft->client_uuid;
            $this->beneficiaryUuid = $draft->beneficiary_uuid;
            $this->relationshipId = (string) $draft->relationship_id;
            $this->draftUuid = $draft->uuid;
            $this->draftVersion = $draft->version;
        }

        $this->documents = DocumentRequirement::index()
            ->where('is_active', true)
            ->get();
    }

    public function updatedClientUuid(): void
    {
        $this->restoreSelectedDraft();
    }

    public function updatedBeneficiaryUuid(): void
    {
        $this->applicableDocumentCache = null;
        $this->restoreSelectedDraft();
    }

    private function restoreSelectedDraft(): void
    {
        $this->images = [];
        $draft = ApplicationSubmissionDraft::where('user_id', Auth::id())->whereNull('application_uuid')
            ->where('client_uuid', $this->clientUuid)->where('beneficiary_uuid', $this->beneficiaryUuid)->first();
        $this->draftUuid = $draft?->uuid;
        $this->draftVersion = $draft?->version;
        if ($draft) {
            $this->relationshipId = (string) $draft->relationship_id;
        }
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
            'draftUuid',
            'draftVersion',
            'agreedToTerms',
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
            if (! $this->agreedToTerms) {
                throw ValidationException::withMessages(['agreedToTerms' => 'Accept the terms before submitting.']);
            }
            if (! app(FileserverPolicy::class)->skip()) {
                $this->validateImages();
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        try {
            $service = app(ApplicationSubmissionService::class);
            $draft = $service->prepare($this->clientUuid, $this->beneficiaryUuid, (int) $this->relationshipId, $this->draftVersion);
            $this->draftUuid = $draft->uuid;
            $this->draftVersion = $draft->version;
            $application = $service->submit($draft->uuid, $this->draftVersion, $this->images);
            session()->forget(['client_uuid', 'beneficiary_uuid']);
            $this->dispatch('notification:alert', ['type' => 'success', 'text' => 'Your application has been submitted.']);
            $this->redirect(route('application.show', $application));
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());
        } catch (\Throwable $exception) {
            $this->images = [];
            $this->addError('images', 'Submission could not finish. Your draft and saved uploads are preserved. Select only missing documents before retrying.');
        }
    }

    public function render()
    {
        $documents = $this->applicableDocuments();
        $saved = $this->draftUuid ? (ApplicationSubmissionDraft::whereKey($this->draftUuid)->where('user_id', Auth::id())->first()->documents ?? []) : [];
        $skip = app(FileserverPolicy::class)->skip();
        $ready = $skip || $documents->every(fn ($document) => ! $document->is_required || isset($saved[$document->id]) || isset($this->images[$document->uploadKey()]));

        return view('livewire.application.create', ['applicableDocuments' => $documents,
            'skipImages' => $skip, 'savedDocuments' => $saved, 'documentsReady' => $ready,
        ]);
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
