<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Spatie\SuspiciousLogin\Fingerprinters\DeviceFingerprinter;
use Spatie\SuspiciousLogin\Models\LoginAttempt;
use Spatie\SuspiciousLogin\SuspiciousLogin;
use Throwable;

class AuthenticationAudit
{
    public function internal(?User $account): bool
    {
        return $account && blank($account->citizen_uuid) && $account->isInternal();
    }

    /** @param array<string, scalar|null> $context */
    public function record(?User $account, string $event, string $outcome = 'success', ?User $actor = null, ?bool $credential = null, array $context = [], string $logName = 'authentication'): void
    {
        try {
            if (! config('suspicious-login.monitoring_enabled', true) || ! $this->internal($account)) {
                return;
            }
            if (DB::transactionLevel() > 0) {
                $callback = fn () => $this->record($account, $event, $outcome, $actor, $credential, $context, $logName);
                DB::afterCommit($callback);
                if ($outcome !== 'success') {
                    DB::afterRollBack($callback);
                }

                return;
            }
            $assessed = $this->internal($actor) && $credential === null ? $actor : $account;
            $metadata = $this->assessment($assessed, $event, $outcome, $credential);
            $metadata = array_merge($metadata, ['affected_account_id' => $account->id, 'affected_account_name' => $account->fullname(),
                'recipient_id' => $account->id, 'actor_id' => $actor?->id, 'actor_name' => $actor?->fullname()]);
            $properties = ['security' => $metadata,
                'context' => array_intersect_key($context, array_flip(['method', 'reason', 'role_id', 'target_id']))];
            $log = activity($logName)->performedOn($account)->withProperties($properties);
            $actor ? $log->causedBy($actor) : $log->causedByAnonymous();
            $log->log($event);
        } catch (Throwable) {
            $this->diagnostic();
        }
    }

    public function recordForEmail(string $email, string $event, string $outcome): void
    {
        try {
            $account = User::with('roles')->whereRaw('LOWER(email) = ?', [mb_strtolower(trim($email))])->first();
            $this->record($account, $event, $outcome);
        } catch (Throwable) {
            $this->diagnostic();
        }
    }

    /** Enrich existing security records without replacing their identity or actor. */
    public function enrich(Activity $activity): void
    {
        try {
            if (! config('suspicious-login.monitoring_enabled', true) || ! AuthenticationLog::isSecurity($activity) || $activity->properties->has('security')) {
                return;
            }
            $actor = $activity->causer instanceof User ? $activity->causer : null;
            $target = $activity->subject instanceof User ? $activity->subject : null;
            $targetId = $activity->properties->get('target_id');
            if ($targetId) {
                $target = User::with('roles')->find($targetId);
            }
            $account = $this->internal($actor) ? $actor : $target;
            if (! $this->internal($account)) {
                return;
            }
            $outcome = str_contains(strtolower($activity->description), 'fail') || $activity->log_name === 'permissions' ? 'failure' : 'success';
            $metadata = $this->assessment($account, $activity->description, $outcome);
            $metadata['recipient_id'] = $this->internal($target) ? $target->id : $account->id;
            $metadata['affected_account_id'] = $target->id ?? $account->id;
            $metadata['affected_account_name'] = $target?->fullname() ?? $account->fullname();
            $metadata['actor_id'] = $actor?->id;
            $metadata['actor_name'] = $actor?->fullname();
            $activity->properties = $activity->properties->only(['method', 'target_id', 'roles', 'previous_roles', 'previous_expiry', 'expires_at', 'exception_type', 'id'])
                ->put('security', $metadata);
        } catch (Throwable) {
            $activity->properties = $activity->properties->put('security', ['assessment' => 'unavailable']);
            $this->diagnostic();
        }
    }

    /** @return array<string, mixed> */
    private function assessment(User $account, string $event, string $outcome, ?bool $credential = null): array
    {
        $metadata = ['event' => $event, 'outcome' => $outcome, 'staff_account_id' => $account->id,
            'affected_account_id' => $account->id, 'affected_account_name' => $account->fullname(),
            'recipient_id' => $account->id, 'assessment' => 'unavailable', 'risk_score' => null, 'risk_level' => null,
            'reasons' => [], 'ip_address' => request()->ip(), 'user_agent' => mb_substr(request()->userAgent() ?? '', 0, 512),
            'route' => request()->route()?->getName(), 'http_method' => request()->method()];
        try {
            $attempt = new LoginAttempt(['guard' => 'web', 'successful' => $credential === true, 'blocked' => false,
                'identifier' => mb_strtolower($account->email), 'ip_address' => $metadata['ip_address'],
                'user_agent' => $metadata['user_agent'], 'device_fingerprint' => app(DeviceFingerprinter::class)->fingerprint(request()),
                'created_at' => now(), 'properties' => ['event' => $event]]);
            $attempt->authenticatable()->associate($account);
            app(SuspiciousLogin::class)->assess($attempt, new AuthenticationMonitorPreset);
            if ($credential !== null) {
                $attempt->save();
                $metadata['login_attempt_id'] = $attempt->id;
            }
            $metadata['assessment'] = 'scored';
            $metadata['risk_score'] = $attempt->risk_score;
            $metadata['risk_level'] = $attempt->risk_level->value;
            $metadata['reasons'] = $attempt->reasons;
        } catch (Throwable) {
            $this->diagnostic();
        }

        return $metadata;
    }

    public function notify(Activity $activity): void
    {
        try {
            if (! config('suspicious-login.monitoring_enabled', true)) {
                return;
            }
            $security = $activity->properties->get('security', []);
            if (! in_array($security['risk_level'] ?? '', ['medium', 'high'], true)) {
                return;
            }
            if (DB::transactionLevel() > 0) {
                DB::afterCommit(fn () => $this->notify($activity));

                return;
            }
            $recipient = User::find($security['recipient_id']);
            if (! $recipient || filled($recipient->citizen_uuid)) {
                return;
            }
            $checks = array_column($security['reasons'], 'check');
            sort($checks);
            $key = 'authentication-alert.'.hash('sha256', $recipient->id.'|'.implode('|', $checks));
            Cache::lock($key.'.lock', 10)->block(2, function () use ($key, $recipient, $security, $activity) {
                $previous = Cache::get($key);
                if ($previous === 'high' || $previous === ($security['risk_level'] ?? null)) {
                    return;
                }
                $reasons = implode('; ', array_column($security['reasons'], 'reason'));
                app(NotificationService::class)->recordInternal($recipient, 'Suspicious account activity',
                    $activity->description.' at '.$activity->created_at->format('Y-m-d H:i:s').'. Risk: '.$security['risk_level'].' ('.$security['risk_score'].'/100). '.$reasons);
                Cache::put($key, $security['risk_level'], (int) config('suspicious-login.notification_window_seconds', 900));
            });
        } catch (Throwable) {
            $this->diagnostic();
        }
    }

    private function diagnostic(): void
    {
        try {
            logger()->warning('Authentication monitoring could not complete. Check monitoring database and cache configuration.');
        } catch (Throwable) {
        }
    }
}
