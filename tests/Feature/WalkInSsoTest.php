<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use App\Services\CentralClientService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class WalkInSsoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'walk_in_sso_testing',
            'database.connections.walk_in_sso_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'app.key' => 'base64:'.base64_encode(str_repeat('s', 32)), 'cache.default' => 'array', 'session.driver' => 'array',
            'services.portal.users.enable.get' => true, 'services.portal.users.endpoint' => 'https://portal.test/users',
            'services.portal.users.key' => 'portal-test-key', 'services.portal.sso.secret' => 'sso-test-key', 'services.portal.users.mock' => false]);
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Artisan::call('migrate', ['--database' => 'walk_in_sso_testing', '--force' => true]);
        SystemSetting::create(['uuid' => (string) Str::uuid(), 'walk_in_intake_enabled' => true]);
        Http::preventStrayRequests();
    }

    protected function tearDown(): void
    {
        DB::purge('walk_in_sso_testing');
        parent::tearDown();
    }

    private function signedUrl(string $uuid): string
    {
        $payload = rtrim(strtr(base64_encode(json_encode(['citizen_uuid' => $uuid, 'nonce' => 'test-nonce'])), '+/', '-_'), '=');

        return '/sso/callback?'.http_build_query(['payload' => $payload, 'signature' => hash_hmac('sha256', $payload, 'sso-test-key')]);
    }

    private function record(string $uuid, string $email = 'citizen@portal.test'): array
    {
        return ['user_id' => $uuid, 'email' => $email, 'firstname' => 'Portal', 'middlename' => null,
            'lastname' => 'Citizen', 'suffix' => null, 'contact_number' => '09123456789'];
    }

    public function test_signed_sso_creates_citizen_and_establishes_claim_session_for_subsequent_logins(): void
    {
        $uuid = (string) Str::uuid();
        Http::fake(['https://portal.test/*' => Http::response(['data' => [$this->record($uuid)]])]);
        $this->get($this->signedUrl($uuid))->assertRedirect(route('client.create'))->assertSessionHas('citizen.sso_uuid', $uuid);
        $citizen = User::where('citizen_uuid', $uuid)->firstOrFail();
        $this->assertAuthenticatedAs($citizen);
        $this->get(route('walk-in.claim'))->assertOk()->assertSee('citizen@portal.test');
        Auth::logout();
        session()->forget('citizen.sso_uuid');
        $this->get($this->signedUrl($uuid))->assertRedirect()->assertSessionHas('citizen.sso_uuid', $uuid);
        $this->assertSame(1, User::count());
        Http::assertSentCount(2);
    }

    public function test_invalid_signature_does_not_fetch_portal_or_establish_claim_session(): void
    {
        Http::fake();
        $this->get($this->signedUrl((string) Str::uuid()).'bad')->assertRedirect();
        $this->assertGuest();
        $this->assertNull(session('citizen.sso_uuid'));
        Http::assertNothingSent();
    }

    public function test_portal_uuid_or_immutable_email_mismatch_never_changes_local_identity(): void
    {
        $uuid = (string) Str::uuid();
        $user = User::factory()->create(['citizen_uuid' => $uuid, 'email' => 'original@portal.test', 'first_name' => 'Original']);
        foreach ([$this->record((string) Str::uuid(), $user->email), $this->record($uuid, 'different@portal.test')] as $record) {
            Http::fake(['https://portal.test/*' => Http::response(['data' => [$record]])]);
            $this->assertNull(app(CentralClientService::class)->checkIfUser('uuid', $uuid));
            $this->assertSame('Original', $user->fresh()->first_name);
            $this->assertSame('original@portal.test', $user->fresh()->email);
        }
    }

    public function test_empty_portal_response_does_not_reuse_existing_local_account(): void
    {
        $uuid = (string) Str::uuid();
        User::factory()->create(['citizen_uuid' => $uuid, 'email' => 'citizen@portal.test']);
        Http::fake(['https://portal.test/*' => Http::response(['data' => []])]);
        $this->getJson($this->signedUrl($uuid))->assertStatus(500);
        $this->assertGuest();
        $this->assertNull(session('citizen.sso_uuid'));
    }

    public function test_incomplete_portal_profile_is_not_authenticated(): void
    {
        $uuid = (string) Str::uuid();
        Http::fake(['https://portal.test/*' => Http::response(['data' => [['user_id' => $uuid, 'email' => 'citizen@portal.test']]])]);
        $this->getJson($this->signedUrl($uuid))->assertStatus(500);
        $this->assertGuest();
        $this->assertSame(0, User::count());
    }
}
