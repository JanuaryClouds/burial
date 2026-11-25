<?php

namespace Tests\Feature;

use App\Livewire\Application\Create;
use App\Models\Application;
use App\Models\ApplicationDocumentFile;
use App\Models\DocumentRequirement;
use App\Services\ApplicationSubmissionService;
use App\Services\FileserverPolicy;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Support\EnvironmentFixtures;
use Tests\TestCase;

class EnvironmentAwareSubmissionTest extends TestCase
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
    public function test_development_without_mock_submits_without_images_or_remote_calls(string $environment): void
    {
        $this->app->instance('env', $environment);
        $owner = $this->citizen();
        $client = $this->client($owner);
        $beneficiary = $this->beneficiary($owner);
        DocumentRequirement::create(['name' => 'Proof', 'source' => 'Test', 'is_required' => true, 'is_active' => true]);
        $service = app(ApplicationSubmissionService::class);
        $draft = $service->prepare($client->uuid, $beneficiary->uuid, 1);
        $application = $service->submit($draft->uuid, $draft->version, ['invalid-key' => 'discarded']);
        $this->assertSame($client->uuid, $application->client_uuid);
        $this->assertSame(0, ApplicationDocumentFile::count());
        Http::assertNothingSent();
    }

    public function test_required_image_and_disabled_production_uploads_preserve_draft(): void
    {
        $owner = $this->citizen();
        $client = $this->client($owner);
        $beneficiary = $this->beneficiary($owner);
        $document = DocumentRequirement::create(['name' => 'Proof', 'source' => 'Test', 'is_required' => true, 'is_active' => true]);
        $service = app(ApplicationSubmissionService::class);
        $draft = $service->prepare($client->uuid, $beneficiary->uuid, 1);
        $this->app->instance('env', 'production');
        foreach ([[], [$document->uploadKey() => UploadedFile::fake()->image('proof.jpg')]] as $images) {
            config(['services.fileserver.enable.post' => false]);
            try {
                $service->submit($draft->uuid, $draft->version, $images);
                $this->fail('Required upload was bypassed.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('images', $exception->errors());
            }
        }
        $this->assertNull($draft->fresh()->application_uuid);
        $this->assertSame(0, Application::count());
        Http::assertNothingSent();
    }

    public function test_mock_is_explicit_and_outage_blocks_submission(): void
    {
        config(['services.fileserver.mock' => true]);
        Http::fake(['http://127.0.0.1:18991/health' => Http::response([], 503)]);
        $this->assertNotNull(app(FileserverPolicy::class)->warning());
        $this->assertSame('mock', app(ImageService::class)->destination());
        $this->app->instance('env', 'production');
        Http::fake();
        $this->assertFalse(app(FileserverPolicy::class)->mock());
        $this->assertNull(app(FileserverPolicy::class)->warning());
        $this->assertSame('remote', app(ImageService::class)->destination());
        Http::assertNothingSent();
    }

    public function test_partial_uploads_resume_and_submitted_draft_cannot_be_reused(): void
    {
        $owner = $this->citizen();
        $client = $this->client($owner);
        $beneficiary = $this->beneficiary($owner);
        $first = DocumentRequirement::create(['name' => 'First', 'source' => 'Test', 'is_required' => true, 'is_active' => true]);
        $second = DocumentRequirement::create(['name' => 'Second', 'source' => 'Test', 'is_required' => true, 'is_active' => true]);
        $service = app(ApplicationSubmissionService::class);
        $draft = $service->prepare($client->uuid, $beneficiary->uuid, 1);
        $this->app->instance('env', 'production');
        $uploads = 0;
        Http::fake(['https://files.test/*' => function ($request) use (&$uploads) {
            if ($request->method() === 'GET') {
                return Http::response([], 404);
            }
            $uploads++;

            return $uploads === 2 ? Http::response([], 503) : Http::response(['status' => 'success', 'file' => 'saved-'.$uploads.'.jpg.enc', 'folder' => 'documents']);
        }]);
        try {
            $service->submit($draft->uuid, $draft->version, [$first->uploadKey() => UploadedFile::fake()->image('first.jpg'), $second->uploadKey() => UploadedFile::fake()->image('second.jpg')]);
            $this->fail('Upload failure was ignored.');
        } catch (\RuntimeException) {
            $this->assertCount(1, $draft->fresh()->documents);
        }
        $application = $service->submit($draft->uuid, $draft->version, [$second->uploadKey() => UploadedFile::fake()->image('retry.jpg')]);
        $this->assertSame(3, $uploads);
        $this->assertSame(2, ApplicationDocumentFile::where('application_uuid', $application->uuid)->count());
        $this->expectException(ValidationException::class);
        $service->submit($draft->uuid, $draft->version, []);
    }

    public function test_drafts_reject_other_users_stale_versions_and_foreign_storage(): void
    {
        $owner = $this->citizen();
        $client = $this->client($owner);
        $beneficiary = $this->beneficiary($owner);
        $document = DocumentRequirement::create(['name' => 'Proof', 'source' => 'Registry', 'is_required' => true, 'is_active' => true]);
        $service = app(ApplicationSubmissionService::class);
        $draft = $service->prepare($client->uuid, $beneficiary->uuid, 1);
        $this->citizen();
        try {
            $service->submit($draft->uuid, $draft->version, []);
            $this->fail('Another citizen used the draft.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
        $this->actingAs($owner);
        try {
            $service->submit($draft->uuid, $draft->version + 1, []);
            $this->fail('A stale version was accepted.');
        } catch (ValidationException) {
        }
        $draft->update(['documents' => [$document->id => ['storage' => 'mock', 'folder' => 'test', 'file_name' => 'proof.jpg.enc']]]);
        $this->app->instance('env', 'production');
        $this->expectException(ValidationException::class);
        $service->submit($draft->uuid, $draft->version, []);
    }

    public function test_development_mock_requires_documents_and_never_uses_real_endpoints(): void
    {
        $owner = $this->citizen();
        $client = $this->client($owner);
        $beneficiary = $this->beneficiary($owner);
        $document = DocumentRequirement::create(['name' => 'Proof', 'source' => 'Registry', 'is_required' => true, 'is_active' => true]);
        config(['services.fileserver.mock' => true]);
        Http::fake(['http://127.0.0.1:18991/health' => Http::response(['service' => 'burial-fileserver-mock']),
            'http://127.0.0.1:18991/apiburial/*' => fn ($request) => $request->method() === 'GET' ? Http::response([], 404) : Http::response(['status' => 'success', 'file' => 'stored.jpg.enc', 'folder' => 'proof'])]);
        $service = app(ApplicationSubmissionService::class);
        $draft = $service->prepare($client->uuid, $beneficiary->uuid, 1);
        try {
            $service->submit($draft->uuid, $draft->version, []);
            $this->fail('Required image was omitted with an active mock.');
        } catch (ValidationException) {
        }
        $application = $service->submit($draft->uuid, $draft->version, [$document->uploadKey() => UploadedFile::fake()->image('proof.jpg')]);
        $this->assertSame(1, ApplicationDocumentFile::where('application_uuid', $application->uuid)->count());
        Http::assertNotSent(fn ($request) => str_starts_with($request->url(), 'https://files.test/'));
    }

    public function test_citizen_livewire_form_allows_mock_off_submission_without_image_selection(): void
    {
        $owner = $this->citizen();
        $client = $this->client($owner);
        $beneficiary = $this->beneficiary($owner);
        Livewire::test(Create::class)
            ->set('clientUuid', $client->uuid)->set('beneficiaryUuid', $beneficiary->uuid)->set('relationshipId', '1')
            ->set('agreedToTerms', true)->call('save')->assertHasNoErrors();
        $this->assertSame(1, Application::count());
        Http::assertNothingSent();
    }

    public function test_nonapplicable_religious_requirements_do_not_block_submission(): void
    {
        $owner = $this->citizen();
        $client = $this->client($owner);
        $beneficiary = $this->beneficiary($owner);
        DocumentRequirement::create(['name' => 'Muslim Evidence', 'source' => 'Registry', 'is_required' => true, 'is_active' => true, 'for_muslim' => true]);
        $this->app->instance('env', 'production');
        $service = app(ApplicationSubmissionService::class);
        $draft = $service->prepare($client->uuid, $beneficiary->uuid, 1);
        $service->submit($draft->uuid, $draft->version, []);
        $this->assertSame(1, Application::count());
        Http::assertNothingSent();
    }

    public function test_reload_restores_saved_documents_with_existing_session_selections(): void
    {
        $owner = $this->citizen();
        $client = $this->client($owner);
        $beneficiary = $this->beneficiary($owner);
        $document = DocumentRequirement::create(['name' => 'Saved Proof', 'source' => 'Registry', 'is_required' => true, 'is_active' => true]);
        $draft = app(ApplicationSubmissionService::class)->prepare($client->uuid, $beneficiary->uuid, 1);
        $draft->update(['documents' => [$document->id => ['storage' => 'mock', 'folder' => 'proof', 'file_name' => 'saved.jpg.enc']]]);
        config(['services.fileserver.mock' => true]);
        session(['client_uuid' => $client->uuid, 'beneficiary_uuid' => $beneficiary->uuid]);
        Http::fake(['http://127.0.0.1:18991/health' => Http::response(['service' => 'burial-fileserver-mock'])]);
        Livewire::test(Create::class)
            ->assertSet('draftUuid', $draft->uuid)->assertSet('relationshipId', '1')
            ->assertSee('is saved with this draft')->set('agreedToTerms', true)->call('save')->assertHasNoErrors();
        $this->assertSame(1, Application::count());
        $this->assertSame(1, ApplicationDocumentFile::count());
        Http::assertNotSent(fn ($request) => $request->method() === 'POST');
    }
}
