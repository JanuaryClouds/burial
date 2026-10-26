<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationClaimantAssignment;
use App\Models\Client;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ClaimantReplacementService
{
    public function replace(string $applicationUuid, array $data): ApplicationClaimantAssignment
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
        Validator::make($data, [
            'client_uuid' => ['required', 'uuid', 'exists:clients,uuid'],
            'relationship_id' => ['required', 'integer', 'exists:relationships,id'],
            'reason' => ['required', 'string', 'max:5000'],
            'expected_assignment_uuid' => ['required', 'uuid'],
        ])->validate();

        return DB::transaction(function () use ($applicationUuid, $data) {
            // The same parent lock is used by every processing/outcome writer.
            $application = Application::whereKey($applicationUuid)->lockForUpdate()->firstOrFail();
            $application->load(['client', 'rejection', 'cancellation', 'referral', 'closure']);
            Gate::authorize('changeClaimant', $application);
            $application->assertAssignment($data['expected_assignment_uuid']);
            $settings = SystemSetting::query()->lockForUpdate()->firstOrFail();
            if ($application->claimantAssignments()->count() - 1 >= $settings->claimant_change_limit) {
                throw ValidationException::withMessages(['clientUuid' => 'The claimant change limit has been reached.']);
            }
            if ($application->hasPaymentBlock()) {
                throw ValidationException::withMessages(['clientUuid' => 'An issued cheque or claimed benefit prevents claimant replacement.']);
            }
            $client = Client::whereKey($data['client_uuid'])->lockForUpdate()->firstOrFail();
            if ($client->user_id === $application->client->user_id || $client->isUsed(true)) {
                throw ValidationException::withMessages(['clientUuid' => 'Choose a different account with a client record that has never been used.']);
            }
            $this->validateDraft($client);
            $old = $application->claimantAssignments()->whereKey($application->current_claimant_assignment_uuid)->firstOrFail();
            $old->update(['ended_at' => now()]);
            $assignment = ApplicationClaimantAssignment::create([
                'application_uuid' => $application->uuid,
                'client_uuid' => $client->uuid,
                'relationship_id' => $data['relationship_id'],
                'sequence' => $old->sequence + 1,
                'started_at' => now(),
                'assigned_by' => auth()->id(),
                'reason' => trim($data['reason']),
                'identity_snapshot' => ApplicationClaimantAssignment::snapshot($client),
            ]);
            $application->forceFill([
                'client_uuid' => $client->uuid,
                'relationship_id' => $data['relationship_id'],
                'current_claimant_assignment_uuid' => $assignment->uuid,
                'current_recommendation_uuid' => null,
                'current_workflow_stage_uuid' => null,
            ])->save();
            ActivityLoggerService::logSuccess('Replaced application claimant and restarted processing', [
                'application_uuid' => $application->uuid,
                'previous_assignment_uuid' => $old->uuid,
                'claimant_assignment_uuid' => $assignment->uuid,
                'reason' => $assignment->reason,
            ]);

            return $assignment;
        }, 3);
    }

    public function validateDraft(Client $client): void
    {
        $client->load(['user', 'demographic', 'socialInfo', 'address']);
        $values = [
            'account' => $client->user?->id,
            'date_of_birth' => $client->date_of_birth,
            'contact_number' => $client->contact_number,
            'sex_id' => $client->demographic?->sex_id,
            'nationality_id' => $client->demographic?->nationality_id,
            'religion_id' => $client->demographic?->religion_id,
            'civil_id' => $client->socialInfo?->civil_id,
            'education_id' => $client->socialInfo?->education_id,
            'philhealth' => $client->socialInfo?->philhealth,
            'skill' => $client->socialInfo?->skill,
            'income' => $client->socialInfo?->income,
            'region_code' => $client->address?->region_code,
            'province_code' => $client->address?->province_code,
            'municipality_code' => $client->address?->municipality_code,
            'province_name' => $client->address?->province_name,
            'municipality_name' => $client->address?->municipality_name,
            'barangay_code' => $client->address?->barangay_code,
            'street' => $client->address?->street,
            'house_number' => $client->address?->house_number,
        ];
        $validator = Validator::make($values, [
            'account' => ['required', 'exists:users,id'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'contact_number' => ['required', 'string', 'max:255'],
            'sex_id' => ['required', 'exists:sexes,id'],
            'nationality_id' => ['required', 'exists:nationalities,id'],
            'religion_id' => ['required', 'exists:religions,id'],
            'civil_id' => ['required', 'exists:civil_statuses,id'],
            'education_id' => ['required', 'exists:educations,id'],
            'philhealth' => ['nullable', 'string', 'max:255'],
            'skill' => ['nullable', 'string', 'max:255'],
            'income' => ['nullable', 'string', 'max:255'],
            'region_code' => ['required', 'string'],
            'province_code' => ['nullable', 'required_without:municipality_code', 'string'],
            'municipality_code' => ['nullable', 'required_without:province_code', 'string'],
            'province_name' => ['nullable', 'required_without:municipality_code', 'string'],
            'municipality_name' => ['nullable', 'required_without:province_code', 'string'],
            'barangay_code' => ['required', 'string'],
            'street' => ['required', 'string', 'max:255'],
            'house_number' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            throw ValidationException::withMessages(['clientUuid' => 'Complete the client draft first: '.implode(' ', $validator->errors()->all())]);
        }
        if (blank($client->socialInfo)) {
            throw ValidationException::withMessages(['clientUuid' => 'Complete the client social information first.']);
        }
    }
}
