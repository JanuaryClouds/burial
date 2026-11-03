<?php

namespace App\Services\Mail;

use App\Models\MailDelivery;
use App\Models\MailDeliveryAttempt;
use App\Models\User;
use App\Services\Auth\AccountAccess;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ImmediateMailService
{
    /** Registered message handlers reconstruct mail from encrypted, typed data. */
    private const HANDLERS = ['password_reset' => PasswordResetMessage::class];

    public function record(string $recipient, MessagePayload $payload): MailDelivery
    {
        if (! isset(self::HANDLERS[$payload->type()]) || ! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid mail message.');
        }

        return MailDelivery::create(['type' => $payload->type(), 'recipient' => $recipient, 'payload' => $payload->toArray()]);
    }

    public function send(string $recipient, MessagePayload $payload): MailDelivery
    {
        if (DB::transactionLevel() > 0) {
            throw new \LogicException('Mail must be sent outside database transactions.');
        }

        return $this->deliver($this->record($recipient, $payload));
    }

    public function retry(MailDelivery $delivery, User $actor): MailDelivery
    {
        app(AccountAccess::class)->assertWritable();
        abort_unless(auth()->id() === $actor->id && $actor->fresh(['roles'])?->isSystemAdministrator(), 403);

        return $this->deliver($delivery, $actor->id, true);
    }

    public function interruptedBefore(): Carbon
    {
        // A conservative grace period, longer than the configured SMTP timeout.
        return now()->subSeconds(max(300, (int) config('mail.mailers.smtp.timeout', 15) * 10));
    }

    public function deliver(MailDelivery $delivery, ?int $actorId = null, bool $retry = false): MailDelivery
    {
        if (DB::transactionLevel() > 0) {
            throw new \LogicException('Mail must be sent outside database transactions.');
        }
        $claim = (string) Str::uuid();
        DB::transaction(function () use ($delivery, $claim, $actorId, $retry) {
            $row = MailDelivery::whereKey($delivery->id)->lockForUpdate()->firstOrFail();
            $stale = $row->status === 'sending' && $row->claimed_at?->lt($this->interruptedBefore());
            $abandoned = $row->status === 'pending' && $row->created_at->lt($this->interruptedBefore());
            abort_unless($retry
                ? (in_array($row->status, ['failed', 'uncertain'], true) || $stale || $abandoned)
                : $row->status === 'pending', 409, 'This email is already being sent or cannot be retried.');
            abort_if($row->failure_code === 'ineligible', 409, 'This recovery request is no longer eligible.');
            if ($stale) {
                $row->attempts()->where('claim', $row->claim)->where('status', 'sending')
                    ->update(['status' => 'uncertain', 'failure_code' => 'interrupted', 'finished_at' => now()]);
            }
            $row->update(['status' => 'sending', 'claim' => $claim, 'claimed_at' => now(), 'failure_code' => null,
                'attempt_count' => $row->attempt_count + 1]);
            $row->attempts()->create(['claim' => $claim, 'actor_id' => $actorId, 'status' => 'sending', 'started_at' => now()]);
        });
        $delivery->refresh();
        try {
            $mailer = config('mail.default');
            if (app()->isProduction() && config("mail.mailers.$mailer.transport") !== 'smtp') {
                throw new \LogicException('SMTP is required in production.');
            }
            $handler = self::HANDLERS[$delivery->type] ?? throw new \LogicException('Unknown message type.');
            $message = app($handler)->prepare($delivery);
            if (Mail::mailer($mailer)->to($delivery->recipient)->sendNow($message) === null) {
                throw new \RuntimeException('Mail was not accepted.');
            }
            $status = 'sent';
            $code = null;
        } catch (\Throwable $exception) {
            // Deliberately never report raw mail exceptions: transports can include secrets or message content.
            $status = 'failed';
            $code = match (true) {
                $exception instanceof IneligibleMessage => 'ineligible',
                $exception instanceof \LogicException => 'configuration',
                default => 'transport',
            };
        }
        // A failure here leaves a durable sending claim, surfaced as uncertain after the grace period.
        DB::transaction(function () use ($delivery, $claim, $status, $code) {
            $updated = MailDelivery::whereKey($delivery->id)->where('claim', $claim)->where('status', 'sending')
                ->update(['status' => $status, 'failure_code' => $code, 'sent_at' => $status === 'sent' ? now() : null, 'updated_at' => now()]);
            if ($updated) {
                MailDeliveryAttempt::where('claim', $claim)->update(['status' => $status, 'failure_code' => $code, 'finished_at' => now()]);
                if ($status === 'sent' || $code === 'ineligible') {
                    MailDelivery::whereKey($delivery->id)->where('claim', $claim)->update(['payload' => null]);
                }
            }
        });

        return $delivery->refresh();
    }
}
