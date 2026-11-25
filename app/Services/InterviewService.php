<?php

namespace App\Services;

use App\Models\ApplicationClaimantAssignment;
use App\Models\Client;
use App\Models\Interview;
use App\Services\Auth\AccountAccess;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class InterviewService
{
    public function index(?string $userId = null)
    {
        return Interview::with(['client', 'client.user', 'claimantAssignment'])
            ->when($userId, function ($query) use ($userId) {
                $query->whereHas('client.user', function ($q) use ($userId) {
                    $q->where('id', $userId);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($interview) {
                return [
                    'id' => $interview->uuid,
                    'client' => $interview->client->fullname(),
                    'schedule' => $interview->schedule ? Carbon::parse($interview->schedule)->format('F j, Y g:i A') : null,
                    'status' => $interview->claimantAssignment?->ended_at ? 'archived ('.$interview->status.')' : $interview->status,
                    'remarks' => $interview->remarks,
                ];
            });
    }

    /**
     * Summary of store
     */
    public function store(array $data, string $clientUuid, ?string $expectedAssignmentUuid = null): Interview
    {
        return app(InterviewSchedulingService::class)->schedule($data, $clientUuid, $expectedAssignmentUuid);
    }

    public function done(string $uuid): Interview
    {
        app(AccountAccess::class)->assertWritable();
        $hint = Interview::findOrFail($uuid);

        return DB::transaction(function () use ($hint) {
            $application = null;
            $assignment = $hint->claimant_assignment_uuid
                ? ApplicationClaimantAssignment::findOrFail($hint->claimant_assignment_uuid) : null;
            if ($assignment) {
                $application = app(ApplicationProcessingService::class)->lock($assignment->application_uuid, $assignment->uuid);
                abort_unless($application->isActive(), 403);
            }
            $client = Client::whereKey($hint->client_uuid)->lockForUpdate()->firstOrFail();
            abort_if(! $assignment && $client->isUsed(true), 403);
            $interview = Interview::whereKey($hint->uuid)->lockForUpdate()->firstOrFail();
            Gate::authorize('complete', [$interview, $application]);
            abort_unless($interview->status === 'scheduled', 403);
            $interview->update(['status' => 'done']);
            ActivityLoggerService::logSuccess('Completed claimant interview', ['interview_uuid' => $interview->uuid]);

            return $interview;
        }, 3);
    }
}
