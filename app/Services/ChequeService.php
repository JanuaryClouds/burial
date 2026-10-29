<?php

namespace App\Services;

use App\Models\Cheque;
use App\Services\Auth\AccountAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/** Payment integrations must use the same application lock as claimant replacement. */
class ChequeService
{
    public function save(string $applicationUuid, string $expectedAssignmentUuid, array $data, ?string $chequeUuid = null): Cheque
    {
        app(AccountAccess::class)->assertWritable();
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
        Validator::make($data, [
            'obr_number' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:issued,claimed,cancelled'],
            'date_claimed' => ['nullable', 'date', 'required_if:status,claimed'],
            'date_issued' => ['nullable', 'date'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'cheque_number' => ['nullable', 'string', 'max:255'],
            'dv_number' => ['nullable', 'string', 'max:255'],
        ])->validate();

        return DB::transaction(function () use ($applicationUuid, $expectedAssignmentUuid, $data, $chequeUuid) {
            $application = app(ApplicationProcessingService::class)->lock($applicationUuid, $expectedAssignmentUuid);
            abort_unless($application->isActive(), 403);
            $cheque = $chequeUuid ? Cheque::where('application_uuid', $applicationUuid)->whereKey($chequeUuid)->lockForUpdate()->firstOrFail() : new Cheque;
            abort_if($cheque->exists && ($cheque->date_claimed || $cheque->status === 'claimed'), 403);
            $cheque->fill(collect($data)->only($cheque->getFillable())->except('application_uuid')->all());
            $cheque->application_uuid = $applicationUuid;
            $cheque->save();

            return $cheque;
        }, 3);
    }
}
