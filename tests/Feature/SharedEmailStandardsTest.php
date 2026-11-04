<?php

namespace Tests\Feature;

use App\Services\Mail\InvalidMailRecipient;
use App\Services\Mail\MockEmailLogs;
use App\Services\Mail\OutgoingMailPolicy;
use App\Services\Mail\SharedEmailLogs;
use App\Services\Mail\SharedEmailLogsUnavailable;
use App\Services\Mail\SuppressedRecipient;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as Notifications;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class SharedEmailStandardsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['email_logs.mock' => true, 'database.connections.email_logs_mock.database' => ':memory:',
            'mail.default' => 'array', 'mail.from.address' => 'burial@example.test']);
        app(MockEmailLogs::class)->initialize();
    }

    protected function tearDown(): void
    {
        DB::purge('email_logs_mock');
        parent::tearDown();
    }

    private function sendRaw(string $recipient = 'allowed@example.test', ?string $cc = null, ?string $bcc = null): void
    {
        Mail::raw('Synthetic content', function ($message) use ($recipient, $cc, $bcc) {
            $message->to($recipient)->subject('Synthetic shared log test');
            if ($cc) {
                $message->cc($cc);
            }
            if ($bcc) {
                $message->bcc($bcc);
            }
        });
    }

    public function test_raw_mail_is_tagged_normalized_and_logged_once_per_recipient(): void
    {
        $this->sendRaw('USER@Example.Test', 'cc@example.test', 'CC@EXAMPLE.TEST');
        $messages = Mail::mailer()->getSymfonyTransport()->messages();
        $this->assertCount(1, $messages);
        $message = $messages->first()->getOriginalMessage();
        $this->assertSame('site=burial', $message->getHeaders()->get('X-SES-MESSAGE-TAGS')->getBodyAsString());
        $this->assertSame('user@example.test', $message->getTo()[0]->getAddress());
        $rows = DB::connection('email_logs_mock')->table('email_logs')->where('subject', 'Synthetic shared log test')->get();
        $this->assertCount(2, $rows);
        $this->assertSame(['cc@example.test', 'user@example.test'], $rows->pluck('recipient')->sort()->values()->all());
        $this->assertSame(['Send'], $rows->pluck('event_type')->unique()->all());
        $this->assertSame(['burial'], $rows->pluck('site_tag')->unique()->all());
        $this->assertCount(1, $rows->pluck('message_id')->unique());
    }

    public function test_mailables_and_notifications_share_the_global_policy(): void
    {
        Mail::to('mailable@example.test')->sendNow(new class extends Mailable
        {
            public function build(): static
            {
                return $this->subject('Mailable')->html('<p>Synthetic</p>');
            }
        });
        Notifications::route('mail', 'notification@example.test')->notify(new class extends Notification
        {
            public function via($notifiable): array
            {
                return ['mail'];
            }

            public function toMail($notifiable): MailMessage
            {
                return (new MailMessage)->subject('Notification')->line('Synthetic');
            }
        });
        $messages = Mail::mailer()->getSymfonyTransport()->messages();
        $this->assertCount(2, $messages);
        foreach ($messages as $sent) {
            $this->assertSame('site=burial', $sent->getOriginalMessage()->getHeaders()->get('X-SES-MESSAGE-TAGS')->getBodyAsString());
        }
    }

    public function test_existing_tags_preserve_other_values_and_replace_conflicting_sites(): void
    {
        $message = (new Email)->to('allowed@example.test');
        $message->getHeaders()->addTextHeader('X-SES-MESSAGE-TAGS', 'campaign=local_test, site=wrong.domain');
        $policy = app(OutgoingMailPolicy::class);
        $policy->sending(new MessageSending($message));
        $policy->sending(new MessageSending($message));
        $this->assertCount(1, iterator_to_array($message->getHeaders()->all('X-SES-MESSAGE-TAGS')));
        $this->assertSame('campaign=local_test, site=burial', $message->getHeaders()->get('X-SES-MESSAGE-TAGS')->getBodyAsString());
    }

    public function test_malformed_other_tags_are_rejected_before_transport(): void
    {
        $message = (new Email)->to('allowed@example.test');
        $message->getHeaders()->addTextHeader('X-SES-MESSAGE-TAGS', 'campaign=bad value');
        $this->expectException(\LogicException::class);
        app(OutgoingMailPolicy::class)->sending(new MessageSending($message));
    }

    public function test_suppression_applies_to_all_recipients_and_other_sites_without_partial_sends(): void
    {
        foreach ([['permanent@example.test', null, null], ['allowed@example.test', 'complaint@example.test', null],
            ['allowed@example.test', null, 'other-site@example.test']] as [$to, $cc, $bcc]) {
            try {
                $this->sendRaw($to, $cc, $bcc);
                $this->fail('Suppressed message was sent.');
            } catch (SuppressedRecipient) {
                $this->assertCount(0, Mail::mailer()->getSymfonyTransport()->messages());
            }
        }
    }

    public function test_informational_events_do_not_suppress_and_new_complaints_are_not_cached(): void
    {
        foreach (['mailboxfull', 'delayed', 'delivered'] as $scenario) {
            $this->sendRaw($scenario.'@example.test');
        }
        $this->assertCount(3, Mail::mailer()->getSymfonyTransport()->messages());
        app(MockEmailLogs::class)->append(' DELIVERED@EXAMPLE.TEST ', 'Complaint', 'mock-later-complaint');
        $this->expectException(SuppressedRecipient::class);
        $this->sendRaw('delivered@example.test');
    }

    public function test_invalid_addresses_are_rejected_by_the_shared_reader(): void
    {
        $this->expectException(InvalidMailRecipient::class);
        app(SharedEmailLogs::class)->assertAllowed(['not an email']);
    }

    public function test_missing_table_stops_sending_with_a_safe_error(): void
    {
        DB::connection('email_logs_mock')->getSchemaBuilder()->drop('email_logs');
        try {
            $this->sendRaw();
            $this->fail('Missing suppression table must block sending.');
        } catch (SharedEmailLogsUnavailable $error) {
            $this->assertSame('Email suppression checks are unavailable. Sending was stopped.', $error->getMessage());
            $this->assertNull($error->getPrevious());
            $this->assertCount(0, Mail::mailer()->getSymfonyTransport()->messages());
        }
    }

    public function test_unconfigured_production_connection_never_uses_the_main_database(): void
    {
        $default = config('database.default');
        config(['email_logs.mock' => false, 'database.connections.email_logs_db.host' => null]);
        try {
            $this->sendRaw();
            $this->fail('Unconfigured shared connection must block sending.');
        } catch (SharedEmailLogsUnavailable) {
            $this->assertSame($default, config('database.default'));
            $this->assertCount(0, Mail::mailer()->getSymfonyTransport()->messages());
        }
    }

    public function test_unavailable_mock_file_stops_sending_without_exposing_connection_details(): void
    {
        DB::purge('email_logs_mock');
        config(['database.connections.email_logs_mock.database' => storage_path('app/private/email-logs-mock/missing-'.bin2hex(random_bytes(8)).'.sqlite')]);
        try {
            $this->sendRaw();
            $this->fail('An unavailable database must block sending.');
        } catch (SharedEmailLogsUnavailable $error) {
            $this->assertSame('Email suppression checks are unavailable. Sending was stopped.', $error->getMessage());
            $this->assertNull($error->getPrevious());
            $this->assertCount(0, Mail::mailer()->getSymfonyTransport()->messages());
        }
    }

    public function test_mock_commands_are_idempotent_validate_events_and_preserve_existing_rows(): void
    {
        $count = DB::connection('email_logs_mock')->table('email_logs')->count();
        $this->artisan('email-logs:mock-init')->assertSuccessful();
        $this->artisan('email-logs:mock-init')->assertSuccessful();
        $this->assertSame($count, DB::connection('email_logs_mock')->table('email_logs')->count());
        $this->artisan('email-logs:mock-event', ['recipient' => ' LOCAL@EXAMPLE.TEST ', 'event' => 'Bounce',
            'message-id' => 'mock-manual', '--bounce-type' => 'Transient', '--bounce-subtype' => 'MailboxFull'])->assertSuccessful();
        $this->assertSame('local@example.test', DB::connection('email_logs_mock')->table('email_logs')->where('message_id', 'mock-manual')->value('recipient'));
        foreach ([['event' => 'Unknown'], ['event' => 'Bounce'], ['event' => 'Send', '--bounce-type' => 'Permanent'],
            ['event' => 'Complaint', '--site' => 'bad.domain'], ['event' => 'Send', 'recipient' => 'invalid']] as $invalid) {
            $this->artisan('email-logs:mock-event', array_merge(['recipient' => 'local@example.test', 'message-id' => 'invalid'], $invalid))->assertFailed();
        }
        $this->assertSame($count + 1, DB::connection('email_logs_mock')->table('email_logs')->count());
    }

    public function test_mock_writes_and_reads_are_rejected_in_production_or_when_disabled(): void
    {
        $environment = app()->environment();
        $this->app->instance('env', 'production');
        try {
            $this->artisan('email-logs:mock-init')->assertFailed();
            $this->artisan('email-logs:mock-event', ['recipient' => 'local@example.test', 'event' => 'Send', 'message-id' => 'invalid'])->assertFailed();
            try {
                $this->sendRaw();
                $this->fail('Production must not use mock suppression.');
            } catch (SharedEmailLogsUnavailable) {
                $this->assertCount(0, Mail::mailer()->getSymfonyTransport()->messages());
            }
        } finally {
            $this->app->instance('env', $environment);
        }
        config(['email_logs.mock' => false]);
        $this->artisan('email-logs:mock-init')->assertFailed();
        $this->artisan('email-logs:mock-event', ['recipient' => 'local@example.test', 'event' => 'Send', 'message-id' => 'invalid'])->assertFailed();
    }

    public function test_post_acceptance_mock_failure_does_not_report_a_delivery_failure(): void
    {
        $writer = \Mockery::mock(MockEmailLogs::class);
        $writer->shouldReceive('append')->once()->andThrow(new \RuntimeException('SECRET DATABASE DETAILS'));
        $this->app->instance(MockEmailLogs::class, $writer);
        Log::shouldReceive('warning')->once()->with('Mock email log write failed after SMTP acceptance. Do not retry the delivered message.');
        $this->sendRaw();
        $this->assertCount(1, Mail::mailer()->getSymfonyTransport()->messages());
    }

    public function test_failed_warning_after_mock_write_failure_does_not_change_acceptance(): void
    {
        $writer = \Mockery::mock(MockEmailLogs::class);
        $writer->shouldReceive('append')->once()->andThrow(new \RuntimeException('Mock database outage'));
        $this->app->instance(MockEmailLogs::class, $writer);
        Log::shouldReceive('warning')->once()->andThrow(new \RuntimeException('Log storage outage'));
        $this->sendRaw();
        $this->assertCount(1, Mail::mailer()->getSymfonyTransport()->messages());
    }
}
