<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Auth\AccountAccess;
use App\Services\ImagePayload;
use App\Services\ImageService;
use App\Support\MockFileserverStorage;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
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
            'services.fileserver.mock' => true,
            'services.fileserver.mock_port' => $this->port,
            'services.fileserver.mock_token' => 'local-test-secret',
            'services.fileserver.enable.get' => true,
            'services.fileserver.enable.post' => true,
            'services.fileserver.enc_key' => 'base64:'.base64_encode(str_repeat('k', 32)),
        ]);
        $this->startServer();
        $this->client = new Client(['base_uri' => 'http://127.0.0.1:'.$this->port, 'http_errors' => false, 'timeout' => 20, 'expect' => false]);
    }

    private function startServer(string $duplicates = 'reject'): void
    {
        $this->server = new Process([
            PHP_BINARY, '-d', 'upload_max_filesize=33M', '-d', 'post_max_size=34M', '-d', 'display_errors=0',
            '-S', '127.0.0.1:'.$this->port, '-t', base_path('tools/fileserver-mock'), base_path('tools/fileserver-mock/router.php'),
        ], base_path(), ['BURIAL_MOCK_ROOT' => $this->root, 'BURIAL_MOCK_TOKEN' => 'local-test-secret', 'BURIAL_MOCK_DUPLICATES' => $duplicates, 'BURIAL_MOCK_MAX_BYTES' => '32768']);
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
        parent::tearDown();
    }

    private function upload(string $name, string $bytes, string $token = 'local-test-secret')
    {
        return $this->client->post('/upload', ['multipart' => [
            ['name' => 'token', 'contents' => $token],
            ['name' => 'file', 'contents' => $bytes, 'filename' => $name],
        ]]);
    }

    public function test_http_storage_preserves_opaque_bytes_and_rejects_invalid_credentials_and_duplicates(): void
    {
        $bytes = "\0not an image\xff".random_bytes(100);
        $this->assertSame(401, $this->upload('sample.jpg.enc', $bytes, 'wrong')->getStatusCode());
        $this->assertSame(404, $this->client->get('/burial/sample.jpg.enc')->getStatusCode());
        $this->assertSame(201, $this->upload('sample.jpg.enc', $bytes)->getStatusCode());
        $this->assertSame($bytes, (string) $this->client->get('/burial/sample.jpg.enc')->getBody());
        $this->assertSame(409, $this->upload('sample.jpg.enc', 'replacement')->getStatusCode());
        $this->assertSame($bytes, (string) $this->client->get('/burial/sample.jpg.enc')->getBody());
        $this->assertSame(400, $this->client->get('/burial/..%2Foutside')->getStatusCode());
        $this->assertSame(404, $this->client->get('/router.php')->getStatusCode());
        $this->assertSame(400, $this->upload('.lock', 'invalid')->getStatusCode());
    }

    public function test_mock_can_overwrite_for_direct_api_experiments(): void
    {
        $this->server->stop();
        $this->startServer('overwrite');
        $this->assertSame(201, $this->upload('same.enc', 'first')->getStatusCode());
        $this->assertSame(201, $this->upload('same.enc', 'second')->getStatusCode());
        $this->assertSame('second', (string) $this->client->get('/burial/same.enc')->getBody());
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
        $stored = (string) $this->client->get('/burial/'.$filename)->getBody();
        $this->assertStringStartsWith("BURIAL-IMAGE-V1\n", $stored);
        $jpeg = $service->get($filename);
        $this->assertSame(IMAGETYPE_JPEG, getimagesizefromstring($jpeg)[2]);
        $this->assertSame(app(ImagePayload::class)->decrypt($stored), $jpeg);
        $this->assertNull($service->get('missing.jpg.enc'));
    }

    public function test_upload_limit_and_opportunistic_cleanup(): void
    {
        $storage = new MockFileserverStorage($this->root);
        $storage->write('expired.enc', 'old', false);
        touch($this->root.'/expired.enc', time() - 169 * 3600);
        $this->assertSame(['expired.enc'], $storage->cleanup(168, true));
        $this->assertFileExists($this->root.'/expired.enc');
        $this->assertSame(201, $this->upload('fresh.enc', 'new')->getStatusCode());
        $this->assertFileDoesNotExist($this->root.'/expired.enc');
        $this->assertFileExists($this->root.'/fresh.enc');
        $this->assertSame(201, $this->upload('boundary.enc', str_repeat('x', 32768))->getStatusCode());
        $this->assertSame(413, $this->upload('large.enc', str_repeat('x', 32769))->getStatusCode());
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
