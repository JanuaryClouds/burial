<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\DocumentRequirement;
use App\Models\User;
use App\Services\Auth\AccountAccess;
use App\Services\ImagePayload;
use App\Services\ImageService;
use App\Support\MockFileserverStorage;
use GuzzleHttp\Client;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class MockFileserverTest extends TestCase
{
    private string $root;

    private Process $server;

    private Client $client;

    private int $port;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = sys_get_temp_dir().'/burial-mock-test-'.bin2hex(random_bytes(8));
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        $this->port = (int) substr(strrchr(stream_socket_get_name($socket, false), ':'), 1);
        fclose($socket);
        config([
            'database.default' => 'mock_files_testing',
            'database.connections.mock_files_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'services.fileserver.mock' => true,
            'services.fileserver.mock_port' => $this->port,
            'services.fileserver.mock_token' => 'local-test-secret',
            'services.fileserver.enable.get' => true,
            'services.fileserver.enable.post' => true,
            'services.fileserver.enc_key' => 'base64:'.base64_encode(str_repeat('k', 32)),
        ]);
        Schema::create('applications', fn (Blueprint $table) => $table->uuid('uuid')->primary());
        Schema::create('document_requirements', fn (Blueprint $table) => $table->id());
        DB::table('applications')->insert([['uuid' => 'mock-application-1'], ['uuid' => 'mock-application-2']]);
        DB::table('document_requirements')->insert([['id' => 1], ['id' => 2]]);
        (require database_path('migrations/data/2026_11_11_120000_create_application_document_files_table.php'))->up();
        $this->startServer();
        $this->client = new Client(['base_uri' => 'http://127.0.0.1:'.$this->port, 'http_errors' => false, 'timeout' => 20, 'expect' => false]);
    }

    private function startServer(string $duplicates = 'reject'): void
    {
        $this->server = new Process([
            PHP_BINARY, '-d', 'upload_max_filesize=33M', '-d', 'post_max_size=34M', '-d', 'display_errors=0',
            '-S', '127.0.0.1:'.$this->port, '-t', base_path('tools/fileserver-mock'), base_path('tools/fileserver-mock/router.php'),
        ], base_path(), ['BURIAL_MOCK_ROOT' => $this->root, 'BURIAL_MOCK_TOKEN' => 'local-test-secret', 'BURIAL_MOCK_TIMEZONE' => (string) config('app.timezone'), 'BURIAL_MOCK_DUPLICATES' => $duplicates, 'BURIAL_MOCK_MAX_BYTES' => '32768']);
        $this->server->start();
        for ($attempt = 0; $attempt < 100; $attempt++) {
            $socket = @stream_socket_client('tcp://127.0.0.1:'.$this->port, $errno, $error, 0.05);
            if ($socket) {
                fclose($socket);

                return;
            }
            usleep(20000);
        }
        $this->fail('Mock server failed to start: '.$this->server->getErrorOutput());
    }

    protected function tearDown(): void
    {
        if (isset($this->server)) {
            $this->server->stop();
        }
        if (isset($this->root)) {
            File::deleteDirectory($this->root);
        }
        DB::purge('mock_files_testing');
        parent::tearDown();
    }

    private function download(string $name, string $folder = '')
    {
        return $this->client->get('/apiburial/', ['query' => [
            'action' => 'download', 'file' => $name, 'folder' => $folder,
            'token' => now()->format('Ymd').'local-test-secret',
        ]]);
    }

    public function test_all_actions_require_current_dated_token_and_old_routes_are_removed(): void
    {
        foreach (['view', 'download', 'list'] as $action) {
            foreach (['' => 401, 'local-test-secret' => 403, '20000101local-test-secret' => 403] as $token => $status) {
                $this->assertSame($status, $this->client->get('/apiburial/', ['query' => compact('action', 'token')])->getStatusCode());
            }
        }
        foreach (['upload', 'delete'] as $action) {
            $this->assertSame(401, $this->client->post('/apiburial/', ['json' => compact('action')])->getStatusCode());
            $this->assertSame(403, $this->client->post('/apiburial/', ['json' => ['action' => $action, 'token' => '20000101local-test-secret']])->getStatusCode());
        }
        $this->assertSame(404, $this->client->get('/burial/sample.enc')->getStatusCode());
        $this->assertSame(404, $this->client->post('/upload')->getStatusCode());
    }

    public function test_json_upload_folders_listing_view_and_deletion(): void
    {
        $params = ['token' => now()->format('Ymd').'local-test-secret', 'folder' => 'deathcertificates'];
        $bytes = 'opaque encrypted bytes';
        $upload = ['action' => 'upload', 'file_name' => 'tracking.jpg.enc', 'file_data' => base64_encode($bytes)];
        $response = $this->client->post('/apiburial/', ['json' => $params + $upload]);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertFalse(json_decode((string) $response->getBody(), true)['reencoded']);
        $this->assertSame($bytes, (string) $this->download('tracking.jpg.enc', 'deathcertificates')->getBody());
        $this->assertSame(404, $this->download('tracking.jpg.enc', 'claims')->getStatusCode());
        $view = $this->client->get('/apiburial/', ['query' => $params + ['action' => 'view', 'file' => 'tracking.jpg.enc']]);
        $this->assertSame($bytes, (string) $view->getBody());
        $this->assertStringStartsWith('inline;', $view->getHeaderLine('Content-Disposition'));
        $this->assertStringStartsWith('attachment;', $this->download('tracking.jpg.enc', 'deathcertificates')->getHeaderLine('Content-Disposition'));
        $list = $this->client->get('/apiburial/', ['query' => $params + ['action' => 'list', 'limit' => 1]]);
        $data = json_decode((string) $list->getBody(), true);
        $this->assertSame(1, $data['count']);
        $this->assertSame('tracking.jpg.enc', $data['files'][0]['name']);
        foreach (['../outside', '/absolute', 'a/b'] as $folder) {
            $this->assertSame(400, $this->download('tracking.jpg.enc', $folder)->getStatusCode());
        }
        $this->assertSame(400, $this->client->post('/apiburial/', ['json' => $params + array_replace($upload, ['file_data' => '%%%'])])->getStatusCode());
        $delete = $params + ['action' => 'delete', 'file' => 'tracking.jpg.enc'];
        $this->assertSame(200, $this->client->post('/apiburial/', ['json' => $delete])->getStatusCode());
        $this->assertSame(404, $this->download('tracking.jpg.enc', 'deathcertificates')->getStatusCode());
        $this->assertSame(404, $this->client->post('/apiburial/', ['json' => $delete])->getStatusCode());
    }

    private function upload(string $name, string $bytes, ?string $token = null)
    {
        return $this->client->post('/apiburial/', ['multipart' => [
            ['name' => 'action', 'contents' => 'upload'],
            ['name' => 'token', 'contents' => $token ?? now()->format('Ymd').'local-test-secret'],
            ['name' => 'file', 'contents' => $bytes, 'filename' => $name],
        ]]);
    }

    public function test_http_storage_preserves_opaque_bytes_and_rejects_invalid_credentials_and_duplicates(): void
    {
        $bytes = "\0not an image\xff".random_bytes(100);
        $this->assertSame(403, $this->upload('sample.jpg.enc', $bytes, 'wrong')->getStatusCode());
        $this->assertSame(404, $this->download('sample.jpg.enc')->getStatusCode());
        $this->assertSame(200, $this->upload('sample.jpg.enc', $bytes)->getStatusCode());
        $this->assertSame($bytes, (string) $this->download('sample.jpg.enc')->getBody());
        $this->assertSame(409, $this->upload('sample.jpg.enc', 'replacement')->getStatusCode());
        $this->assertSame($bytes, (string) $this->download('sample.jpg.enc')->getBody());
        $this->assertSame(400, $this->download('../outside')->getStatusCode());
        $this->assertSame(404, $this->client->get('/router.php')->getStatusCode());
        $this->assertSame(400, $this->upload('.lock', 'invalid')->getStatusCode());
    }

    public function test_mock_can_overwrite_for_direct_api_experiments(): void
    {
        $this->server->stop();
        $this->startServer('overwrite');
        $this->assertSame(200, $this->upload('same.enc', 'first')->getStatusCode());
        $this->assertSame(200, $this->upload('same.enc', 'second')->getStatusCode());
        $this->assertSame('second', (string) $this->download('same.enc')->getBody());
    }

    public function test_service_uploads_without_personal_tokens_and_decrypts_real_http_download(): void
    {
        $actor = new User;
        auth()->setUser($actor);
        $access = \Mockery::mock(AccountAccess::class);
        $access->shouldReceive('assertWritable')->once()->andReturn($actor);
        $this->app->instance(AccountAccess::class, $access);
        $service = new ImageService;
        $this->assertTrue($service->post('sample-document', UploadedFile::fake()->image('sample.png', 20, 20)));
        $filename = (app()->isLocal() ? 'test-' : '').'sample-document.jpg.enc';
        $stored = (string) $this->download($filename)->getBody();
        $this->assertStringStartsWith("BURIAL-IMAGE-V1\n", $stored);
        $jpeg = $service->get($filename);
        $this->assertSame(IMAGETYPE_JPEG, getimagesizefromstring($jpeg)[2]);
        $this->assertSame(app(ImagePayload::class)->decrypt($stored), $jpeg);
        $this->assertNull($service->get('missing.jpg.enc'));
    }

    public function test_document_uploads_share_requirement_folders_and_survive_requirement_renaming(): void
    {
        $actor = new User;
        auth()->setUser($actor);
        $access = \Mockery::mock(AccountAccess::class);
        $access->shouldReceive('assertWritable')->times(4)->andReturn($actor);
        $this->app->instance(AccountAccess::class, $access);
        $application = (new Application)->forceFill(['uuid' => 'mock-application-1', 'tracking_no' => '2026-0042']);
        $otherApplication = (new Application)->forceFill(['uuid' => 'mock-application-2', 'tracking_no' => '2026-0043']);
        $deathCertificate = (new DocumentRequirement)->forceFill(['id' => 1, 'name' => 'Death Certificate']);
        $deathCertificate->exists = true;
        $contract = (new DocumentRequirement)->forceFill(['id' => 2, 'name' => 'Funeral Contract']);
        $contract->exists = true;
        $service = new ImageService;
        $this->assertTrue($service->postDocument($application, $deathCertificate, UploadedFile::fake()->image('death.png', 10, 10)));
        $this->assertTrue($service->postDocument($application, $contract, UploadedFile::fake()->image('contract.png', 20, 20)));
        $this->assertTrue($service->postDocument($otherApplication, $deathCertificate, UploadedFile::fake()->image('other.png', 30, 30)));
        $this->assertFileExists($this->root.'/document-requirement-1/2026-0042.jpg.enc');
        $this->assertFileExists($this->root.'/document-requirement-1/2026-0043.jpg.enc');
        $this->assertFileExists($this->root.'/document-requirement-2/2026-0042.jpg.enc');
        $this->assertDirectoryDoesNotExist($this->root.'/2026-0042');
        $stored = (string) $this->download('2026-0042.jpg.enc', 'document-requirement-1')->getBody();
        $this->assertStringStartsWith("BURIAL-IMAGE-V1\n", $stored);
        $deathCertificate->name = 'Renamed Certificate';
        $this->assertSame(app(ImagePayload::class)->decrypt($stored), $service->getDocument($application, $deathCertificate));
        $this->assertSame(10, getimagesizefromstring($service->getDocument($application, $deathCertificate))[0]);
        $this->assertSame(20, getimagesizefromstring($service->getDocument($application, $contract))[0]);
        $this->assertSame(30, getimagesizefromstring($service->getDocument($otherApplication, $deathCertificate))[0]);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('2026-0042.jpg.enc already exists.');
        $service->postDocument($application, $deathCertificate, UploadedFile::fake()->image('duplicate.png', 10, 10));
    }

    public function test_document_fetch_still_reads_historical_flat_mock_files(): void
    {
        $application = (new Application)->forceFill(['uuid' => 'mock-application-1', 'tracking_no' => '2026-0042']);
        $document = (new DocumentRequirement)->forceFill(['id' => 1, 'name' => 'Death Certificate']);
        $document->exists = true;
        $file = UploadedFile::fake()->image('old.jpg', 10, 10);
        $jpeg = file_get_contents($file->getRealPath());
        $this->assertSame(200, $this->upload('2026-0042-death-certificate.jpg.enc', app(ImagePayload::class)->encrypt($jpeg))->getStatusCode());
        $this->assertSame($jpeg, (new ImageService)->getDocument($application, $document));
    }

    public function test_upload_limit_and_opportunistic_cleanup(): void
    {
        $storage = new MockFileserverStorage($this->root);
        $storage->write('expired.enc', 'old', false);
        $storage->folder('deathcertificates')->write('expired.enc', 'old', false);
        touch($this->root.'/deathcertificates/expired.enc', time() - 169 * 3600);
        touch($this->root.'/expired.enc', time() - 169 * 3600);
        $this->assertEqualsCanonicalizing(['expired.enc', 'deathcertificates/expired.enc'], $storage->cleanup(168, true));
        $this->assertFileExists($this->root.'/expired.enc');
        $this->assertSame(200, $this->upload('fresh.enc', 'new')->getStatusCode());
        $this->assertFileDoesNotExist($this->root.'/expired.enc');
        $this->assertFileDoesNotExist($this->root.'/deathcertificates/expired.enc');
        $this->assertFileExists($this->root.'/fresh.enc');
        $this->assertSame(200, $this->upload('boundary.enc', str_repeat('x', 32768))->getStatusCode());
        $this->assertSame(400, $this->upload('large.enc', str_repeat('x', 32769))->getStatusCode());
        $this->assertFileDoesNotExist($this->root.'/large.enc');
    }

    public function test_cleanup_command_is_confined_to_mock_storage_and_supports_dry_run(): void
    {
        $previous = storage_path();
        $this->app->useStoragePath($this->root.'/command-storage');
        try {
            $root = storage_path('app/private/fileserver-mock');
            $storage = new MockFileserverStorage($root);
            $storage->write('old.enc', 'expired', false);
            $storage->write('fresh.enc', 'keep', false);
            touch($root.'/old.enc', time() - 169 * 3600);
            file_put_contents($this->root.'/outside.enc', 'keep');
            $this->artisan('fileserver:mock-cleanup', ['--dry-run' => true])->assertSuccessful();
            $this->assertFileExists($root.'/old.enc');
            $this->artisan('fileserver:mock-cleanup')->assertSuccessful();
            $this->assertFileDoesNotExist($root.'/old.enc');
            $this->assertFileExists($root.'/fresh.enc');
            $this->assertFileExists($this->root.'/outside.enc');
            $this->artisan('fileserver:mock-cleanup', ['--hours' => '-1'])->assertFailed();
        } finally {
            $this->app->useStoragePath($previous);
        }
    }
}
