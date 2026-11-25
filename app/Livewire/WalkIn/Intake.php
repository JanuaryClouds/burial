<?php

namespace App\Livewire\WalkIn;

use App\Models\CivilStatus;
use App\Models\Education;
use App\Models\Nationality;
use App\Models\Relationship;
use App\Models\Religion;
use App\Models\Sex;
use App\Models\WalkInIntake;
use App\Services\FileserverPolicy;
use App\Services\PsaClassificationService;
use App\Services\WalkInIntakeService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class Intake extends Component
{
    use WithFileUploads;

    #[Locked]
    public ?string $intakeUuid = null;

    #[Locked]
    public ?int $version = null;

    #[Locked]
    public bool $readyForReview = false;

    public array $data = [];

    public array $images = [];

    public bool $reviewed = false;

    public function mount(?WalkInIntake $intake = null): void
    {
        $this->authorize('walk-in-intake');
        if ($intake?->exists) {
            app(WalkInIntakeService::class)->authorize($intake);
            abort_if($intake->application_uuid !== null, 403);
            $this->intakeUuid = $intake->uuid;
            $this->version = $intake->version;
            $this->data = $intake->data;
        }
    }

    public function updatedData(mixed $value, ?string $key): void
    {
        $this->readyForReview = false;
        $this->reviewed = false;
        if ($key === null) {
            return;
        }
        [$group, $field] = array_pad(explode('.', $key, 2), 2, null);
        if (! in_array($group, ['address', 'beneficiaryAddress'])) {
            return;
        }
        $reset = match ($field) {
            'regionCode' => ['provinceCode', 'municipalityCode', 'barangayCode'],
            'provinceCode' => ['municipalityCode', 'barangayCode'],
            'municipalityCode' => ['provinceCode', 'barangayCode'],
            default => [],
        };
        foreach ($reset as $dependent) {
            $this->data[$group][$dependent] = null;
        }
    }

    public function save(): void
    {
        $intake = app(WalkInIntakeService::class)->saveDraft($this->intakeUuid, $this->data, $this->version);
        $this->intakeUuid = $intake->uuid;
        $this->version = $intake->version;
        $this->data = $intake->data;
        $this->readyForReview = true;
        $this->reviewed = false;
        $this->resetErrorBag();
        $this->dispatch('notification:toast', ['type' => 'success', 'text' => 'Draft saved. Review the information and possible duplicates before submission.']);
    }

    public function checkFileserver(): void
    {
        $this->authorize('walk-in-intake');
        $this->resetValidation('fileserver');
    }

    public function removeImage(int $id): void
    {
        $this->authorize('walk-in-intake');
        unset($this->images[$id]);
    }

    public function addFamilyMember(): void
    {
        $this->authorize('walk-in-intake');
        if (count($this->data['family'] ?? []) < 50) {
            $this->data['family'][] = [];
        }
        $this->readyForReview = false;
        $this->reviewed = false;
    }

    public function removeFamilyMember(int $index): void
    {
        $this->authorize('walk-in-intake');
        unset($this->data['family'][$index]);
        $this->data['family'] = array_values($this->data['family'] ?? []);
        $this->readyForReview = false;
        $this->reviewed = false;
    }

    public function submit(): void
    {
        $service = app(WalkInIntakeService::class);
        $intake = $this->intakeUuid ? WalkInIntake::findOrFail($this->intakeUuid) : null;
        if (! $intake || ! $this->readyForReview || $service->validateData($this->data) != $intake->data) {
            throw ValidationException::withMessages(['intake' => 'Save the draft and review it before submitting.']);
        }
        try {
            $result = $service->submit($intake->uuid, $this->version, $this->images, $this->reviewed);
        } catch (QueryException $exception) {
            Log::error('Walk-in application database save failed.', [
                'intake_uuid' => $intake->uuid,
                'sql_state' => $exception->errorInfo[0] ?? null,
                'driver_code' => $exception->errorInfo[1] ?? null,
            ]);
            $this->images = [];
            $this->addError('intake', 'The application could not be saved to the database. Your draft and saved documents are preserved. Ask the superadmin to check the database migrations before trying again.');

            return;
        } catch (\RuntimeException $exception) {
            $this->images = [];
            $this->addError('images', 'Submission could not finish. Saved documents remain with the draft. Please check the upload service and select only the missing documents.');

            return;
        }
        session(['walk_in_receipt' => ['uuid' => $intake->uuid, 'code' => Crypt::encryptString($result['code']), 'expires' => now()->addMinutes(5)->timestamp]]);
        $this->redirect(route('walk-in.receipt'));
    }

    public function render(): View
    {
        $this->authorize('walk-in-intake');
        $service = app(WalkInIntakeService::class);
        $intake = $this->intakeUuid ? WalkInIntake::findOrFail($this->intakeUuid) : null;
        if ($intake) {
            $service->authorize($intake);
        }
        $psa = app(PsaClassificationService::class);
        $addresses = [];
        foreach (['address', 'beneficiaryAddress'] as $group) {
            $region = data_get($this->data, $group.'.regionCode');
            $parent = data_get($this->data, $group.'.municipalityCode') ?: data_get($this->data, $group.'.provinceCode');
            $addresses[$group] = [
                'regionCode' => $psa->getRegionOptions(),
                'provinceCode' => $region ? $psa->getProvinceOptions($region) : [],
                'municipalityCode' => $region ? $psa->getMunicipalityOptions($region) : [],
                'barangayCode' => $parent ? $psa->getBarangayOptions($parent) : [],
            ];
        }

        return view('livewire.walk-in.intake', [
            'addresses' => $addresses, 'intake' => $intake,
            'duplicates' => $intake && $this->readyForReview ? $service->duplicates($intake) : collect(),
            'documents' => $service->applicableDocuments($this->data),
            'requiresDocuments' => $service->requiresDocuments(),
            'fileserverWarning' => app(FileserverPolicy::class)->warning($this->images !== [] || ($service->requiresDocuments() && $service->applicableDocuments($this->data)->contains('is_required', true))),
            'options' => [
                'sex' => Sex::pluck('name', 'id')->all(),
                'religion' => Religion::pluck('name', 'id')->all(),
                'nationality' => Nationality::pluck('name', 'id')->all(),
                'civil' => CivilStatus::pluck('name', 'id')->all(),
                'education' => Education::pluck('name', 'id')->all(),
                'relationship' => Relationship::pluck('name', 'id')->all(),
            ],
        ]);
    }
}
