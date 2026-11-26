<?php

namespace Tests\Feature;

use App\Models\DocumentTemplate;
use App\Models\FuneralAssistanceType;
use App\Models\SuperadminMfaCredential;
use App\Models\User;
use App\Services\Auth\SuperadminMfa;
use App\Services\DocumentCleanupService;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use League\Flysystem\FilesystemOperator;
use Mockery;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DocumentCleanupTest extends TestCase
{
    private const SECRET = 'JBSWY3DPEHPK3PXP';

    protected function setUp(): void
    {
        parent::setUp();
        $this->freezeTime();
        config(['database.default' => 'cleanup_ui_testing',
            'database.connections.cleanup_ui_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'cache.default' => 'array', 'session.driver' => 'array', 'hashing.bcrypt.rounds' => 4,
            'app.key' => 'base64:'.base64_encode(str_repeat('c', 32)), 'services.psa_classification.get' => false]);
        Http::preventStrayRequests();
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        Artisan::call('migrate', ['--database' => 'cleanup_ui_testing', '--force' => true]);
        Storage::fake('document_templates');
    }

    protected function tearDown(): void
    {
        DB::purge('cleanup_ui_testing');
        parent::tearDown();
    }

    private function admin(): User
    {
        $admin = User::factory()->superadmin()->create();
        $this->actingAs($admin);
        session()->save();
        $this->withCookie(config('session.cookie'), session()->getId());

        return $admin;
    }

    private function file(string $path, ?int $timestamp = null, string $contents = 'document'): void
    {
        $disk = Storage::disk('document_templates');
        $disk->put($path, $contents);
        touch($disk->path($path), $timestamp ?? now()->subDays(2)->timestamp);
        clearstatcache();
    }

    private function reference(string $path, bool $deletedType = false): void
    {
        $type = FuneralAssistanceType::create(['name' => 'Cleanup template type', 'description' => 'Test type']);
        $template = new DocumentTemplate(['name' => 'Referenced template', 'path' => $path, 'original_filename' => 'template.docx']);
        $template->funeral_assistance_type_uuid = $type->uuid;
        $template->save();
        if ($deletedType) {
            $type->delete();
        }
    }

    private function preview(): string
    {
        $this->post(route('system.document-cleanup.preview'))->assertRedirect(route('system.document-cleanup.index'))->assertSessionHas('document_cleanup.preview');

        return session('document_cleanup.preview');
    }

    private function clean(string $token, ?string $code = null): TestResponse
    {
        return $this->post(route('system.document-cleanup.clean'), ['preview_token' => $token, 'confirm_deletion' => '1',
            'mfa_code' => $code ?? SuperadminMfa::otp(self::SECRET)->at(now()->timestamp)]);
    }

    private function partialDisk(): FilesystemAdapter
    {
        $real = Storage::disk('document_templates');
        $disk = Mockery::mock(FilesystemAdapter::class, [$real->getDriver(), $real->getAdapter(), $real->getConfig()])->makePartial();
        Storage::set('document_templates', $disk);

        return $disk;
    }

    public function test_preview_is_read_only_escapes_names_and_preserves_references_and_recent_files(): void
    {
        $this->admin();
        $this->file("templates/orphan'&.docx", now()->subHours(24)->timestamp);
        $this->file('generated/old.DOCX');
        $this->file('generated/recent.docx', now()->subHours(24)->addSecond()->timestamp);
        $this->file('templates/referenced.docx');
        $this->reference('templates/referenced.docx', true);
        $this->file('other/outside.docx');
        $this->file('templates/notes.txt');
        $this->get(route('system.index'))->assertOk()->assertSee('Document Cleanup');
        $token = $this->preview();
        $response = $this->get(route('system.document-cleanup.index'))->assertOk()
            ->assertSee('templates/orphan&#039;&amp;.docx', false)->assertDontSee("templates/orphan'&.docx", false)
            ->assertSee('generated/old.DOCX')->assertDontSee('generated/recent.docx')->assertDontSee('templates/referenced.docx')
            ->assertDontSee('other/outside.docx')->assertDontSee('templates/notes.txt')
            ->assertDontSee(Storage::disk('document_templates')->path(''))
            ->assertHeader('Referrer-Policy', 'no-referrer');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertCount(2, Cache::get('document-cleanup.preview.'.$token)['files']);
        $this->assertCount(6, Storage::disk('document_templates')->allFiles());
        $this->assertSame(1, DocumentTemplate::count());
        $this->assertSame(0, Activity::where('log_name', 'document-cleanup')->count());
        Http::assertNothingSent();
    }

    public function test_clean_deletes_only_confirmed_snapshot_and_records_start_and_outcome_without_codes(): void
    {
        $admin = $this->admin();
        $this->file('templates/orphan.docx');
        $token = $this->preview();
        $this->file('generated/after-preview.docx');
        $this->clean($token)->assertRedirect(route('system.document-cleanup.index'))->assertSessionHas('cleanup_result', fn ($result) => $result['deleted'] === 1 && $result['status'] === 'completed');
        Storage::disk('document_templates')->assertMissing('templates/orphan.docx');
        Storage::disk('document_templates')->assertExists('generated/after-preview.docx');
        $this->assertNull(Cache::get('document-cleanup.preview.'.$token));
        $entries = Activity::where('log_name', 'document-cleanup')->orderBy('id')->get();
        $this->assertCount(2, $entries);
        $this->assertSame($admin->id, $entries[0]->causer_id);
        $this->assertSame('started', $entries[0]->getProperty('status'));
        $this->assertSame('completed', $entries[1]->getProperty('status'));
        $this->assertSame(1, $entries[1]->getProperty('deleted'));
        $this->assertSame(24, $entries[1]->getProperty('hours'));
        $this->assertSame($entries[0]->getProperty('operation'), $entries[1]->getProperty('operation'));
        $this->assertStringNotContainsString(SuperadminMfa::otp(self::SECRET)->at(now()->timestamp), $entries->toJson());
        $this->assertStringNotContainsString('orphan.docx', $entries->toJson());
        $this->get(route('system.document-cleanup.index'))->assertOk()->assertSee('Deleted: 1.')->assertDontSee('Delete previewed files');
        $this->travel(30)->seconds();
        $this->clean($token)->assertSessionHasErrors('preview_token');
        Storage::disk('document_templates')->assertExists('generated/after-preview.docx');
    }

    public function test_clean_skips_new_references_and_files_with_changed_metadata_or_missing_files(): void
    {
        $this->admin();
        foreach (['referenced', 'size-changed', 'time-changed', 'missing', 'delete'] as $name) {
            $this->file('templates/'.$name.'.docx');
        }
        $token = $this->preview();
        $this->reference('templates/referenced.docx');
        $this->file('templates/size-changed.docx', contents: 'a different size');
        $this->file('templates/time-changed.docx', now()->subHours(49)->timestamp);
        Storage::disk('document_templates')->delete('templates/missing.docx');
        $this->clean($token)->assertSessionHas('cleanup_result', fn ($result) => $result['deleted'] === 1 && $result['skipped'] === 4);
        Storage::disk('document_templates')->assertExists(['templates/referenced.docx', 'templates/size-changed.docx', 'templates/time-changed.docx']);
        Storage::disk('document_templates')->assertMissing('templates/delete.docx');
        $this->assertSame(1, DocumentTemplate::count());
    }

    public function test_every_endpoint_requires_an_active_superadmin_with_completed_mfa(): void
    {
        $routes = ['index' => 'getJson', 'preview' => 'postJson', 'clean' => 'postJson'];
        foreach ($routes as $route => $method) {
            $this->$method(route('system.document-cleanup.'.$route))->assertUnauthorized();
        }
        $staff = User::factory()->create();
        $staff->assignRole(Role::findOrCreate('staff', 'web'));
        foreach ($routes as $route => $method) {
            $this->actingAs($staff)->$method(route('system.document-cleanup.'.$route))->assertForbidden();
        }
        $admin = $this->admin();
        foreach ($routes as $route => $method) {
            $this->actingAs($admin);
            session()->forget('mfa.proof');
            session()->save();
            $this->$method(route('system.document-cleanup.'.$route))->assertForbidden();
        }
        $admin->update(['is_active' => false]);
        foreach ($routes as $route => $method) {
            $this->actingAs($admin)->$method(route('system.document-cleanup.'.$route))->assertForbidden();
        }
    }

    public function test_confirmation_and_authenticator_code_are_required_without_flashing_the_code(): void
    {
        $this->admin();
        $this->file('templates/orphan.docx');
        $token = $this->preview();
        $this->post(route('system.document-cleanup.clean'), ['preview_token' => $token, 'mfa_code' => '123456'])
            ->assertSessionHasErrors('confirm_deletion')->assertSessionMissing('_old_input.mfa_code');
        $this->post(route('system.document-cleanup.clean'), ['preview_token' => $token, 'confirm_deletion' => '1'])->assertSessionHasErrors('mfa_code');
        $this->clean($token, str_repeat('a', 32))->assertSessionHasErrors('mfa_code');
        Storage::disk('document_templates')->assertExists('templates/orphan.docx');
        $this->assertSame(0, Activity::where('log_name', 'document-cleanup')->count());
    }

    public function test_invalid_and_previously_consumed_authenticator_codes_cannot_delete(): void
    {
        $admin = $this->admin();
        $this->file('templates/orphan.docx');
        $token = $this->preview();
        $this->clean($token, SuperadminMfa::otp(self::SECRET)->at(now()->subMinutes(5)->timestamp))->assertSessionHasErrors('mfa_code');
        app(SuperadminMfa::class)->protect($admin, SuperadminMfa::otp(self::SECRET)->at(now()->timestamp), fn () => null);
        $this->clean($token)->assertSessionHasErrors('mfa_code');
        Storage::disk('document_templates')->assertExists('templates/orphan.docx');
        $this->assertNotNull(Cache::get('document-cleanup.preview.'.$token));
        $this->assertSame(0, Activity::where('log_name', 'document-cleanup')->count());
    }

    public function test_missing_tampered_and_expired_preview_tokens_cannot_delete(): void
    {
        $this->admin();
        $this->file('templates/orphan.docx');
        $this->clean('')->assertSessionHasErrors('preview_token');
        $token = $this->preview();
        $this->clean(str_repeat('x', 64))->assertSessionHasErrors('preview_token');
        $this->travel(15)->minutes();
        $this->clean($token)->assertSessionHasErrors('preview_token');
        Storage::disk('document_templates')->assertExists('templates/orphan.docx');
    }

    public function test_preview_cannot_be_used_by_another_account_or_session(): void
    {
        $original = $this->admin();
        $this->file('templates/orphan.docx');
        $token = $this->preview();
        $this->admin();
        $this->clean($token)->assertSessionHasErrors('preview_token');
        $this->actingAs($original);
        session()->regenerate();
        session()->save();
        $this->withCookie(config('session.cookie'), session()->getId());
        $this->clean($token)->assertSessionHasErrors('preview_token');
        Storage::disk('document_templates')->assertExists('templates/orphan.docx');
    }

    public function test_replaced_mfa_credentials_invalidate_old_previews_even_after_signing_in_again(): void
    {
        $admin = $this->admin();
        $this->file('templates/orphan.docx');
        $token = $this->preview();
        SuperadminMfaCredential::where('user_id', $admin->id)->update(['version' => (string) Str::uuid()]);
        $this->actingAs($admin);
        session()->save();
        $this->clean($token)->assertSessionHasErrors('preview_token');
        Storage::disk('document_templates')->assertExists('templates/orphan.docx');
    }

    public function test_new_preview_invalidates_the_previous_token(): void
    {
        $this->admin();
        $this->file('templates/orphan.docx');
        $old = $this->preview();
        $new = $this->preview();
        $this->assertNotSame($old, $new);
        $this->assertNull(Cache::get('document-cleanup.preview.'.$old));
        $this->clean($old)->assertSessionHasErrors('preview_token');
        $this->clean($new)->assertSessionHas('cleanup_result', fn ($result) => $result['deleted'] === 1);
    }

    public function test_batches_are_limited_to_one_hundred_and_following_previews_find_the_rest(): void
    {
        $this->admin();
        for ($index = 0; $index < 101; $index++) {
            $this->file('generated/output-'.$index.'.docx');
        }
        $token = $this->preview();
        $this->get(route('system.document-cleanup.index'))->assertOk()->assertSee('incomplete scan');
        $this->assertCount(100, Cache::get('document-cleanup.preview.'.$token)['files']);
        $this->clean($token)->assertSessionHas('cleanup_result', fn ($result) => $result['deleted'] === 100);
        $next = $this->preview();
        $this->assertCount(1, Cache::get('document-cleanup.preview.'.$next)['files']);
        $this->assertCount(1, Storage::disk('document_templates')->allFiles());
    }

    public function test_time_budget_limits_preview_and_leaves_a_visible_partial_cleanup_result(): void
    {
        $this->admin();
        $this->file('templates/orphan.docx');
        $service = Mockery::mock(DocumentCleanupService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('clock')->andReturn(0, 10);
        $this->app->instance(DocumentCleanupService::class, $service);
        $token = $this->preview();
        $this->assertTrue(Cache::get('document-cleanup.preview.'.$token)['limited']);
        $this->assertSame([], Cache::get('document-cleanup.preview.'.$token)['files']);
        $this->app->instance(DocumentCleanupService::class, new DocumentCleanupService);
        $token = $this->preview();
        $service = Mockery::mock(DocumentCleanupService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('clock')->andReturn(0, 10);
        $this->app->instance(DocumentCleanupService::class, $service);
        $this->clean($token)->assertSessionHas('cleanup_result', fn ($result) => $result['status'] === 'partial' && $result['unprocessed'] === 1 && $result['deleted'] === 0);
        Storage::disk('document_templates')->assertExists('templates/orphan.docx');
        $this->get(route('system.document-cleanup.index'))->assertOk()->assertSee('Unprocessed: 1.');
    }

    public function test_concurrent_cleanup_is_refused_without_consuming_preview_or_code(): void
    {
        $this->admin();
        $this->file('templates/orphan.docx');
        $token = $this->preview();
        $lock = Cache::lock('document-cleanup.execution', 120);
        $this->assertTrue($lock->get());
        $this->clean($token)->assertSessionHas('cleanup_error', 'Another document cleanup is running. Please wait, then try again.');
        Storage::disk('document_templates')->assertExists('templates/orphan.docx');
        $lock->release();
        $this->clean($token)->assertSessionHas('cleanup_result', fn ($result) => $result['deleted'] === 1);
    }

    public function test_database_failure_before_cleanup_deletes_nothing_and_reports_failure(): void
    {
        $this->admin();
        $this->file('templates/orphan.docx');
        $token = $this->preview();
        Schema::drop('document_templates');
        $this->clean($token)->assertSessionHas('cleanup_result', fn ($result) => $result['initial_failed'] && $result['deleted'] === 0 && $result['unprocessed'] === 1);
        Storage::disk('document_templates')->assertExists('templates/orphan.docx');
        $this->assertSame('failed', Activity::where('log_name', 'document-cleanup')->orderByDesc('id')->first()->getProperty('status'));
    }

    public function test_preview_database_failure_removes_stale_preview_and_never_enables_deletion(): void
    {
        $this->admin();
        $this->file('templates/orphan.docx');
        $token = $this->preview();
        Schema::drop('document_templates');
        $this->post(route('system.document-cleanup.preview'))->assertSessionHas('cleanup_error')->assertSessionMissing('document_cleanup.preview');
        $this->assertNull(Cache::get('document-cleanup.preview.'.$token));
        $this->get(route('system.document-cleanup.index'))->assertOk()->assertDontSee('Delete previewed files');
        Storage::disk('document_templates')->assertExists('templates/orphan.docx');
    }

    public function test_storage_listing_failure_is_sanitized_and_prevents_deletion(): void
    {
        $this->admin();
        $this->file('templates/orphan.docx');
        $token = $this->preview();
        $driver = Mockery::mock(FilesystemOperator::class);
        $driver->shouldReceive('listContents')->andThrow(new \RuntimeException('Sensitive C:/private/path and token'));
        $this->partialDisk()->shouldReceive('getDriver')->andReturn($driver);
        $this->clean($token)->assertSessionHas('cleanup_result', fn ($result) => $result['initial_failed'] && $result['deleted'] === 0);
        $this->get(route('system.document-cleanup.index'))->assertOk()->assertDontSee('Sensitive')->assertDontSee('C:/private/path');
        $this->assertSame(2, Activity::where('log_name', 'document-cleanup')->count());
    }

    public function test_partial_deletion_failure_reports_counts_and_consumes_preview(): void
    {
        $this->admin();
        $this->file('templates/fails.docx');
        $this->file('templates/succeeds.docx');
        $token = $this->preview();
        $disk = $this->partialDisk();
        $disk->shouldReceive('delete')->with('templates/fails.docx')->once()->andReturn(false);
        $this->clean($token)->assertSessionHas('cleanup_result', fn ($result) => $result['status'] === 'partial' && $result['failed'] === 1 && $result['deleted'] === 1);
        $disk->assertExists('templates/fails.docx');
        $disk->assertMissing('templates/succeeds.docx');
        $this->assertNull(Cache::get('document-cleanup.preview.'.$token));
    }

    public function test_individual_preview_metadata_failure_excludes_that_file_and_reports_it(): void
    {
        $this->admin();
        $this->file('templates/fails.docx');
        $this->file('templates/works.docx');
        $disk = $this->partialDisk();
        $disk->shouldReceive('size')->with('templates/fails.docx')->andThrow(new \RuntimeException('Private storage diagnostics'));
        $token = $this->preview();
        $snapshot = Cache::get('document-cleanup.preview.'.$token);
        $this->assertSame(['templates/fails.docx'], $snapshot['failed']);
        $this->assertCount(1, $snapshot['files']);
        $this->get(route('system.document-cleanup.index'))->assertOk()->assertSee('1 file(s) could not be inspected')->assertDontSee('Private storage diagnostics');
        $this->clean($token)->assertSessionHas('cleanup_result', fn ($result) => $result['deleted'] === 1);
        $disk->assertExists('templates/fails.docx');
    }

    public function test_rate_limit_is_shared_by_preview_and_cleanup_and_is_per_account(): void
    {
        $this->admin();
        for ($index = 0; $index < 5; $index++) {
            $this->preview();
        }
        $this->postJson(route('system.document-cleanup.preview'))->assertTooManyRequests();
        $this->postJson(route('system.document-cleanup.clean'))->assertTooManyRequests();
        $this->admin();
        $this->preview();
    }

    public function test_client_supplied_paths_and_age_cannot_expand_the_confirmed_batch(): void
    {
        $this->admin();
        $this->file('templates/old.docx');
        $this->file('templates/recent.docx', now()->timestamp);
        $this->file('outside/old.docx');
        $token = $this->preview();
        $this->post(route('system.document-cleanup.clean'), ['preview_token' => $token, 'confirm_deletion' => '1',
            'mfa_code' => SuperadminMfa::otp(self::SECRET)->at(now()->timestamp), 'hours' => 0,
            'files' => ['templates/recent.docx', 'outside/old.docx']])->assertSessionHas('cleanup_result', fn ($result) => $result['deleted'] === 1);
        Storage::disk('document_templates')->assertMissing('templates/old.docx');
        Storage::disk('document_templates')->assertExists(['templates/recent.docx', 'outside/old.docx']);
    }

    public function test_lost_execution_lock_stops_before_deleting_a_file(): void
    {
        $this->admin();
        $this->file('templates/orphan.docx');
        $token = $this->preview();
        $disk = $this->partialDisk();
        $disk->shouldReceive('lastModified')->once()->andReturnUsing(function (): int {
            Cache::lock('document-cleanup.execution')->forceRelease();

            return now()->subDays(2)->timestamp;
        });
        $disk->shouldNotReceive('delete');
        $this->clean($token)->assertSessionHas('cleanup_result', fn ($result) => $result['unprocessed'] === 1 && $result['deleted'] === 0);
        $disk->assertExists('templates/orphan.docx');
    }

    public function test_missing_start_audit_prevents_deletion_and_keeps_preview_available(): void
    {
        $this->admin();
        $this->file('templates/orphan.docx');
        $token = $this->preview();
        Schema::drop('activity_log');
        $this->clean($token)->assertSessionHas('cleanup_error');
        Storage::disk('document_templates')->assertExists('templates/orphan.docx');
        $this->assertNotNull(Cache::get('document-cleanup.preview.'.$token));
    }

    public function test_disabled_or_cancelled_audit_logging_prevents_deletion(): void
    {
        $this->admin();
        $this->file('templates/orphan.docx');
        $token = $this->preview();
        activity()->disableLogging();
        try {
            $this->clean($token)->assertSessionHas('cleanup_error', 'Cleanup could not start. No files were deleted. Please try again later.');
            Storage::disk('document_templates')->assertExists('templates/orphan.docx');
            $this->assertNotNull(Cache::get('document-cleanup.preview.'.$token));
        } finally {
            activity()->enableLogging();
        }
        Activity::creating(fn (Activity $activity): bool => $activity->log_name !== 'document-cleanup');
        try {
            $this->clean($token)->assertSessionHas('cleanup_error', 'Cleanup could not start. No files were deleted. Please try again later.');
            Storage::disk('document_templates')->assertExists('templates/orphan.docx');
            $this->assertSame(0, Activity::where('log_name', 'document-cleanup')->count());
        } finally {
            Activity::flushEventListeners();
        }
    }

    public function test_outcome_audit_failure_does_not_restore_files_or_allow_replay(): void
    {
        $this->admin();
        $this->file('templates/orphan.docx');
        $token = $this->preview();
        Activity::creating(function (Activity $activity): void {
            if ($activity->log_name === 'document-cleanup' && $activity->getProperty('status') === 'completed') {
                throw new \RuntimeException('Private audit diagnostics');
            }
        });
        try {
            $this->clean($token)->assertSessionHas('cleanup_error');
            Storage::disk('document_templates')->assertMissing('templates/orphan.docx');
            $this->assertNull(Cache::get('document-cleanup.preview.'.$token));
            $this->assertSame(1, Activity::where('log_name', 'document-cleanup')->count());
            $this->get(route('system.document-cleanup.index'))->assertOk()->assertSee('Some files may have been removed.')->assertDontSee('Private audit diagnostics');
        } finally {
            Activity::flushEventListeners();
        }
    }
}
