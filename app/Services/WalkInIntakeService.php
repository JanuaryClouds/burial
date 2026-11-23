<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationDocumentFile;
use App\Models\Beneficiary;
use App\Models\BeneficiaryFamily;
use App\Models\Client;
use App\Models\DocumentRequirement;
use App\Models\Religion;
use App\Models\WalkInIntake;
use App\Services\Auth\AccountAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WalkInIntakeService
{
    public const INVALID_CODE = 'The claim code is invalid, expired, or already used. Please contact CSWDO.';

    public function requiresDocuments(): bool
    {
        return ! app()->isLocal();
    }

    public function authorize(WalkInIntake $intake): void
    {
        Gate::authorize('walk-in-intake');
        abort_unless(auth()->user()->isSystemAdministrator() || $intake->encoded_by === auth()->id(), 403);
    }

    /** @return array<string, mixed> */
    public function validateData(array $data, bool $complete = false): array
    {
        $required = $complete ? 'required' : 'nullable';
        $rules = ['relationshipId' => [$required, 'integer', 'exists:relationships,id']];
        foreach (['identity', 'beneficiary'] as $group) {
            foreach (['first_name', 'middle_name', 'last_name', 'suffix'] as $field) {
                $rules["{$group}.{$field}"] = [in_array($field, ['first_name', 'last_name']) ? $required : 'nullable', 'string', 'max:255'];
            }
        }
        $rules += [
            'client.dateOfBirth' => [$required, 'date', 'before:today'],
            'client.contactNumber' => [$required, 'string', 'max:255'],
            'beneficiary.date_of_birth' => [$required, 'date', 'before_or_equal:today'],
            'beneficiary.date_of_death' => [$required, 'date', 'before_or_equal:today'],
            'beneficiary.pwd' => ['nullable', 'boolean'],
        ];
        if (filled(data_get($data, 'beneficiary.date_of_birth'))) {
            $rules['beneficiary.date_of_death'][] = 'after_or_equal:beneficiary.date_of_birth';
        }
        foreach (['demographic.sexId' => 'sexes', 'demographic.nationalityId' => 'nationalities',
            'demographic.religionId' => 'religions', 'social.civilId' => 'civil_statuses',
            'social.educationId' => 'educations', 'beneficiary.sex_id' => 'sexes', 'beneficiary.religion_id' => 'religions'] as $field => $table) {
            $rules[$field] = [$required, 'integer', 'exists:'.$table.',id'];
        }
        foreach (['philhealth', 'skill', 'income'] as $field) {
            $rules['social.'.$field] = ['nullable', 'string', 'max:255'];
        }
        foreach (['address', 'beneficiaryAddress'] as $group) {
            foreach (['regionCode', 'provinceCode', 'municipalityCode', 'barangayCode', 'street', 'houseNumber',
                'regionCode_display', 'provinceCode_display', 'municipalityCode_display', 'barangayCode_display'] as $field) {
                $rules[$group.'.'.$field] = [$complete && in_array($field, ['regionCode', 'barangayCode', 'street', 'houseNumber']) ? 'required' : 'nullable', 'string', 'max:255'];
            }
            if ($complete) {
                $rules[$group.'.provinceCode'][] = 'required_without:'.$group.'.municipalityCode';
                $rules[$group.'.municipalityCode'][] = 'required_without:'.$group.'.provinceCode';
            }
        }
        $rules['family'] = ['sometimes', 'array', 'max:50'];
        foreach (['name' => ['required', 'string', 'max:255'], 'age' => ['nullable', 'integer', 'min:0', 'max:130'],
            'sex_id' => ['required', 'integer', 'exists:sexes,id'], 'civil_id' => ['required', 'integer', 'exists:civil_statuses,id'],
            'relationship_id' => ['required', 'integer', 'exists:relationships,id'], 'occupation' => ['nullable', 'string', 'max:255'],
            'income' => ['nullable', 'string', 'max:64']] as $field => $rule) {
            $rules['family.*.'.$field] = $complete ? $rule : array_map(fn ($item) => $item === 'required' ? 'nullable' : $item, $rule);
        }

        return Validator::make($data, $rules)->validate();
    }

    public function saveDraft(?string $uuid, array $data, ?int $version): WalkInIntake
    {
        app(AccountAccess::class)->assertWritable();
        Gate::authorize('walk-in-intake');
        $data = $this->validateData($data);

        return DB::transaction(function () use ($uuid, $data, $version) {
            if ($uuid === null) {
                $intake = WalkInIntake::create(['encoded_by' => auth()->id(), 'data' => $data]);
            } else {
                $intake = $this->draft($uuid, $version);
                $intake->forceFill(['data' => $data, 'version' => $intake->version + 1])->save();
            }
            ActivityLoggerService::logSuccess('Saved walk-in intake draft', ['intake_uuid' => $intake->uuid]);

            return $intake->refresh();
        });
    }

    private function draft(string $uuid, ?int $version): WalkInIntake
    {
        $intake = WalkInIntake::whereKey($uuid)->lockForUpdate()->firstOrFail();
        $this->authorize($intake);
        if ($intake->application_uuid || $intake->version !== $version) {
            throw ValidationException::withMessages(['intake' => 'This intake changed or was already submitted. Reload it before continuing.']);
        }

        return $intake;
    }

    public function applicableDocuments(array $data): Collection
    {
        $isMuslim = Religion::find(data_get($data, 'beneficiary.religion_id'))?->name === 'Islam';

        return DocumentRequirement::index()->where('is_active', true)->get()
            ->filter(fn ($document) => ! $document->for_muslim || $isMuslim);
    }

    public function duplicates(WalkInIntake $intake): Collection
    {
        $this->authorize($intake);
        $name = mb_strtolower(trim(data_get($intake->data, 'identity.first_name').' '.data_get($intake->data, 'identity.last_name')));
        $birthday = data_get($intake->data, 'client.dateOfBirth');
        $phone = data_get($intake->data, 'client.contactNumber');

        return Client::with('user')->whereHas('claimantAssignments')->lazy(100)
            ->filter(fn (Client $client) => ($birthday && $client->date_of_birth === $birthday)
                || ($phone && $client->contact_number === $phone)
                || ($name !== '' && mb_strtolower(trim($client->identity('first_name').' '.$client->identity('last_name'))) === $name))
            ->take(20)->collect();
    }

    /** @return array{intake: WalkInIntake, code: string} */
    public function submit(string $uuid, int $version, array $images, bool $reviewed): array
    {
        app(AccountAccess::class)->assertWritable();
        $intake = WalkInIntake::findOrFail($uuid);
        $this->authorize($intake);
        $warning = app(WalkInFileserverService::class)->submissionWarning();
        if ($warning !== null) {
            throw ValidationException::withMessages(['fileserver' => $warning]);
        }
        $data = $this->validateData($intake->data, true);
        if (! $reviewed) {
            throw ValidationException::withMessages(['reviewed' => 'Review the applicant information and possible duplicates before submitting.']);
        }
        $documents = $this->applicableDocuments($data);
        $stored = $intake->documents ?? [];
        $destination = app(ImageService::class)->destination();
        foreach ($documents as $document) {
            if (isset($stored[$document->id]) && $stored[$document->id]['storage'] !== $destination) {
                throw ValidationException::withMessages(['images' => 'Saved documents belong to a different fileserver environment. Contact the administrator before submitting this intake.']);
            }
        }
        foreach ($images as $key => $file) {
            if (! $documents->contains('id', $key)) {
                throw ValidationException::withMessages(['images' => 'An uploaded document is no longer applicable. Reload the intake.']);
            }
            app(ImageUploadValidator::class)->validate($file, 'images.'.$key);
            Validator::make(['images' => [$key => $file]], ['images.'.$key => ['file', 'max:10240']])->validate();
        }
        foreach ($documents as $document) {
            if ($this->requiresDocuments() && $document->is_required && ! isset($images[$document->id]) && ! isset($stored[$document->id])) {
                throw ValidationException::withMessages(['images.'.$document->id => 'Upload '.$document->name.' before submission.']);
            }
        }
        foreach ($images as $key => $file) {
            DB::transaction(function () use ($uuid, $version, $key, $file, $documents) {
                $draft = $this->draft($uuid, $version);
                $stored = $draft->documents ?? [];
                if (isset($stored[$key])) {
                    throw ValidationException::withMessages(['images.'.$key => 'This document is already saved. Remove the new selection to reuse it.']);
                }
                $stored[$key] = app(ImageService::class)->stageWalkInDocument($documents->firstWhere('id', $key), $file);
                $draft->forceFill(['documents' => $stored])->save();
                ActivityLoggerService::logSuccess('Saved walk-in intake document', ['intake_uuid' => $uuid, 'document_requirement_id' => $key]);
            });
        }

        return DB::transaction(function () use ($uuid, $version) {
            $intake = $this->draft($uuid, $version);
            $data = $this->validateData($intake->data, true);
            $client = new Client(['date_of_birth' => $data['client']['dateOfBirth'], 'contact_number' => $data['client']['contactNumber']]);
            $client->forceFill(['user_id' => null, 'walk_in_intake_uuid' => $intake->uuid, 'identity_snapshot' => $data['identity']])->save();
            app(ClientDemographicsService::class)->store($data['demographic'], $client);
            app(ClientSocialInfoService::class)->store(array_replace(['philhealth' => null, 'skill' => null, 'income' => null], $data['social']), $client);
            app(AddressService::class)->store($this->address($data['address']), $client);
            $beneficiary = Beneficiary::create([...$data['beneficiary'], 'created_by' => $intake->encoded_by]);
            app(AddressService::class)->store($this->address($data['beneficiaryAddress']), $beneficiary);
            foreach ($data['family'] ?? [] as $member) {
                BeneficiaryFamily::create([...$member, 'beneficiary_uuid' => $beneficiary->uuid]);
            }
            $application = app(ApplicationService::class)->storeWalkIn($client, $beneficiary, (int) $data['relationshipId']);
            foreach ($this->applicableDocuments($data) as $document) {
                $reference = ($intake->documents ?? [])[$document->id] ?? null;
                if (! $reference && $this->requiresDocuments() && $document->is_required) {
                    throw ValidationException::withMessages(['images' => 'Required documents changed. Reload and complete the missing uploads.']);
                }
                if ($reference) {
                    ApplicationDocumentFile::create([...$reference, 'application_uuid' => $application->uuid, 'document_requirement_id' => $document->id]);
                }
            }
            $code = $this->issueCode($intake);
            $intake->forceFill(['application_uuid' => $application->uuid])->save();
            ActivityLoggerService::logSuccess('Submitted walk-in application', ['intake_uuid' => $intake->uuid, 'application_uuid' => $application->uuid]);

            return ['intake' => $intake, 'code' => $code];
        });
    }

    /** Resolve address labels from PSA rather than trusting browser-supplied labels. */
    private function address(array $data): array
    {
        $psa = app(PsaClassificationService::class);
        $regions = $psa->getRegionOptions();
        $provinces = $psa->getProvinceOptions($data['regionCode']);
        $municipalities = $psa->getMunicipalityOptions($data['regionCode']);
        $parent = $data['municipalityCode'] ?? $data['provinceCode'] ?? null;
        $barangays = $parent ? $psa->getBarangayOptions($parent) : [];
        foreach (['regionCode' => $regions, 'provinceCode' => $provinces, 'municipalityCode' => $municipalities, 'barangayCode' => $barangays] as $field => $options) {
            if (filled($data[$field] ?? null) && ! isset($options[$data[$field]])) {
                throw ValidationException::withMessages(['intake' => 'An address selection is no longer valid. Please select the address again.']);
            }
            $data[$field.'_display'] = $options[$data[$field] ?? ''] ?? null;
        }

        return array_replace(['provinceCode' => null, 'municipalityCode' => null], $data);
    }

    private function issueCode(WalkInIntake $intake): string
    {
        $code = bin2hex(random_bytes(32));
        $intake->forceFill(['claim_code_hash' => hash('sha256', $code), 'claim_expires_at' => now()->addDays(90)])->save();

        return $code;
    }

    public function replaceCode(string $uuid, string $reason, bool $identityVerified): string
    {
        app(AccountAccess::class)->assertWritable();
        Gate::authorize('walk-in-intake');
        Validator::make(['reason' => $reason, 'identityVerified' => $identityVerified], ['reason' => ['required', 'string', 'max:2000'], 'identityVerified' => ['accepted']])->validate();

        return DB::transaction(function () use ($uuid, $reason) {
            $intake = WalkInIntake::whereKey($uuid)->lockForUpdate()->firstOrFail();
            abort_unless($intake->application_uuid !== null, 403);
            if ($intake->claimed_by !== null) {
                throw ValidationException::withMessages(['intake' => 'These records have already been linked. A replacement code cannot be issued.']);
            }
            $code = $this->issueCode($intake);
            ActivityLoggerService::logSuccess('Replaced walk-in claim code after identity verification', ['intake_uuid' => $uuid, 'reason' => trim($reason)]);

            return $code;
        });
    }

    public function claim(string $code): Application
    {
        $user = app(AccountAccess::class)->assertWritable();
        Gate::authorize('claim-walk-in');

        return DB::transaction(function () use ($code, $user) {
            $hash = hash('sha256', trim($code));
            $accepted = WalkInIntake::where('claim_code_hash', $hash)->whereNull('claimed_by')
                ->whereNotNull('application_uuid')->where('claim_expires_at', '>', now())
                ->update(['claimed_by' => $user->id, 'claimed_at' => now()]);
            if ($accepted !== 1) {
                throw ValidationException::withMessages(['claim_code' => self::INVALID_CODE]);
            }
            $intake = WalkInIntake::where('claim_code_hash', $hash)->lockForUpdate()->firstOrFail();
            $client = $intake->client()->lockForUpdate()->firstOrFail();
            if ($client->user_id !== null) {
                throw ValidationException::withMessages(['claim_code' => self::INVALID_CODE]);
            }
            // Only the account association changes; submitted facts and claimant snapshots remain immutable.
            if (Client::whereKey($client->uuid)->whereNull('user_id')->update(['user_id' => $user->id]) !== 1) {
                throw ValidationException::withMessages(['claim_code' => self::INVALID_CODE]);
            }
            $intake->forceFill(['claim_code_hash' => null])->save();
            ActivityLoggerService::logSuccess('Linked walk-in records to citizen account', ['intake_uuid' => $intake->uuid, 'application_uuid' => $intake->application_uuid]);

            return Application::whereKey($intake->application_uuid)->firstOrFail();
        }, 3);
    }
}
