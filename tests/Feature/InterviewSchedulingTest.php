<?php

namespace Tests\Feature;

use App\Livewire\Interview\Create;
use App\Models\Interview;
use App\Models\InterviewNotificationAttempt;
use App\Models\InterviewSchedulingRequest;
use App\Models\Notification;
use App\Models\User;
use App\Services\InterviewSchedulingService;
use App\Services\InterviewService;
use App\Services\NotificationService;
use App\Services\SmsService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\Support\EnvironmentFixtures;
use Tests\TestCase;

class InterviewSchedulingTest extends TestCase
{
    use EnvironmentFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initializeEnvironment();
    }

    protected function tearDown(): void
    {
        DB::purge('environment_testing');
        parent::tearDown();
    }

    #[TestWith(['local'])]
    #[TestWith(['testing'])]
    public function test_development_schedules_and_records_internal_notification_without_external_calls(string $environment): void
    {
        $this->app->instance('env', $environment);
        $citizen = User::factory()->create(['citizen_uuid' => (string) Str::uuid()]);
        $client = $this->client($citizen);
        $this->staff();
        $interview = app(InterviewService::class)->store(['schedule' => now()->addDay()->toDateTimeString()], $client->uuid);
        $this->assertSame('scheduled', $interview->status);
        $this->assertSame(1, Notification::where('notifiable_id', $citizen->id)->count());
        Http::assertNothingSent();
    }

    public function test_linked_production_client_requires_both_enabled_channels(): void
    {
        $client = $this->client(User::factory()->create(['citizen_uuid' => (string) Str::uuid()]));
        $this->staff();
        $this->app->instance('env', 'production');
        config(['services.sms.enable.post' => false]);
        $this->expectException(ValidationException::class);
        try {
            app(InterviewService::class)->store(['schedule' => now()->addDay()->toDateTimeString()], $client->uuid);
        } finally {
            $this->assertSame(0, Interview::count());
            Http::assertNothingSent();
        }
    }

    public function test_walk_in_requires_sms_only(): void
    {
        $client = $this->client(null);
        $this->staff();
        $this->app->instance('env', 'production');
        config(['services.portal.notification.enable.post' => false]);
        Http::fake(['https://sms.test/*' => Http::response([], 200)]);
        app(InterviewService::class)->store(['schedule' => now()->addDay()->toDateTimeString()], $client->uuid);
        Http::assertSentCount(1);
        $this->assertSame(1, Interview::count());
        $this->assertSame(0, Notification::count());
    }

    public function test_partial_failure_retry_sends_only_missing_channel(): void
    {
        $client = $this->client(User::factory()->create(['citizen_uuid' => (string) Str::uuid()]));
        $this->staff();
        $this->app->instance('env', 'production');
        $smsCalls = 0;
        Http::fake(['https://portal.test/*' => Http::response([], 200), 'https://sms.test/*' => function () use (&$smsCalls) {
            return Http::response([], ++$smsCalls === 1 ? 503 : 200);
        }]);
        try {
            app(InterviewService::class)->store(['schedule' => now()->addDay()->toDateTimeString()], $client->uuid);
            $this->fail('Failed notification was ignored.');
        } catch (ValidationException) {
            $this->assertSame(0, Interview::count());
            $this->assertSame(0, Notification::count());
        }
        $request = InterviewSchedulingRequest::firstOrFail();
        app(InterviewSchedulingService::class)->resume($request->uuid);
        Http::assertSentCount(3);
        $this->assertSame(1, Interview::count());
        $this->assertSame(1, Notification::count());
        $this->assertSame(2, InterviewNotificationAttempt::where('status', 'accepted')->count());
    }

    public function test_timeout_requires_confirmation_and_changed_recipient_rejects_retry(): void
    {
        $client = $this->client(null);
        $this->staff();
        $this->app->instance('env', 'production');
        Http::fake(['https://sms.test/*' => Http::failedConnection()]);
        try {
            app(InterviewService::class)->store(['schedule' => now()->addDay()->toDateTimeString()], $client->uuid);
        } catch (ValidationException) {
        }
        $request = InterviewSchedulingRequest::firstOrFail();
        try {
            app(InterviewSchedulingService::class)->resume($request->uuid);
            $this->fail('Uncertain delivery was retried without confirmation.');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString('uncertain', $exception->errors()['schedule'][0]);
        }
        $client->update(['contact_number' => '09999999999']);
        $this->expectException(ValidationException::class);
        app(InterviewSchedulingService::class)->resume($request->uuid, true);
    }

    public function test_active_claim_blocks_another_sender_and_expired_claim_needs_confirmation(): void
    {
        $client = $this->client(null);
        $this->staff();
        $this->app->instance('env', 'production');
        Http::fake(['https://sms.test/*' => Http::failedConnection()]);
        try {
            app(InterviewService::class)->store(['schedule' => now()->addDay()->toDateTimeString()], $client->uuid);
        } catch (ValidationException) {
        }
        $request = InterviewSchedulingRequest::firstOrFail();
        $attempt = InterviewNotificationAttempt::firstOrFail();
        $attempt->update(['status' => 'sending', 'claimed_at' => now()]);
        try {
            app(InterviewSchedulingService::class)->resume($request->uuid, true);
            $this->fail('A concurrent sender was allowed.');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString('still sending', $exception->errors()['schedule'][0]);
        }
        $attempt->update(['claimed_at' => now()->subMinutes(6)]);
        try {
            app(InterviewSchedulingService::class)->resume($request->uuid);
            $this->fail('An expired claim was retried without confirmation.');
        } catch (ValidationException) {
            $this->assertSame('uncertain', $attempt->fresh()->status);
        }
        Http::swap(new Factory);
        Http::preventStrayRequests();
        Http::fake(['https://sms.test/*' => Http::response([], 200)]);
        app(InterviewSchedulingService::class)->resume($request->uuid, true);
        $this->assertSame(1, Interview::count());
    }

    public function test_changed_slot_does_not_reuse_accepted_outcomes(): void
    {
        $client = $this->client(User::factory()->create(['citizen_uuid' => (string) Str::uuid()]));
        $this->staff();
        $this->app->instance('env', 'production');
        Http::fake(['https://portal.test/*' => Http::response([], 200), 'https://sms.test/*' => Http::response([], 503)]);
        try {
            app(InterviewService::class)->store(['schedule' => now()->addDay()->toDateTimeString()], $client->uuid);
        } catch (ValidationException) {
        }
        $old = InterviewSchedulingRequest::firstOrFail();
        Http::swap(new Factory);
        Http::preventStrayRequests();
        Http::fake(['https://portal.test/*' => Http::response([], 200), 'https://sms.test/*' => Http::response([], 200)]);
        app(InterviewService::class)->store(['schedule' => now()->addDays(2)->toDateTimeString()], $client->uuid);
        $this->assertSame('invalidated', $old->fresh()->status);
        $this->assertSame(2, InterviewNotificationAttempt::where('channel', 'portal')->where('status', 'accepted')->count());
        $this->assertSame(1, Interview::count());
    }

    public function test_local_notification_services_never_call_external_providers_even_when_enabled(): void
    {
        $citizen = User::factory()->create(['citizen_uuid' => (string) Str::uuid()]);
        app(NotificationService::class)->send($citizen->citizen_uuid, 'test', 'Subject', 'Body');
        app(SmsService::class)->send('09123456789', 'Body');
        $this->assertSame(1, Notification::count());
        Http::assertNothingSent();
    }

    public function test_livewire_scheduling_has_no_outer_transaction_and_records_one_notification(): void
    {
        $client = $this->client(User::factory()->create(['citizen_uuid' => (string) Str::uuid()]));
        $this->staff();
        Livewire::test(Create::class, ['client' => $client])
            ->set('schedule', now()->addDay()->format('Y-m-d\TH:i'))->call('save')->assertHasNoErrors();
        $this->assertSame(1, Interview::count());
        $this->assertSame(1, Notification::count());
    }

    public function test_failed_finalization_preserves_accepted_channels_for_retry(): void
    {
        $client = $this->client(null);
        $this->staff();
        $this->app->instance('env', 'production');
        Http::fake(['https://sms.test/*' => Http::response([], 200)]);
        $fail = true;
        Interview::creating(function () use (&$fail): void {
            if ($fail) {
                throw new \RuntimeException('Synthetic database finalization failure');
            }
        });
        try {
            app(InterviewService::class)->store(['schedule' => now()->addDay()->toDateTimeString()], $client->uuid);
            $this->fail('Synthetic finalization failure did not run.');
        } catch (\RuntimeException) {
            $this->assertSame(0, Interview::count());
            $this->assertSame(1, InterviewNotificationAttempt::where('status', 'accepted')->count());
        }
        $fail = false;
        app(InterviewSchedulingService::class)->resume(InterviewSchedulingRequest::firstOrFail()->uuid);
        $this->assertSame(1, Interview::count());
        Http::assertSentCount(1);
    }

    public function test_revoked_permission_blocks_retry_without_another_send(): void
    {
        $client = $this->client(null);
        $staff = $this->staff();
        $this->app->instance('env', 'production');
        Http::fake(['https://sms.test/*' => Http::response([], 503)]);
        try {
            app(InterviewService::class)->store(['schedule' => now()->addDay()->toDateTimeString()], $client->uuid);
        } catch (ValidationException) {
        }
        $staff->roles()->detach();
        auth()->setUser($staff->fresh());
        $this->expectException(AuthorizationException::class);
        try {
            app(InterviewSchedulingService::class)->resume(InterviewSchedulingRequest::firstOrFail()->uuid);
        } finally {
            Http::assertSentCount(1);
            $this->assertSame(0, Interview::count());
        }
    }
}
