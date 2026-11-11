<?php

namespace Tests\Feature;

use App\Http\Controllers\ImageController;
use App\Models\Application;
use App\Models\ApplicationDocumentFile;
use App\Models\DocumentRequirement;
use App\Models\User;
use App\Services\Auth\AccountAccess;
use App\Services\ImagePayload;
use App\Services\ImageService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ImageDeliveryTest extends TestCase
{
    private Application $application;

    private User $actor;

    public function test_image_viewer_defers_image_loading_to_the_status_aware_loader(): void
    {
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        $html = view('components.image.viewer', ['src' => '/application/test/image/death_certificate', 'alt' => 'Death Certificate'])->render();
        $this->assertStringContainsString('data-document-viewer', $html);
        $this->assertStringContainsString('data-document-image-src="/application/test/image/death_certificate"', $html);
        $this->assertStringContainsString('data-document-message', $html);
        $this->assertDoesNotMatchRegularExpression('/<img[^>]+\ssrc=/', $html);
    }

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'cache.default' => 'array',
            'database.default' => 'images_testing',
            'database.connections.images_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
            'services.fileserver.mock' => false,
            'services.fileserver.get.endpoint' => 'https://files.example.invalid/apiburial/',
            'services.fileserver.get.key' => 'remote-test-secret',
            'services.fileserver.post.endpoint' => 'https://files.example.invalid/apiburial/',
            'services.fileserver.post.key' => 'remote-test-secret',
            'services.fileserver.api' => '/upload',
            'services.fileserver.enable.get' => true,
            'services.fileserver.enable.post' => true,
            'services.fileserver.enc_key' => 'base64:'.base64_encode(str_repeat('a', 32)),
        ]);
        Schema::create('document_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        DB::table('document_requirements')->insert(['name' => 'Death Certificate']);
        Schema::create('applications', fn (Blueprint $table) => $table->uuid('uuid')->primary());
        DB::table('applications')->insert(['uuid' => 'test-application']);
        (require database_path('migrations/data/2026_11_11_120000_create_application_document_files_table.php'))->up();
        $this->application = new Application;
        $this->application->forceFill(['uuid' => 'test-application', 'tracking_no' => 'test123']);
        $this->actor = \Mockery::mock(User::class)->makePartial();
        auth()->setUser($this->actor);
        Http::preventStrayRequests();
    }

    protected function tearDown(): void
    {
        DB::purge('images_testing');
        parent::tearDown();
    }

    private function controller(): ImageController
    {
        return new ImageController(new ImageService);
    }

    private function assertStatus(callable $callback, int $status): void
    {
        try {
            $callback();
            $this->fail('Expected HTTP '.$status);
        } catch (HttpException $exception) {
            $this->assertSame($status, $exception->getStatusCode());
        }
    }

    public function test_authorized_response_contains_decrypted_jpeg_and_private_headers(): void
    {
        $this->actor->shouldReceive('can')->once()->with('view', $this->application)->andReturn(true);
        $file = UploadedFile::fake()->image('document.jpg', 10, 10);
        $jpeg = file_get_contents($file->getRealPath());
        Http::fake(['files.example.invalid/*' => Http::response((new ImagePayload)->encrypt($jpeg))]);
        $response = $this->controller()->get($this->application, 'death_certificate');
        $this->assertSame($jpeg, $response->getContent());
        $this->assertSame('image/jpeg', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
    }

    public function test_unauthorized_and_unknown_document_requests_never_fetch_files(): void
    {
        Http::fake();
        $this->actor->shouldReceive('can')->once()->andReturn(false);
        $this->assertStatus(fn () => $this->controller()->get($this->application, 'death_certificate'), 403);
        Http::assertNothingSent();
    }

    public function test_unknown_document_is_404_without_upstream_request(): void
    {
        Http::fake();
        $this->actor->shouldReceive('can')->once()->andReturn(true);
        $this->assertStatus(fn () => $this->controller()->get($this->application, 'unknown'), 404);
        Http::assertNothingSent();
    }

    public function test_missing_image_and_disabled_downloads_are_404(): void
    {
        $this->actor->shouldReceive('can')->twice()->andReturn(true);
        Http::fake(['*' => Http::response('', 404)]);
        $this->assertStatus(fn () => $this->controller()->get($this->application, 'death_certificate'), 404);
        Http::assertSentCount(2);
        config(['services.fileserver.enable.get' => false]);
        $this->assertStatus(fn () => $this->controller()->get($this->application, 'death_certificate'), 404);
        Http::assertSentCount(2);
    }

    public function test_upstream_errors_corruption_and_outages_are_generic_502(): void
    {
        $this->actor->shouldReceive('can')->times(3)->andReturn(true);
        foreach ([Http::response('private upstream details', 500), Http::response('corrupted'), Http::failedConnection()] as $result) {
            Http::fake(['*' => $result]);
            $this->assertStatus(fn () => $this->controller()->get($this->application, 'death_certificate'), 502);
        }
    }

    public function test_duplicate_check_failure_stops_upload_and_is_not_retried(): void
    {
        config(['services.fileserver.mock' => true, 'services.fileserver.mock_token' => 'mock-only']);
        $access = \Mockery::mock(AccountAccess::class);
        $access->shouldReceive('assertWritable')->once()->andReturn($this->actor);
        $this->app->instance(AccountAccess::class, $access);
        Http::fake(['*' => Http::response('', 503)]);
        try {
            (new ImageService)->post('test', UploadedFile::fake()->image('file.png'));
            $this->fail('Failed existence check accepted.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('File existence check failed.', $exception->getMessage());
        }
        Http::assertSentCount(1);
        Http::assertNotSent(fn ($request) => $request->method() === 'POST');
    }

    public function test_mock_document_route_fetches_from_requirement_folder_with_server_side_token(): void
    {
        config(['services.fileserver.mock' => true, 'services.fileserver.mock_token' => 'mock-only', 'services.fileserver.mock_port' => 8099]);
        $this->actor->shouldReceive('can')->once()->with('view', $this->application)->andReturn(true);
        $file = UploadedFile::fake()->image('document.jpg', 10, 10);
        $jpeg = file_get_contents($file->getRealPath());
        Http::fake(['*' => Http::response(app(ImagePayload::class)->encrypt($jpeg))]);
        $response = $this->controller()->get($this->application, 'death_certificate');
        $this->assertSame($jpeg, $response->getContent());
        $this->assertStringNotContainsString('mock-only', $response->getContent());
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request->method() === 'GET'
            && str_starts_with($request->url(), 'http://127.0.0.1:8099/apiburial/?')
            && $request['folder'] === 'document-requirement-1'
            && $request['file'] === 'test123.jpg.enc'
            && $request['token'] === now()->format('Ymd').'mock-only');
    }

    public function test_folder_fetch_failure_does_not_fall_back_to_historical_files(): void
    {
        config(['services.fileserver.mock' => true, 'services.fileserver.mock_token' => 'mock-only']);
        $this->actor->shouldReceive('can')->once()->andReturn(true);
        Http::fake(['*' => Http::response('', 403)]);
        $this->assertStatus(fn () => $this->controller()->get($this->application, 'death_certificate'), 502);
        Http::assertSentCount(1);
    }

    public function test_remote_upload_and_fetch_use_new_contract_and_persist_server_returned_location(): void
    {
        $this->actor->shouldReceive('tokens')->never();
        $this->actor->shouldReceive('can')->once()->with('view', $this->application)->andReturn(true);
        $access = \Mockery::mock(AccountAccess::class);
        $access->shouldReceive('assertWritable')->once()->andReturn($this->actor);
        $this->app->instance(AccountAccess::class, $access);
        $file = UploadedFile::fake()->image('document.jpg', 10, 10);
        $document = DocumentRequirement::firstOrFail();
        $jpeg = file_get_contents($file->getRealPath());
        // A differently named folder is retained too, rather than reconstructed.
        Http::fakeSequence()->push('', 404)
            ->push(['status' => 'success', 'file' => '1793868436_test123.jpg.enc', 'folder' => 'storedcertificates'])
            ->push(app(ImagePayload::class)->encrypt($jpeg));
        $service = new ImageService;
        $this->assertTrue($service->postDocument($this->application, $document, $file));
        $reference = ApplicationDocumentFile::sole();
        $this->assertSame('remote', $reference->storage);
        $this->assertSame('1793868436_test123.jpg.enc', $reference->file_name);
        $this->assertSame('storedcertificates', $reference->folder);
        DB::table('document_requirements')->where('id', $document->id)->update(['name' => 'Renamed Certificate']);
        $response = $this->controller()->get($this->application, 'renamed_certificate');
        $this->assertSame($jpeg, $response->getContent());
        Http::assertSentCount(3);
        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request->url() === 'https://files.example.invalid/apiburial/'
            && $request->hasFile('action', 'upload')
            && $request->hasFile('folder', 'document-requirement-1')
            && $request->hasFile('token', now()->format('Ymd').'remote-test-secret')
            && $request->hasFile('file', null, 'test123.jpg.enc'));
        Http::assertSent(fn ($request) => $request->method() === 'GET'
            && $request['action'] === 'download'
            && $request['folder'] === 'storedcertificates'
            && $request['file'] === '1793868436_test123.jpg.enc'
            && $request['token'] === now()->format('Ymd').'remote-test-secret');
    }

    public function test_mock_and_remote_references_are_isolated_and_saved_missing_file_has_no_fallback(): void
    {
        foreach (['mock', 'remote'] as $storage) {
            ApplicationDocumentFile::create(['application_uuid' => $this->application->uuid, 'document_requirement_id' => 1,
                'storage' => $storage, 'folder' => 'document-requirement-1', 'file_name' => $storage.'.jpg.enc']);
        }
        Http::fake(['*' => Http::response('', 404)]);
        $service = new ImageService;
        $this->assertNull($service->getDocument($this->application, DocumentRequirement::firstOrFail()));
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request['file'] === 'remote.jpg.enc');
        Http::assertNotSent(fn ($request) => $request['file'] === 'mock.jpg.enc');
    }

    public function test_remote_upload_failures_or_invalid_locations_do_not_create_references(): void
    {
        $responses = [Http::response(['status' => 'error'], 400), Http::response('', 403), Http::response('not json'),
            Http::response(['status' => 'success', 'file' => '../outside.enc', 'folder' => 'claims']),
            Http::response(['status' => 'success', 'file' => 'stored.jpg.enc', 'folder' => 'nested/folder'])];
        $access = \Mockery::mock(AccountAccess::class);
        $access->shouldReceive('assertWritable')->times(count($responses))->andReturn($this->actor);
        $this->app->instance(AccountAccess::class, $access);
        foreach ($responses as $response) {
            Http::fake(fn ($request) => $request->method() === 'GET' ? Http::response('', 404) : $response);
            try {
                (new ImageService)->postDocument($this->application, DocumentRequirement::firstOrFail(), UploadedFile::fake()->image('file.png'));
                $this->fail('Invalid upload accepted.');
            } catch (\RuntimeException $exception) {
                $this->assertStringNotContainsString('remote-test-secret', $exception->getMessage());
            }
            $this->assertSame(0, ApplicationDocumentFile::count());
            Http::assertSentCount(2);
        }
    }

    public function test_post_only_configuration_uses_post_credentials_for_its_existence_check(): void
    {
        config(['services.fileserver.enable.get' => false, 'services.fileserver.get.endpoint' => '', 'services.fileserver.get.key' => '']);
        $access = \Mockery::mock(AccountAccess::class);
        $access->shouldReceive('assertWritable')->once()->andReturn($this->actor);
        $this->app->instance(AccountAccess::class, $access);
        Http::fakeSequence()->push('', 404)->push(['status' => 'success', 'file' => 'stored.jpg.enc', 'folder' => 'document-requirement-1']);
        $this->assertTrue((new ImageService)->postDocument($this->application, DocumentRequirement::firstOrFail(), UploadedFile::fake()->image('file.png')));
        Http::assertSent(fn ($request) => $request->method() === 'GET'
            && str_starts_with($request->url(), 'https://files.example.invalid/apiburial/?')
            && $request['token'] === now()->format('Ymd').'remote-test-secret');
        $this->assertSame(1, ApplicationDocumentFile::count());
    }

    public function test_missing_remote_credentials_and_connection_errors_do_not_expose_tokens(): void
    {
        Http::fake();
        config(['services.fileserver.get.key' => '']);
        try {
            (new ImageService)->get('sample.jpg.enc');
            $this->fail('Missing credentials accepted.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('File server token is not configured.', $exception->getMessage());
        }
        Http::assertNothingSent();
        config(['services.fileserver.get.key' => 'remote-test-secret']);
        Http::fake(['*' => Http::failedConnection('private URL with remote-test-secret')]);
        try {
            (new ImageService)->get('sample.jpg.enc');
            $this->fail('Connection failure accepted.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('File download failed.', $exception->getMessage());
            $this->assertNull($exception->getPrevious());
        }
    }

    public function test_date_prefix_is_regenerated_for_each_remote_request(): void
    {
        $file = UploadedFile::fake()->image('document.jpg', 10, 10);
        Http::fake(['*' => Http::response(app(ImagePayload::class)->encrypt(file_get_contents($file->getRealPath())))]);
        $service = new ImageService;
        $this->travelTo(now()->setDate(2026, 11, 11)->setTime(23, 59, 59));
        $service->get('sample.jpg.enc');
        $this->travel(2)->seconds();
        $service->get('sample.jpg.enc');
        Http::assertSent(fn ($request) => $request['token'] === '20261111remote-test-secret');
        Http::assertSent(fn ($request) => $request['token'] === '20261112remote-test-secret');
        $this->travelBack();
    }

    public function test_mock_service_and_commands_refuse_production(): void
    {
        $this->app->instance('env', 'production');
        config(['services.fileserver.mock' => true]);
        $this->artisan('fileserver:mock')->assertFailed();
        $this->artisan('fileserver:mock-cleanup', ['--dry-run' => true])->assertFailed();
        $this->expectException(\RuntimeException::class);
        new ImageService;
    }
}
