<?php

namespace Tests\Feature;

use App\Http\Controllers\ImageController;
use App\Models\Application;
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
            'services.fileserver.endpoint' => 'https://files.example.invalid',
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
        Http::assertSentCount(1);
        config(['services.fileserver.enable.get' => false]);
        $this->assertStatus(fn () => $this->controller()->get($this->application, 'death_certificate'), 404);
        Http::assertSentCount(1);
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
