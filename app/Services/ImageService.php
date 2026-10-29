<?php

namespace App\Services;

use App\Services\Auth\AccountAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\FileExtension;
use Intervention\Image\ImageManager;
use RuntimeException;

class ImageService
{
    protected string $serverUrl;

    protected string $serverApi;

    protected bool $mock;

    public function __construct()
    {
        $this->mock = (bool) config('services.fileserver.mock');
        if ($this->mock && ! app()->environment('local', 'testing')) {
            throw new RuntimeException('Mock fileserver is only available locally or in tests.');
        }
        $port = filter_var(config('services.fileserver.mock_port'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1024, 'max_range' => 65535]]);
        if ($this->mock && $port === false) {
            throw new RuntimeException('Invalid mock fileserver port.');
        }
        $this->serverUrl = $this->mock ? 'http://127.0.0.1:'.$port : rtrim((string) config('services.fileserver.endpoint'), '/');
        $this->serverApi = $this->mock ? '/upload' : '/'.ltrim((string) config('services.fileserver.api'), '/');
    }

    private function request()
    {
        // Never redirect an upload carrying credentials to another host.
        return Http::connectTimeout(5)->timeout(15)->withoutRedirecting();
    }

    private function requireEndpoint(): void
    {
        if ($this->serverUrl === '') {
            throw new RuntimeException('File server is not configured.');
        }
    }

    public function get(string $filename): ?string
    {
        if (! config('services.fileserver.enable.get')) {
            return null;
        }
        $this->requireEndpoint();
        $response = $this->request()->get($this->serverUrl.'/burial/'.rawurlencode(basename($filename)));
        if ($response->status() === 404) {
            return null;
        }
        if (! $response->successful()) {
            throw new RuntimeException('File download failed.');
        }

        return app(ImagePayload::class)->decrypt($response->body());
    }

    public function post(string $filename, $file)
    {
        app(AccountAccess::class)->assertWritable();
        if (! config('services.fileserver.enable.post')) {
            activity()->causedBy(Auth::user())
                ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent'), 'filename' => $filename])
                ->log('File upload service skipped');

            return true;
        }
        if (! $file->isValid() || ! in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new RuntimeException($filename.' is not a valid image file.');
        }
        $jpeg = (string) ImageManager::usingDriver(Driver::class)->decode($file)->encodeUsingFileExtension(FileExtension::JPG);
        $filename = Str::slug($filename).'.jpg.enc';
        $this->requireEndpoint();
        if (! Auth::check()) {
            throw new RuntimeException('You are not logged in.');
        }
        if ($this->mock) {
            $token = (string) config('services.fileserver.mock_token');
            if ($token === '') {
                throw new RuntimeException('Mock fileserver token is not configured.');
            }
        } else {
            // Preserve the remote server's existing, undocumented token protocol.
            $personalAccessToken = Auth::user()->tokens()->first();
            if ($personalAccessToken === null) {
                throw new RuntimeException('No token found. You cannot upload images without a token.');
            }
            $token = $personalAccessToken->token.now()->format('Ymd');
        }
        $payload = app(ImagePayload::class)->encrypt($jpeg);
        $duplicateCheck = $this->request()->get($this->serverUrl.'/burial/'.rawurlencode($filename));
        if ($duplicateCheck->successful()) {
            throw new RuntimeException($filename.' already exists.');
        }
        if ($duplicateCheck->status() !== 404) {
            throw new RuntimeException('File existence check failed.');
        }
        // No automatic retry: the server may have stored a timed-out upload.
        $response = $this->request()->asMultipart()->attach('file', $payload, $filename)
            ->post($this->serverUrl.$this->serverApi, ['token' => $token]);
        if (! $response->successful() || $response->json('status') === 'error') {
            activity()->causedBy(Auth::user())
                ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
                ->log('Failed to upload file: '.$filename);
            throw new RuntimeException($response->status().': Failed to upload file: '.$filename);
        }

        return true;
    }
}
