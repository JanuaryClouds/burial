<?php

namespace App\Services\Mail;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MockEmailLogs
{
    public const EVENTS = ['Send', 'Delivery', 'Bounce', 'Complaint', 'DeliveryDelay', 'Open', 'Click'];

    public function connection(): Connection
    {
        $logs = app(SharedEmailLogs::class);
        if (! $logs->mockEnabled()) {
            throw new SharedEmailLogsUnavailable;
        }

        return $logs->connection();
    }

    public function initialize(): void
    {
        // Resolve the guarded mock connection before creating any directory or file.
        $connection = $this->connection();
        $path = $connection->getConfig('database');
        if ($path !== ':memory:') {
            $directory = storage_path('app/private/email-logs-mock');
            if ($path !== $directory.DIRECTORY_SEPARATOR.'email-logs.sqlite' && $path !== $directory.'/email-logs.sqlite') {
                throw new \LogicException('Mock database must use its dedicated storage directory.');
            }
            if (! is_dir($directory) && ! mkdir($directory, 0700, true) && ! is_dir($directory)) {
                throw new \RuntimeException('Cannot create mock database directory.');
            }
            if (! is_file($path)) {
                $file = fopen($path, 'x');
                if ($file === false) {
                    throw new \RuntimeException('Cannot create mock database file.');
                }
                fclose($file);
            }
        }
        $schema = $connection->getSchemaBuilder();
        if (! $schema->hasTable('email_logs')) {
            $schema->create('email_logs', function (Blueprint $table) {
                $table->id();
                $table->string('event_type');
                $table->string('recipient');
                $table->string('message_id');
                $table->text('subject')->nullable();
                $table->string('caller_identity')->nullable();
                $table->string('source_ip')->nullable();
                $table->string('site_tag')->nullable();
                $table->string('bounce_type')->nullable();
                $table->string('bounce_subtype')->nullable();
                $table->text('diagnostic')->nullable();
                $table->timestamp('occurred_at');
                $table->timestamps();
                $table->index(['recipient', 'event_type', 'bounce_type']);
                $table->index('message_id');
            });
        }
        $fixtures = [
            ['delivered', ['Send', 'Delivery', 'Open', 'Click', 'Open', 'Click']],
            ['permanent', ['Send', 'Bounce']],
            ['complaint', ['Send', 'Delivery', 'Complaint']],
            ['mailboxfull', ['Send', 'DeliveryDelay', 'Bounce']],
            ['delayed', ['Send', 'DeliveryDelay']],
            ['other-site', ['Send', 'Bounce']],
        ];
        $connection->transaction(function () use ($connection, $fixtures) {
            foreach ($fixtures as [$scenario, $events]) {
                $id = 'mock-fixture-'.$scenario;
                if ($connection->table('email_logs')->where('message_id', $id)->exists()) {
                    continue;
                }
                foreach ($events as $event) {
                    $bounce = $event === 'Bounce';
                    $this->append($scenario.'@example.test', $event, $id,
                        $bounce ? ($scenario === 'mailboxfull' ? 'Transient' : 'Permanent') : null,
                        $bounce ? ($scenario === 'mailboxfull' ? 'MailboxFull' : 'NoEmail') : null,
                        site: $scenario === 'other-site' ? 'another-system' : 'burial');
                }
            }
        });
    }

    public function append(string $recipient, string $event, string $messageId, ?string $bounceType = null,
        ?string $bounceSubtype = null, ?string $diagnostic = null, string $subject = 'Synthetic local email', string $site = 'burial'): void
    {
        $connection = $this->connection();
        $recipient = app(SharedEmailLogs::class)->normalize($recipient);
        Validator::make(compact('event', 'messageId', 'bounceType', 'bounceSubtype', 'diagnostic', 'subject', 'site'), [
            'event' => ['required', Rule::in(self::EVENTS)],
            'messageId' => ['required', 'string', 'max:255'],
            'site' => ['required', 'regex:/^[A-Za-z0-9_-]+$/D', 'max:255'],
            'bounceType' => $event === 'Bounce' ? ['required', Rule::in(['Permanent', 'Transient', 'Undetermined'])] : ['prohibited'],
            'bounceSubtype' => $event === 'Bounce' ? ['required', 'string', 'max:255'] : ['prohibited'],
            'diagnostic' => $event === 'Bounce' ? ['nullable', 'string', 'max:2000'] : ['prohibited'],
            'subject' => ['string', 'max:2000'],
        ])->validate();
        $connection->table('email_logs')->insert(['event_type' => $event, 'recipient' => $recipient, 'message_id' => $messageId,
            'subject' => $subject, 'caller_identity' => 'mock-mailpit', 'source_ip' => '127.0.0.1', 'site_tag' => $site,
            'bounce_type' => $bounceType, 'bounce_subtype' => $bounceSubtype, 'diagnostic' => $diagnostic,
            'occurred_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
    }
}
