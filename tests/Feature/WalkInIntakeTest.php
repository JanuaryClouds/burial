<?php

namespace Tests\Feature;

use App\Livewire\WalkIn\Intake;
use App\Models\Application;
use App\Models\ApplicationDocumentFile;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\DocumentRequirement;
use App\Models\FuneralAssistanceType;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WalkInIntake;
use App\Services\AssessmentService;
use App\Services\ClaimantReplacementService;
use App\Services\DocumentPlaceholderRegistry;
use App\Services\InterviewService;
use App\Services\PsaClassificationService;
use App\Services\RecommendationService;
use App\Services\WalkInIntakeService;
use App\Services\WorkflowHistoryService;
use App\Services\WorkflowVersionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\TestWith;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class WalkInIntakeTest extends TestCase
{
    private bool $mockOnline = false;

    private ?string $mysqlDatabase = null;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'walk_in_testing',
            'database.connections.walk_in_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'app.key' => 'base64:'.base64_encode(str_repeat('w', 32)), 'cache.default' => 'array', 'session.driver' => 'array',
            'services.portal.users.mock' => false, 'services.fileserver.mock' => false,
            'services.fileserver.enable.post' => true, 'services.fileserver.post.endpoint' => 'https://files.test/api',
            'services.fileserver.post.key' => 'test-secret', 'services.fileserver.enc_key' => 'base64:'.base64_encode(str_repeat('f', 32)),
            'services.fileserver.mock_port' => 18991, 'services.fileserver.mock_token' => 'mock-test-secret']);
        if (getenv('WALK_IN_TEST_MYSQL') === '1') {
            $connection = config('database.connections.mysql');
            if (! in_array($connection['host'], ['localhost', '127.0.0.1', '::1'], true)) {
                throw new \RuntimeException('Walk-in MySQL checks require a local disposable database.');
            }
            $this->mysqlDatabase = 'burial_walk_in_test_'.bin2hex(random_bytes(6));
            config(['database.connections.walk_in_test_server' => [...$connection, 'database' => null, 'url' => null]]);
            DB::connection('walk_in_test_server')->statement('CREATE DATABASE `'.$this->mysqlDatabase.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            config(['database.connections.walk_in_testing' => [...$connection, 'database' => $this->mysqlDatabase, 'url' => null]]);
        }
        config(['services.sms.enable.post' => true, 'services.sms.endpoint' => 'https://sms.test/send', 'services.sms.key' => 'test',
            'services.portal.notification.enable.post' => true, 'services.portal.notification.endpoint' => 'https://portal-notification.test/send', 'services.portal.notification.key' => 'test']);
        Http::preventStrayRequests();
        Http::fake(['https://sms.test/*' => Http::response([], 200), 'https://portal-notification.test/*' => Http::response([], 200)]);
        Http::fake(['http://127.0.0.1:18991/health' => fn () => $this->mockOnline
            ? Http::response(['service' => 'burial-fileserver-mock']) : Http::response([], 404)]);
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Artisan::call('migrate', ['--database' => 'walk_in_testing', '--force' => true]);
        foreach (['sexes', 'religions', 'nationalities', 'civil_statuses', 'educations', 'relationships'] as $table) {
            DB::table($table)->insert(['name' => $table]);
        }
        SystemSetting::create(['uuid' => (string) Str::uuid(), 'claimant_change_limit' => 1, 'walk_in_intake_enabled' => true]);
        $this->mock(PsaClassificationService::class, function ($mock) {
            $mock->shouldReceive('getRegionOptions')->andReturn(['13:0:0:0' => 'NCR']);
            $mock->shouldReceive('getProvinceOptions')->andReturn(['13:1:0:0' => 'Taguig']);
            $mock->shouldReceive('getMunicipalityOptions')->andReturn([]);
            $mock->shouldReceive('getBarangayOptions')->andReturn(['13:1:0:1' => 'Bagumbayan']);
            $mock->shouldReceive('parseKey')->andReturnUsing(fn ($key) => array_combine(['reg', 'prv', 'mun', 'bgy'], array_map(fn ($part) => $part === '0' ? null : $part, explode(':', $key))));
        });
    }

    protected function tearDown(): void
    {
        DB::purge('walk_in_testing');
        if ($this->mysqlDatabase && preg_match('/^burial_walk_in_test_[a-f0-9]{12}$/D', $this->mysqlDatabase)) {
            DB::connection('walk_in_test_server')->statement('DROP DATABASE `'.$this->mysqlDatabase.'`');
            DB::purge('walk_in_test_server');
        }
        parent::tearDown();
    }

    private function staff(string $role = 'CSWDO', bool $permission = true): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate($role, 'web'));
        if ($permission) {
            $user->givePermissionTo(Permission::findOrCreate('Create Walk-in Intake', 'web'));
        }
        $this->actingAs($user);

        return $user;
    }

    private function citizen(bool $active = true): User
    {
        $user = User::factory()->create(['citizen_uuid' => (string) Str::uuid(), 'is_active' => $active]);
        $this->actingAs($user);
        session(['citizen.sso_uuid' => $user->citizen_uuid]);

        return $user;
    }

    private function payload(): array
    {
        $address = ['regionCode' => '13:0:0:0', 'provinceCode' => '13:1:0:0', 'municipalityCode' => null,
            'barangayCode' => '13:1:0:1', 'street' => 'Main Street', 'houseNumber' => '10'];

        return ['identity' => ['first_name' => 'Walk', 'middle_name' => null, 'last_name' => 'Applicant', 'suffix' => null],
            'client' => ['dateOfBirth' => '1980-01-01', 'contactNumber' => '09123456789'],
            'demographic' => ['sexId' => 1, 'religionId' => 1, 'nationalityId' => 1],
            'social' => ['civilId' => 1, 'educationId' => 1], 'address' => $address, 'beneficiaryAddress' => $address,
            'beneficiary' => ['first_name' => 'Deceased', 'last_name' => 'Person', 'date_of_birth' => '1960-01-01',
                'date_of_death' => '2026-01-01', 'sex_id' => 1, 'religion_id' => 1, 'pwd' => false], 'relationshipId' => 1];
    }

    private function submitted(): array
    {
        $intake = app(WalkInIntakeService::class)->saveDraft(null, $this->payload(), null);

        return app(WalkInIntakeService::class)->submit($intake->uuid, $intake->version, [], true);
    }

    public function test_incomplete_draft_is_encrypted_and_resumable_without_a_citizen_account(): void
    {
        $encoder = $this->staff();
        Livewire::test(Intake::class)->set('data.identity.first_name', 'Private Applicant')->call('save')->assertHasNoErrors();
        $intake = WalkInIntake::firstOrFail();
        $this->assertSame($encoder->id, $intake->encoded_by);
        $this->assertSame(1, User::count());
        $this->assertSame(0, Client::count());
        $this->assertStringNotContainsString('Private Applicant', DB::table('walk_in_intakes')->value('data'));
        $this->get(route('walk-in.edit', $intake))->assertOk()->assertSee('Private Applicant');
        $this->get(route('walk-in.index'))->assertOk()->assertSee('Private Applicant');
    }

    public function test_intake_permission_requires_office_membership_and_drafts_are_private_to_encoder(): void
    {
        $owner = $this->staff();
        $intake = app(WalkInIntakeService::class)->saveDraft(null, ['identity' => ['first_name' => 'Restricted']], null);
        $this->staff('CSWDO', false);
        $this->getJson(route('walk-in.create'))->assertForbidden();
        $this->staff('staff');
        $this->assertFalse(Gate::allows('walk-in-intake'));
        $this->staff();
        $this->getJson(route('walk-in.edit', $intake))->assertForbidden();
        $this->get(route('walk-in.index'))->assertOk()->assertDontSee('Restricted');
        $this->staff('superadmin', false);
        $this->get(route('walk-in.edit', $intake))->assertOk();
        $this->assertSame($owner->id, $intake->fresh()->encoded_by);
        $this->citizen();
        $this->getJson(route('walk-in.create'))->assertForbidden();
    }

    public function test_submission_creates_accountless_application_with_original_identity_and_duplicate_warning(): void
    {
        $encoder = $this->staff();
        $result = $this->submitted();
        $intake = $result['intake'];
        $application = $intake->application;
        $client = $application->client;
        $this->assertNull($client->user_id);
        $this->assertSame('Walk Applicant', $client->fullname());
        $this->assertSame($encoder->id, $application->beneficiary->created_by);
        $this->assertSame('Walk Applicant', $application->claimantAssignments()->first()->identity_snapshot['name']);
        $this->assertSame(hash('sha256', $result['code']), $intake->claim_code_hash);
        $this->assertSame(90, (int) now()->startOfDay()->diffInDays($intake->claim_expires_at->startOfDay()));
        $this->assertStringNotContainsString($result['code'], $intake->toJson());
        $draft = app(WalkInIntakeService::class)->saveDraft(null, $this->payload(), null);
        $this->assertSame([$client->uuid], app(WalkInIntakeService::class)->duplicates($draft)->pluck('uuid')->all());
        $values = app(DocumentPlaceholderRegistry::class)->values($application, ['client.first_name', 'client.last_name']);
        $this->assertSame('Walk', $values['client.first_name']);
        $this->get(route('application.show', $application))->assertOk()->assertSee('Walk Applicant')->assertDontSee($result['code']);
        $this->get(route('client.show', $client))->assertOk()->assertSee('Walk Applicant');
        $this->assertSame(1, User::count());
    }

    public function test_stale_drafts_and_repeat_submission_do_not_create_duplicate_applications(): void
    {
        $this->staff();
        $draft = app(WalkInIntakeService::class)->saveDraft(null, $this->payload(), null);
        app(WalkInIntakeService::class)->saveDraft($draft->uuid, $this->payload(), $draft->version);
        try {
            app(WalkInIntakeService::class)->submit($draft->uuid, $draft->version, [], true);
            $this->fail('A stale form submitted.');
        } catch (ValidationException) {
            $this->assertSame(0, Application::count());
        }
        $result = $this->submitted();
        try {
            app(WalkInIntakeService::class)->submit($result['intake']->uuid, $result['intake']->version, [], true);
            $this->fail('An intake submitted twice.');
        } catch (ValidationException) {
            $this->assertSame(1, Application::count());
        }
    }

    public function test_missing_required_document_prevents_submission_and_preserves_draft(): void
    {
        $this->app->instance('env', 'production');
        $this->staff();
        $document = DocumentRequirement::create(['name' => 'Death Certificate', 'source' => 'Registry', 'is_required' => true, 'is_active' => true]);
        $draft = app(WalkInIntakeService::class)->saveDraft(null, $this->payload(), null);
        try {
            app(WalkInIntakeService::class)->submit($draft->uuid, $draft->version, [], true);
            $this->fail('Missing evidence was accepted.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('images.'.$document->id, $exception->errors());
            $this->assertNull($draft->fresh()->application_uuid);
            $this->assertSame(0, Application::count());
        }
    }

    public function test_partial_upload_failure_preserves_successful_reference_and_retry_only_uploads_missing_document(): void
    {
        $this->app->instance('env', 'production');
        $this->staff();
        $first = DocumentRequirement::create(['name' => 'First Evidence', 'source' => 'Registry', 'is_required' => true, 'is_active' => true]);
        $second = DocumentRequirement::create(['name' => 'Second Evidence', 'source' => 'Registry', 'is_required' => true, 'is_active' => true]);
        $draft = app(WalkInIntakeService::class)->saveDraft(null, $this->payload(), null);
        Http::fakeSequence('https://files.test/*')->push('', 404)->push(['status' => 'success', 'file' => 'renamed-first.jpg.enc', 'folder' => 'returned-folder'])
            ->push('', 404)->push(['status' => 'failed'], 500)
            ->push('', 404)->push(['status' => 'success', 'file' => 'renamed-second.jpg.enc', 'folder' => 'returned-folder']);
        try {
            app(WalkInIntakeService::class)->submit($draft->uuid, $draft->version,
                [$first->id => UploadedFile::fake()->image('first.png'), $second->id => UploadedFile::fake()->image('second.png')], true);
            $this->fail('An upload failure was ignored.');
        } catch (\RuntimeException) {
            $this->assertSame(0, Application::count());
            $this->assertSame('renamed-first.jpg.enc', $draft->fresh()->documents[$first->id]['file_name']);
        }
        $result = app(WalkInIntakeService::class)->submit($draft->uuid, $draft->version, [$second->id => UploadedFile::fake()->image('second.png')], true);
        $this->assertNotNull($result['intake']->application_uuid);
        $this->assertSame(2, ApplicationDocumentFile::count());
        $this->assertSame('renamed-first.jpg.enc', ApplicationDocumentFile::where('document_requirement_id', $first->id)->value('file_name'));
        $this->assertCount(6, Http::recorded(fn ($request) => str_starts_with($request->url(), 'https://files.test/')));
    }

    public function test_livewire_submission_issues_private_one_time_receipt(): void
    {
        $this->staff();
        Livewire::test(Intake::class)->set('data', $this->payload())->call('save')->set('reviewed', true)->call('submit')->assertHasNoErrors()->assertRedirect(route('walk-in.receipt'));
        $this->assertStringNotContainsString('claim_code_hash', json_encode(session('walk_in_receipt')));
        $this->get(route('walk-in.receipt'))->assertOk()->assertSee('Private Claim Code')->assertHeader('Cache-Control', 'no-store, private');
        $this->getJson(route('walk-in.receipt'))->assertNotFound();
    }

    public function test_claim_links_only_its_intake_and_preserves_application_state_and_staff_attribution(): void
    {
        $encoder = $this->staff('superadmin', false);
        $result = $this->submitted();
        $unrelated = $this->submitted();
        $application = $result['intake']->application;
        $snapshot = $application->claimantAssignments()->first()->identity_snapshot;
        $assignment = $application->current_claimant_assignment_uuid;
        $interview = app(InterviewService::class)->store(['schedule' => now()->addDay()->toDateTimeString()], $application->client_uuid, $assignment);
        app(InterviewService::class)->done($interview->uuid);
        $assessment = app(AssessmentService::class)->store(['problem_presented' => 'Funeral costs', 'swa' => 'Confidential assessment example', 'expected_assignment_uuid' => $assignment], $application->uuid);
        $citizen = $this->citizen();
        $this->assertFalse(Gate::allows('view', $application));
        $this->post(route('walk-in.claim.store'), ['claim_code' => $result['code'], 'confirmed' => 1])->assertRedirect(route('application.show', $application));
        $this->assertSame($citizen->id, $application->client->fresh()->user_id);
        $this->assertSame($encoder->id, $application->beneficiary->created_by);
        $this->assertSame($assignment, $application->fresh()->current_claimant_assignment_uuid);
        $this->assertSame($snapshot, $application->claimantAssignments()->first()->identity_snapshot);
        $this->assertSame($assessment->uuid, $application->fresh()->assessment->uuid);
        $this->assertSame('done', $interview->fresh()->status);
        $this->assertSame($interview->uuid, app(InterviewService::class)->index((string) $citizen->id)->first()['id']);
        $this->assertNull($unrelated['intake']->client->user_id);
        $this->assertFalse(Gate::allows('view', $unrelated['intake']->application));
        $this->assertTrue(Beneficiary::index((string) $citizen->id)->whereKey($application->beneficiary_uuid)->exists());
        $this->get(route('application.show', $application))->assertOk()->assertDontSee('Confidential assessment example');
        $this->assertSame('Walk Applicant', $application->client->fresh()->fullname());
    }

    public function test_invalid_expired_and_reused_codes_return_same_error_without_flashing_secret(): void
    {
        $this->staff();
        $result = $this->submitted();
        $this->citizen();
        $this->post(route('walk-in.claim.store'), ['claim_code' => 'bad-secret', 'confirmed' => 1])->assertSessionHasErrors(['claim_code' => WalkInIntakeService::INVALID_CODE]);
        $this->assertArrayNotHasKey('claim_code', session()->getOldInput());
        $result['intake']->forceFill(['claim_expires_at' => now()->subSecond()])->save();
        $this->post(route('walk-in.claim.store'), ['claim_code' => $result['code'], 'confirmed' => 1])->assertSessionHasErrors(['claim_code' => WalkInIntakeService::INVALID_CODE]);
        $result['intake']->forceFill(['claim_expires_at' => now()->addDay()])->save();
        $this->post(route('walk-in.claim.store'), ['claim_code' => $result['code'], 'confirmed' => 1])->assertRedirect();
        $this->citizen();
        $this->post(route('walk-in.claim.store'), ['claim_code' => $result['code'], 'confirmed' => 1])->assertSessionHasErrors(['claim_code' => WalkInIntakeService::INVALID_CODE]);
    }

    public function test_claim_requires_active_citizen_sso_and_explicit_confirmation_and_is_rate_limited(): void
    {
        $this->staff();
        $this->postJson(route('walk-in.claim.store'), ['claim_code' => 'secret', 'confirmed' => 1])->assertForbidden();
        $this->citizen(false);
        $this->postJson(route('walk-in.claim.store'), ['claim_code' => 'secret', 'confirmed' => 1])->assertForbidden();
        $this->citizen();
        session()->forget('citizen.sso_uuid');
        $this->postJson(route('walk-in.claim.store'), ['claim_code' => 'secret', 'confirmed' => 1])->assertForbidden();
        $citizen = $this->citizen();
        $this->get(route('walk-in.claim'))->assertOk()->assertSee($citizen->email);
        $this->post(route('walk-in.claim.store'), ['claim_code' => 'secret'])->assertSessionHasErrors('confirmed');
        for ($attempt = 0; $attempt < 4; $attempt++) {
            $this->post(route('walk-in.claim.store'), ['claim_code' => 'secret', 'confirmed' => 1])->assertSessionHasErrors('claim_code');
        }
        $this->post(route('walk-in.claim.store'), ['claim_code' => 'secret', 'confirmed' => 1])->assertStatus(429);
    }

    public function test_replacement_invalidates_old_code_requires_verification_and_cannot_relink_claimed_records(): void
    {
        $this->staff();
        $result = $this->submitted();
        $intake = $result['intake'];
        $this->post(route('walk-in.code.replace', $intake), ['reason' => 'Lost receipt'])->assertSessionHasErrors('identity_verified');
        $code = app(WalkInIntakeService::class)->replaceCode($intake->uuid, 'Lost receipt', true);
        $this->assertNotSame(hash('sha256', $result['code']), $intake->fresh()->claim_code_hash);
        $this->citizen();
        $this->post(route('walk-in.claim.store'), ['claim_code' => $result['code'], 'confirmed' => 1])->assertSessionHasErrors('claim_code');
        $this->post(route('walk-in.claim.store'), ['claim_code' => $code, 'confirmed' => 1])->assertRedirect();
        $this->staff();
        $this->post(route('walk-in.code.replace', $intake), ['reason' => 'Try again', 'identity_verified' => 1])->assertSessionHasErrors('intake');
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function test_linking_before_or_after_replacement_preserves_former_claimant_read_only_access(bool $linkBeforeReplacement): void
    {
        $admin = $this->staff('superadmin', false);
        $result = $this->submitted();
        $application = $result['intake']->application;
        $former = null;
        if ($linkBeforeReplacement) {
            $former = $this->citizen();
            $this->post(route('walk-in.claim.store'), ['claim_code' => $result['code'], 'confirmed' => 1])->assertRedirect();
            $this->actingAs($admin);
        }
        $replacement = User::factory()->create(['citizen_uuid' => (string) Str::uuid()]);
        $draft = Client::factory()->make(['user_id' => $replacement->id]);
        $draft->save();
        $original = $application->client;
        $draft->demographic()->create($original->demographic->only(['sex_id', 'religion_id', 'nationality_id']));
        $draft->socialInfo()->create($original->socialInfo->only(['civil_id', 'education_id', 'philhealth', 'skill', 'income']));
        $draft->address()->create($original->address->only(['region_code', 'region_name', 'province_code', 'province_name', 'municipality_code', 'municipality_name', 'barangay_code', 'barangay_name', 'street', 'house_number']));
        $assignment = app(ClaimantReplacementService::class)->replace($application->uuid, ['client_uuid' => $draft->uuid, 'relationship_id' => 1,
            'reason' => 'Original claimant unavailable', 'expected_assignment_uuid' => $application->current_claimant_assignment_uuid]);
        if ($former === null) {
            $former = $this->citizen();
            $this->post(route('walk-in.claim.store'), ['claim_code' => $result['code'], 'confirmed' => 1])->assertRedirect();
        } else {
            $this->actingAs($former);
        }
        $application->refresh();
        $this->assertTrue($application->isFormerClaimant($former->id));
        $this->assertTrue(Gate::allows('view', $application));
        $this->assertFalse(Gate::allows('cancel', $application));
        $this->assertSame($assignment->uuid, $application->current_claimant_assignment_uuid);
        $this->assertSame($draft->uuid, $application->client_uuid);
        $this->assertSame(2, $application->claimantAssignments()->count());
    }

    public function test_new_sso_account_claims_walk_in_and_retains_access_after_signing_in_again(): void
    {
        $this->staff();
        $result = $this->submitted();
        $application = $result['intake']->application;
        auth()->logout();
        $uuid = (string) Str::uuid();
        config(['services.portal.users.enable.get' => true, 'services.portal.users.endpoint' => 'https://portal.test/users',
            'services.portal.users.key' => 'test', 'services.portal.sso.secret' => 'test-sso']);
        Http::fake(['https://portal.test/*' => Http::response(['data' => [[
            'user_id' => $uuid, 'firstname' => 'Portal', 'lastname' => 'Identity', 'middlename' => null,
            'suffix' => null, 'email' => 'later@portal.test', 'contact_number' => '09123456789',
        ]]])]);
        $payload = rtrim(strtr(base64_encode(json_encode(['citizen_uuid' => $uuid])), '+/', '-_'), '=');
        $url = '/sso/callback?'.http_build_query(['payload' => $payload, 'signature' => hash_hmac('sha256', $payload, 'test-sso')]);
        $this->get($url)->assertRedirect(route('client.create'));
        $this->post(route('walk-in.claim.store'), ['claim_code' => $result['code'], 'confirmed' => 1])->assertRedirect(route('application.show', $application));
        auth()->logout();
        session()->forget('citizen.sso_uuid');
        $this->get($url)->assertRedirect(route('dashboard'));
        $this->get(route('application.show', $application))->assertOk()->assertSee('Walk Applicant');
        $this->assertSame('later@portal.test', auth()->user()->email);
        $this->assertSame($uuid, auth()->user()->citizen_uuid);
        $this->assertSame('Walk Applicant', $application->client->fresh()->fullname());
        $this->assertCount(2, Http::recorded(fn ($request) => str_starts_with($request->url(), 'https://portal.test/')));
    }

    public function test_unsaved_edits_or_missing_review_confirmation_cannot_submit(): void
    {
        $this->staff();
        Livewire::test(Intake::class)->set('data', $this->payload())->call('save')->call('submit')->assertHasErrors('reviewed')
            ->set('reviewed', true)->set('data.identity.first_name', 'Changed')->call('submit')->assertHasErrors('intake');
        $this->assertSame(0, Application::count());
    }

    public function test_disabled_uploads_do_not_count_as_successful_storage(): void
    {
        $this->app->instance('env', 'production');
        $this->staff();
        config(['services.fileserver.enable.post' => false]);
        $document = DocumentRequirement::create(['name' => 'Required Evidence', 'source' => 'Registry', 'is_required' => true, 'is_active' => true]);
        $draft = app(WalkInIntakeService::class)->saveDraft(null, $this->payload(), null);
        try {
            app(WalkInIntakeService::class)->submit($draft->uuid, $draft->version, [$document->id => UploadedFile::fake()->image('proof.png')], true);
            $this->fail('A skipped upload was accepted.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('fileserver', $exception->errors());
            $this->assertNull($draft->fresh()->application_uuid);
            $this->assertNull($draft->fresh()->documents);
            $this->assertSame(0, ApplicationDocumentFile::count());
            Http::assertNotSent(fn ($request) => $request->method() === 'POST');
        }
    }

    public function test_walk_in_identity_is_escaped_and_incomplete_family_and_dates_can_be_saved(): void
    {
        $this->staff();
        $draft = app(WalkInIntakeService::class)->saveDraft(null, [
            'identity' => ['first_name' => '<script>alert(1)</script>'],
            'beneficiary' => ['date_of_death' => '2026-01-01'], 'family' => [['occupation' => 'Unfinished']],
        ], null);
        $this->get(route('walk-in.index'))->assertOk()->assertSee('<script>alert(1)</script>')->assertDontSee('<script>alert(1)</script>', false);
        $this->assertSame('Unfinished', $draft->data['family'][0]['occupation']);
    }

    public function test_competing_claims_link_exactly_one_account_and_reject_the_other(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            $this->markTestSkipped('This competing-claim fixture uses a disposable SQLite database.');
        }
        $this->staff();
        $result = $this->submitted();
        $citizens = User::factory()->count(2)->create()->each(fn ($user) => $user->update(['citizen_uuid' => (string) Str::uuid()]));
        $prefix = sys_get_temp_dir().DIRECTORY_SEPARATOR.'walk-in-race-'.bin2hex(random_bytes(6));
        $database = $prefix.'.sqlite';
        DB::statement('VACUUM INTO '.DB::getPdo()->quote($database));
        $workers = [];
        $files = [$database, $prefix.'-go', $prefix.'-0', $prefix.'-1'];
        try {
            foreach ($citizens as $index => $citizen) {
                $worker = new Process([PHP_BINARY, base_path('tests/Support/claim-walk-in.php')], base_path(), ['APP_ENV' => 'testing'], json_encode([
                    'database' => $database, 'user' => $citizen->id, 'code' => $result['code'], 'ready' => $prefix.'-'.$index, 'go' => $prefix.'-go',
                ]), 30);
                $worker->start();
                $workers[] = $worker;
            }
            $deadline = microtime(true) + 15;
            while (! is_file($prefix.'-0') || ! is_file($prefix.'-1')) {
                foreach ($workers as $worker) {
                    if (! $worker->isRunning()) {
                        $this->fail($worker->getErrorOutput().$worker->getOutput());
                    }
                }
                if (microtime(true) > $deadline) {
                    $this->fail('Claim workers did not start.');
                }
                usleep(10000);
            }
            file_put_contents($prefix.'-go', 'go');
            $outcomes = [];
            foreach ($workers as $worker) {
                $worker->wait();
                $this->assertTrue($worker->isSuccessful(), $worker->getErrorOutput().$worker->getOutput());
                $outcomes[] = trim($worker->getOutput());
            }
            sort($outcomes);
            $this->assertSame(['claimed', 'rejected'], $outcomes);
            config(['database.connections.walk_in_race_result' => ['driver' => 'sqlite', 'database' => $database, 'prefix' => '']]);
            $claimedBy = DB::connection('walk_in_race_result')->table('walk_in_intakes')->value('claimed_by');
            $this->assertContains($claimedBy, $citizens->pluck('id')->all());
            $this->assertSame($claimedBy, DB::connection('walk_in_race_result')->table('clients')->value('user_id'));
            $this->assertNull(DB::connection('walk_in_race_result')->table('walk_in_intakes')->value('claim_code_hash'));
        } finally {
            foreach ($workers as $worker) {
                if ($worker->isRunning()) {
                    $worker->stop();
                }
            }
            DB::purge('walk_in_race_result');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    public function test_documents_from_another_storage_environment_cannot_satisfy_submission(): void
    {
        $this->app->instance('env', 'production');
        $this->staff();
        $document = DocumentRequirement::create(['name' => 'Required Evidence', 'source' => 'Registry', 'is_required' => true, 'is_active' => true]);
        $draft = app(WalkInIntakeService::class)->saveDraft(null, $this->payload(), null);
        $draft->forceFill(['documents' => [$document->id => ['storage' => 'mock', 'folder' => 'test', 'file_name' => 'test.jpg.enc']]])->save();
        try {
            app(WalkInIntakeService::class)->submit($draft->uuid, $draft->version, [], true);
            $this->fail('A mock reference satisfied a remote submission.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('images', $exception->errors());
            $this->assertSame(0, Application::count());
        }
    }

    public function test_rollback_refuses_to_discard_saved_walk_in_data(): void
    {
        $this->staff();
        $draft = app(WalkInIntakeService::class)->saveDraft(null, ['identity' => ['first_name' => 'Preserved']], null);
        $migration = require database_path('migrations/data/2026_11_23_075051_create_walk_in_intakes_table.php');
        try {
            $migration->down();
            $this->fail('Rollback discarded intake records.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Preserve and migrate existing walk-in intake data before rolling back this feature.', $exception->getMessage());
            $this->assertSame('Preserved', $draft->fresh()->data['identity']['first_name']);
        }
    }

    public function test_local_intake_can_submit_without_required_document_images(): void
    {
        $this->staff();
        app()->instance('env', 'local');
        $this->mockOnline = true;
        config(['services.fileserver.enable.post' => false]);
        DocumentRequirement::create(['name' => 'Local Evidence', 'source' => 'Registry', 'is_required' => true, 'is_active' => true]);
        Livewire::test(Intake::class)->set('data', $this->payload())->call('save')
            ->assertSee('Images are optional and will not be stored in local/testing')
            ->set('reviewed', true)->call('submit')->assertHasNoErrors()->assertRedirect(route('walk-in.receipt'));
        $this->assertSame(1, Application::count());
        $this->assertSame(0, ApplicationDocumentFile::count());
        Http::assertNotSent(fn ($request) => $request->method() === 'POST');
    }

    #[TestWith(['production'])]
    #[TestWith(['staging'])]
    public function test_document_requirements_are_enforced_in_nonlocal_environments(string $environment): void
    {
        $this->staff();
        app()->instance('env', $environment);
        $document = DocumentRequirement::create(['name' => 'Required Evidence', 'source' => 'Registry', 'is_required' => true, 'is_active' => true]);
        $draft = app(WalkInIntakeService::class)->saveDraft(null, $this->payload(), null);
        try {
            app(WalkInIntakeService::class)->submit($draft->uuid, $draft->version, [], true);
            $this->fail('A nonlocal intake submitted without required evidence.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('images.'.$document->id, $exception->errors());
            $this->assertSame(0, Application::count());
        }
        Http::assertNotSent(fn ($request) => $request->method() === 'POST');
    }

    public function test_index_supplies_all_authorized_rows_for_datatable_search_and_pagination(): void
    {
        $this->staff();
        for ($index = 0; $index < 22; $index++) {
            app(WalkInIntakeService::class)->saveDraft(null, ['identity' => ['first_name' => 'Applicant '.$index]], null);
        }
        $this->get(route('walk-in.index'))->assertOk()
            ->assertViewHas('intakes', fn ($intakes) => $intakes->count() === 22)
            ->assertSee('Applicant 0')->assertSee('Applicant 21');
    }

    public function test_local_selected_images_use_the_online_mock_with_primary_uploads_disabled(): void
    {
        $this->staff();
        app()->instance('env', 'local');
        $this->mockOnline = true;
        config(['services.fileserver.enable.post' => false, 'services.fileserver.mock' => true]);
        $document = DocumentRequirement::create(['name' => 'Optional Evidence', 'source' => 'Registry', 'is_required' => true, 'is_active' => true]);
        Http::fake(['http://127.0.0.1:18991/apiburial/*' => fn ($request) => $request->method() === 'GET'
            ? Http::response([], 404) : Http::response(['status' => 'success', 'file' => 'mock-renamed.jpg.enc', 'folder' => 'mock-folder'])]);
        $draft = app(WalkInIntakeService::class)->saveDraft(null, $this->payload(), null);
        $result = app(WalkInIntakeService::class)->submit($draft->uuid, $draft->version, [$document->id => UploadedFile::fake()->image('test.png')], true);
        $this->assertNotNull($result['intake']->application_uuid);
        $reference = ApplicationDocumentFile::firstOrFail();
        $this->assertSame('mock', $reference->storage);
        $this->assertSame('mock-renamed.jpg.enc', $reference->file_name);
        Http::assertSent(fn ($request) => $request->method() === 'POST' && str_starts_with($request->url(), 'http://127.0.0.1:18991/apiburial/'));
        Http::assertNotSent(fn ($request) => str_starts_with($request->url(), 'https://files.test/'));
    }

    #[TestWith(['local', true, false, 'fileserver mock is offline'])]
    #[TestWith(['testing', false, false, 'fileserver mock is offline'])]
    #[TestWith(['production', false, false, 'Document uploads are disabled'])]
    public function test_blocked_fileserver_state_preserves_draft_and_warns_in_the_form(string $environment, bool $primary, bool $online, string $message): void
    {
        $this->staff();
        app()->instance('env', $environment);
        $this->mockOnline = $online;
        config(['services.fileserver.enable.post' => $primary, 'services.fileserver.mock' => true]);
        DocumentRequirement::create(['name' => 'Required Evidence', 'source' => 'Registry', 'is_required' => true, 'is_active' => true]);
        Livewire::test(Intake::class)->assertSee($message)->set('data', $this->payload())->call('save')
            ->assertHasNoErrors()->set('reviewed', true)->call('submit')->assertHasErrors('fileserver');
        $this->assertSame(1, WalkInIntake::count());
        $this->assertSame(0, Application::count());
        Http::assertNotSent(fn ($request) => $request->method() === 'POST');
    }

    public function test_local_draft_can_be_submitted_after_the_mock_becomes_available(): void
    {
        $this->staff();
        app()->instance('env', 'local');
        config(['services.fileserver.enable.post' => false, 'services.fileserver.mock' => true]);
        $form = Livewire::test(Intake::class)->set('data', $this->payload())->call('save')
            ->assertSee('fileserver mock is offline')->set('reviewed', true)->call('submit')->assertHasErrors('fileserver');
        $this->mockOnline = true;
        $form->call('checkFileserver')->assertDontSee('fileserver mock is offline')->call('submit')
            ->assertRedirect(route('walk-in.receipt'));
        $this->assertSame(1, Application::count());
    }

    public function test_disabling_walk_ins_blocks_all_feature_routes_and_open_forms_but_preserves_existing_applications(): void
    {
        $this->staff('superadmin', false);
        $submitted = $this->submitted();
        $draft = app(WalkInIntakeService::class)->saveDraft(null, $this->payload(), null);
        $form = Livewire::test(Intake::class, ['intake' => $draft]);
        SystemSetting::query()->update(['walk_in_intake_enabled' => false]);
        foreach (['walk-in.index', 'walk-in.create', 'walk-in.receipt'] as $name) {
            $this->getJson(route($name))->assertForbidden();
        }
        $this->getJson(route('walk-in.edit', $draft))->assertForbidden();
        $this->postJson(route('walk-in.code.replace', $submitted['intake']), ['reason' => 'Lost code', 'identity_verified' => 1])->assertForbidden();
        $form->call('save')->assertStatus(302)->assertHeader('Location', route('dashboard'));
        $this->assertSame($draft->version, $draft->fresh()->version);
        $this->assertFalse(Gate::allows('walk-in-intake'));
        $this->get(route('application.show', $submitted['intake']->application))->assertOk();
        $this->assertSame(1, Application::count());
        $this->citizen();
        $this->getJson(route('walk-in.claim'))->assertForbidden();
        $this->postJson(route('walk-in.claim.store'), ['claim_code' => $submitted['code'], 'confirmed' => 1])->assertForbidden();
        $this->assertFalse(Gate::allows('view-walk-in-claim'));
        $this->assertFalse(Gate::allows('claim-walk-in'));
        $this->assertNull($submitted['intake']->fresh()->claimed_by);
    }

    public function test_long_encrypted_beneficiary_names_can_be_saved_and_are_protected_from_destructive_rollback(): void
    {
        $this->staff();
        $payload = $this->payload();
        foreach (['first_name', 'middle_name', 'last_name', 'suffix'] as $field) {
            $payload['beneficiary'][$field] = str_repeat('x', 200);
        }
        $draft = app(WalkInIntakeService::class)->saveDraft(null, $payload, null);
        $result = app(WalkInIntakeService::class)->submit($draft->uuid, $draft->version, [], true);
        $beneficiary = $result['intake']->application->beneficiary;
        foreach (['first_name', 'middle_name', 'last_name', 'suffix'] as $field) {
            $this->assertSame(str_repeat('x', 200), $beneficiary->{$field});
            $this->assertGreaterThan(255, strlen($beneficiary->getRawOriginal($field)));
        }
        $migration = require database_path('migrations/data/2026_11_23_135953_widen_encrypted_beneficiary_name_columns.php');
        try {
            $migration->down();
            $this->fail('Rollback would truncate encrypted beneficiary names.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('Encrypted beneficiary names exceed the old column limit', $exception->getMessage());
        }
        $this->assertSame(str_repeat('x', 200), $beneficiary->fresh()->suffix);
    }

    public function test_accountless_walk_in_requires_interview_assessment_and_recommendation_before_workflow_processing(): void
    {
        $this->staff('superadmin', false);
        $application = $this->submitted()['intake']->application;
        $assignment = $application->current_claimant_assignment_uuid;
        $permission = Permission::findOrCreate('Workflow Walk-in Verification', 'web');
        $workflow = app(WorkflowVersionService::class)->publish(['name' => 'Walk-in Assistance', 'description' => 'Normal processing',
            'stages' => [['name' => 'Verification', 'description' => 'Verify assistance', 'permission_id' => (string) $permission->id]]]);
        $type = FuneralAssistanceType::create(['name' => 'Walk-in Assistance', 'description' => 'Assistance', 'workflow_uuid' => $workflow->uuid]);
        $mode = DB::table('mode_of_assistances')->insertGetId(['name' => 'Cash']);
        $recommendationData = ['workflow_uuid' => $workflow->uuid, 'funeral_assistance_type_uuid' => $type->uuid,
            'amount_extended' => 1000, 'mode_of_assistance_id' => $mode, 'expected_assignment_uuid' => $assignment, 'expected_recommendation_uuid' => null];
        $assessmentData = ['problem_presented' => 'Funeral costs', 'swa' => 'Needs assistance', 'expected_assignment_uuid' => $assignment];
        try {
            app(AssessmentService::class)->store($assessmentData, $application->uuid);
            $this->fail('Assessment bypassed the interview.');
        } catch (AuthorizationException) {
            $this->assertNull($application->fresh()->assessment);
        }
        try {
            app(RecommendationService::class)->store($recommendationData, $application->uuid);
            $this->fail('Recommendation bypassed the interview.');
        } catch (AuthorizationException) {
            $this->assertNull($application->fresh()->current_recommendation_uuid);
        }
        $interview = app(InterviewService::class)->store(['schedule' => now()->addDay()->toDateTimeString()], $application->client_uuid, $assignment);
        app(InterviewService::class)->done($interview->uuid);
        try {
            app(RecommendationService::class)->store($recommendationData, $application->uuid);
            $this->fail('Recommendation bypassed the assessment.');
        } catch (AuthorizationException) {
            $this->assertNull($application->fresh()->current_recommendation_uuid);
        }
        app(AssessmentService::class)->store($assessmentData, $application->uuid);
        $run = app(RecommendationService::class)->store($recommendationData, $application->uuid);
        $stage = $workflow->stages()->firstOrFail();
        $history = app(WorkflowHistoryService::class)->store(['recommendation_uuid' => $run->uuid, 'expected_stage_uuid' => null,
            'to_stage_uuid' => $stage->uuid, 'date_in' => now()->toDateTimeString(), 'date_out' => now()->toDateTimeString()]);
        $this->assertSame($stage->uuid, $application->fresh()->current_workflow_stage_uuid);
        $this->assertSame($run->uuid, $history->recommendation_uuid);
        $this->assertNull($application->client->user_id);
    }

    public function test_database_failure_preserves_draft_and_reports_database_error_without_logging_private_values(): void
    {
        $this->staff();
        $form = Livewire::test(Intake::class)->set('data', $this->payload())->call('save');
        $draft = WalkInIntake::firstOrFail();
        $previous = new \PDOException('Sensitive database error details');
        $previous->errorInfo = ['22001', 1406, 'Sensitive database error details'];
        $exception = new QueryException('walk_in_testing', 'insert into beneficiaries values (?)', ['private applicant value'], $previous);
        $this->partialMock(WalkInIntakeService::class, fn ($mock) => $mock->shouldReceive('submit')->once()->andThrow($exception));
        Log::spy();
        $form->set('reviewed', true)->call('submit')->assertHasErrors('intake')
            ->assertSee('The application could not be saved to the database.')->assertDontSee('Sensitive database error details');
        $this->assertNull($draft->fresh()->application_uuid);
        $this->assertSame(0, Application::count());
        Log::shouldHaveReceived('error')->once()->with('Walk-in application database save failed.', [
            'intake_uuid' => $draft->uuid, 'sql_state' => '22001', 'driver_code' => 1406,
        ]);
    }
}
