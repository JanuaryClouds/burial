<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Client;
use App\Models\Interview;
use App\Models\InterviewNotificationAttempt;
use App\Models\InterviewSchedulingRequest;
use App\Models\SystemSetting;
use App\Services\Auth\AccountAccess;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InterviewSchedulingService
{
    public function schedule(array $data, string $clientUuid, ?string $assignment): Interview
    {
        if (DB::transactionLevel() > 0) {
            throw new \LogicException('Schedule interviews outside business transactions.');
        }
        Validator::make($data, ['schedule' => ['required', 'date', 'after_or_equal:now']])->validate();
        $slot = Carbon::parse($data['schedule'])->format('Y-m-d H:i:s');
        $request = DB::transaction(function () use ($clientUuid, $assignment, $slot) {
            $client = $this->client($clientUuid, $assignment);
            $snapshot = $this->snapshot($client, $slot);
            $this->configuration($snapshot);
            $existing = InterviewSchedulingRequest::where('client_uuid', $clientUuid)->where('claimant_assignment_uuid', $assignment)
                ->where('schedule', $slot)->where('status', '!=', 'invalidated')->latest()->lockForUpdate()->first();
            if ($existing && $existing->snapshot === $snapshot) {
                return $existing;
            }
            $this->assertNoInterview($clientUuid, $assignment);
            $older = InterviewSchedulingRequest::where('client_uuid', $clientUuid)->where('claimant_assignment_uuid', $assignment)
                ->whereNotIn('status', ['completed', 'invalidated'])->lockForUpdate()->get();
            foreach ($older as $old) {
                if (InterviewNotificationAttempt::where('request_uuid', $old->uuid)->where('status', 'sending')->where('claimed_at', '>', now()->subSeconds($this->grace()))->exists()) {
                    $this->fail('Another notification request is still sending. Wait before changing the schedule.');
                }
                $old->update(['status' => 'invalidated']);
            }

            return InterviewSchedulingRequest::create(['client_uuid' => $clientUuid, 'claimant_assignment_uuid' => $assignment,
                'requested_by' => auth()->id(), 'schedule' => $slot, 'snapshot' => $snapshot, 'status' => 'pending']);
        });

        return $this->resume($request->uuid);
    }

    public function grace(): int
    {
        return max(300, 10 * 10);
    }

    public function latest(string $clientUuid, ?string $assignment): ?InterviewSchedulingRequest
    {
        return InterviewSchedulingRequest::where('client_uuid', $clientUuid)->where('claimant_assignment_uuid', $assignment)
            ->whereNotIn('status', ['completed', 'invalidated'])->latest()->first();
    }

    public function resume(string $uuid, bool $confirmUncertain = false): Interview
    {
        if (DB::transactionLevel() > 0) {
            throw new \LogicException('Send interview notifications outside business transactions.');
        }
        $request = InterviewSchedulingRequest::findOrFail($uuid);
        $this->validateRequest($request);
        if ($request->interview_uuid) {
            return Interview::findOrFail($request->interview_uuid);
        }
        InterviewNotificationAttempt::where('request_uuid', $request->uuid)->where('status', 'sending')
            ->where('claimed_at', '<=', now()->subSeconds($this->grace()))->update(['status' => 'uncertain', 'outcome' => 'interrupted']);
        foreach ($request->snapshot['channels'] as $channel) {
            $attempt = DB::transaction(function () use ($request, $channel, $confirmUncertain) {
                $this->validateRequest($request);
                InterviewSchedulingRequest::whereKey($request->uuid)->lockForUpdate()->firstOrFail();
                $last = InterviewNotificationAttempt::where('request_uuid', $request->uuid)->where('channel', $channel)->latest('id')->lockForUpdate()->first();
                if (InterviewNotificationAttempt::where('request_uuid', $request->uuid)->where('channel', $channel)->where('status', 'accepted')->exists()) {
                    return null;
                }
                if ($last && $last->status === 'sending') {
                    if ($last->claimed_at->gt(now()->subSeconds($this->grace()))) {
                        $this->fail('A notification is still sending. Do not retry yet.');
                    }
                    $last->update(['status' => 'uncertain', 'outcome' => 'interrupted']);
                }
                if ($last && $last->status === 'uncertain' && ! $confirmUncertain) {
                    $this->fail('Delivery is uncertain. Confirm the duplicate-message risk before retrying.');
                }

                return InterviewNotificationAttempt::create(['request_uuid' => $request->uuid, 'channel' => $channel,
                    'status' => 'sending', 'claim' => (string) Str::uuid(), 'claimed_at' => now()]);
            });
            if ($attempt === null) {
                continue;
            }
            try {
                $snapshot = $request->snapshot;
                $message = $snapshot[$channel.'_message'];
                if ($channel === 'sms') {
                    app(SmsService::class)->send($snapshot['phone'], $message);
                } else {
                    app(NotificationService::class)->sendPortal($snapshot['citizen_uuid'], 'interview', 'Interview Scheduled', $message);
                }
            } catch (\Throwable $exception) {
                $attempt->update(['status' => $exception instanceof ConnectionException ? 'uncertain' : 'failed',
                    'outcome' => $exception instanceof ConnectionException ? 'connection_uncertain' : 'provider_failed']);
                $request->update(['status' => 'failed']);
                $this->fail('The interview is not scheduled. A notification failed or its delivery is uncertain. Saved outcomes are preserved; use Retry. Accepted messages cannot be recalled.');
            }
            $attempt->update(['status' => 'accepted', 'outcome' => 'provider_accepted']);
        }

        return DB::transaction(function () use ($request) {
            $request->refresh();
            $client = $this->client($request->client_uuid, $request->claimant_assignment_uuid);
            $current = InterviewSchedulingRequest::whereKey($request->uuid)->lockForUpdate()->firstOrFail();
            $this->validateRequest($current);
            if ($current->interview_uuid) {
                return Interview::findOrFail($current->interview_uuid);
            }
            $this->assertNoInterview($client->uuid, $current->claimant_assignment_uuid);
            foreach ($current->snapshot['channels'] as $channel) {
                if (! InterviewNotificationAttempt::where('request_uuid', $current->uuid)->where('channel', $channel)->where('status', 'accepted')->exists()) {
                    $this->fail('Required notification acceptance is missing.');
                }
            }
            $interview = Interview::create(['client_uuid' => $client->uuid, 'claimant_assignment_uuid' => $current->claimant_assignment_uuid,
                'schedule' => $current->schedule, 'status' => 'scheduled']);
            if ($client->user) {
                app(NotificationService::class)->recordInternal($client->user, 'Interview Scheduled', 'Your interview is scheduled for '.$current->schedule->format('F j, Y g:i A').'.');
            }
            $current->update(['status' => 'completed', 'interview_uuid' => $interview->uuid]);
            ActivityLoggerService::logSuccess('Scheduled claimant interview', ['interview_uuid' => $interview->uuid, 'request_uuid' => $current->uuid]);

            return $interview;
        });
    }

    private function validateRequest(InterviewSchedulingRequest $request): void
    {
        $request->refresh();
        $client = $this->client($request->client_uuid, $request->claimant_assignment_uuid);
        if ($request->status === 'invalidated' || $request->snapshot !== $this->snapshot($client, $request->schedule->format('Y-m-d H:i:s')) || $request->schedule->isPast()) {
            $this->fail('The scheduling request is stale. Reload and choose a new schedule.');
        }
        $this->configuration($request->snapshot);
    }

    private function client(string $uuid, ?string $assignment): Client
    {
        app(AccountAccess::class)->assertWritable();
        $application = Application::where('client_uuid', $uuid)->lockForUpdate()->first();
        $client = Client::with('user')->whereKey($uuid)->lockForUpdate()->firstOrFail();
        Gate::authorize('create', [Interview::class, $client]);
        if ($application) {
            $application->assertAssignment($assignment);
            abort_unless($application->isActive(), 403);
        } else {
            abort_if($assignment || $client->isUsed(true), 403);
        }

        return $client;
    }

    private function snapshot(Client $client, string $slot): array
    {
        $development = app()->environment('local', 'testing');
        $uuid = $client->user?->citizen_uuid;

        return ['development' => $development, 'user_id' => $client->user_id, 'citizen_uuid' => $uuid,
            'phone' => (string) $client->contact_number,
            'portal_message' => 'You have an interview scheduled for '.Carbon::parse($slot)->format('F j, Y g:i A').' at the CSWDO Office in the Taguig City Hall at Gen. Luna St., Tuktukan, Taguig City. Please arrive earlier than scheduled time.',
            'sms_message' => 'Magandang araw! '.$client->fullname().', Ikaw ay inaanyayahan para sa panayam kaugnay ng inyong aplikasyon sa funeral assistance sa CSWDO ng Pamahalaang Lungsod ng Taguig. Petsa: '.Carbon::parse($slot)->format('F j, Y').', Oras: '.Carbon::parse($slot)->format('g:i A').', Lugar: CSWDO Office, Taguig City Hall, General Luna St., Tuktukan, Taguig City. Dalhin ang mga hard copy ng kinakailangang dokumento at dumating bago sa itinakdang oras. Para sa karagdagang detalye: '.SystemSetting::first()?->department_email.'. Maraming salamat po.',
            'channels' => $development ? [] : ($uuid ? ['portal', 'sms'] : ['sms'])];
    }

    private function configuration(array $snapshot): void
    {
        foreach ($snapshot['channels'] as $channel) {
            $prefix = $channel === 'portal' ? 'services.portal.notification' : 'services.sms';
            if (! config($prefix.'.enable.post') || ! config($prefix.'.endpoint') || ! config($prefix.'.key')) {
                $this->fail('Enable and configure the required '.$channel.' notification service before scheduling.');
            }
        }
        if ($snapshot['channels'] !== [] && trim($snapshot['phone']) === '') {
            $this->fail('A contact number is required before scheduling.');
        }
    }

    private function assertNoInterview(string $clientUuid, ?string $assignment): void
    {
        if (Interview::where('client_uuid', $clientUuid)->where('claimant_assignment_uuid', $assignment)->whereIn('status', ['scheduled', 'done'])->exists()) {
            $this->fail('An interview already exists for this claimant cycle.');
        }
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['schedule' => $message]);
    }
}
