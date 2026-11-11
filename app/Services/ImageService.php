<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationDocumentFile;
use App\Models\DocumentRequirement;
use App\Services\Auth\AccountAccess;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\FileExtension;
use Intervention\Image\ImageManager;
use RuntimeException;

class ImageService
{
    protected bool $mock;

    protected string $mockEndpoint;

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
        $this->mockEndpoint = 'http://127.0.0.1:'.$port.'/apiburial/';
    }

    private function request()
    {
        // No redirects or automatic retries: credentials stay on the configured host,
        // and a timed-out upload might already have been stored.
        return Http::connectTimeout(5)->timeout(15)->withoutRedirecting();
    }

    private function endpoint(string $capability): string
    {
        $endpoint = $this->mock ? $this->mockEndpoint : (string) config('services.fileserver.'.$capability.'.endpoint');
        $url = parse_url($endpoint);
        if (! is_array($url) || ! in_array($url['scheme'] ?? '', ['http', 'https'], true) || empty($url['host'])
            || isset($url['user']) || isset($url['pass']) || isset($url['query']) || isset($url['fragment'])) {
            throw new RuntimeException('File server endpoint is not configured correctly.');
        }

        // This is the complete API URL, including /apiburial/ or the direct script.
        return $endpoint;
    }

    private function token(string $capability): string
    {
        $secret = (string) config($this->mock ? 'services.fileserver.mock_token' : 'services.fileserver.'.$capability.'.key');
        if ($secret === '') {
            throw new RuntimeException('File server token is not configured.');
        }

        return now()->format('Ymd').$secret;
    }

    private function destination(): string
    {
        return $this->mock ? 'mock' : 'remote';
    }

    private function reference(Application $application, DocumentRequirement $document)
    {
        return ApplicationDocumentFile::query()->where('application_uuid', $application->uuid)
            ->where('document_requirement_id', $document->getKey())->where('storage', $this->destination());
    }

    private function fetch(string $filename, string $folder = '', string $capability = 'get')
    {
        try {
            return $this->request()->get($this->endpoint($capability), [
                'action' => 'download', 'file' => $filename, 'folder' => $folder, 'token' => $this->token($capability),
            ]);
        } catch (ConnectionException) {
            // The original exception URL can contain the system token.
            throw new RuntimeException('File download failed.');
        }
    }

    public function getDocument(Application $application, DocumentRequirement $document): ?string
    {
        if (! config('services.fileserver.enable.get')) {
            return null;
        }
        $reference = $this->reference($application, $document)->first();
        if ($reference) {
            return $this->get($reference->file_name, $reference->folder);
        }
        $jpeg = $this->get(Str::slug($application->tracking_no).'.jpg.enc', $document->storageFolder());
        if ($jpeg !== null) {
            return $jpeg;
        }

        // Historical flat files still use authenticated requests to the new API.
        return $this->get(Str::slug($application->tracking_no.'-'.$document->name).'.jpg.enc');
    }

    public function postDocument(Application $application, DocumentRequirement $document, $file): bool
    {
        $this->upload($application->tracking_no, $file, $document->storageFolder(), $application, $document);

        return true;
    }

    public function get(string $filename, string $folder = ''): ?string
    {
        if (! config('services.fileserver.enable.get')) {
            return null;
        }
        $this->validateLocation($filename, $folder);
        $response = $this->fetch($filename, $folder);
        if ($response->status() === 404) {
            return null;
        }
        if (! $response->successful()) {
            throw new RuntimeException('File download failed.');
        }

        return app(ImagePayload::class)->decrypt($response->body());
    }

    public function post(string $filename, $file, string $folder = ''): bool
    {
        $this->upload($filename, $file, $folder);

        return true;
    }

    private function validateLocation(mixed $filename, mixed $folder): void
    {
        if (! is_string($filename) || ! preg_match('/\A[a-zA-Z0-9][a-zA-Z0-9_.-]{0,199}\z/D', $filename)
            || ! is_string($folder) || ($folder !== '' && ! preg_match('/\A[a-zA-Z0-9][a-zA-Z0-9_.-]{0,199}\z/D', $folder))) {
            throw new RuntimeException('Invalid file server location.');
        }
    }

    private function upload(string $filename, $file, string $folder, ?Application $application = null, ?DocumentRequirement $document = null): void
    {
        app(AccountAccess::class)->assertWritable();
        app(ImageUploadValidator::class)->validate($file);
        if (! config('services.fileserver.enable.post')) {
            activity()->causedBy(Auth::user())
                ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent'), 'filename' => $filename])
                ->log('File upload service skipped');

            return;
        }
        if (! $file->isValid() || ! in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new RuntimeException('Invalid image file.');
        }
        $filename = Str::slug($filename).'.jpg.enc';
        $this->validateLocation($filename, $folder);
        $endpoint = $this->endpoint('post');
        $token = $this->token('post');
        if ($application && $document && $this->reference($application, $document)->exists()) {
            throw new RuntimeException($filename.' already exists.');
        }
        // Preflight uses the upload capability's URL/key even if public GET is disabled.
        $duplicateCheck = $this->fetch($filename, $folder, 'post');
        if ($duplicateCheck->successful()) {
            throw new RuntimeException($filename.' already exists.');
        }
        if ($duplicateCheck->status() !== 404) {
            throw new RuntimeException('File existence check failed.');
        }
        $jpeg = (string) ImageManager::usingDriver(Driver::class)->decode($file)->encodeUsingFileExtension(FileExtension::JPG);
        $payload = app(ImagePayload::class)->encrypt($jpeg);
        try {
            $response = $this->request()->asMultipart()->attach('file', $payload, $filename)
                ->post($endpoint, ['token' => $token, 'action' => 'upload', 'folder' => $folder]);
        } catch (ConnectionException) {
            throw new RuntimeException('File upload failed.');
        }
        if (! $response->successful() || $response->json('status') !== 'success') {
            throw new RuntimeException('File upload failed.');
        }
        // Never reconstruct a server-generated name (the real API may prefix timestamps).
        $storedName = $response->json('file');
        $storedFolder = $response->json('folder');
        $this->validateLocation($storedName, $storedFolder);
        if ($application && $document) {
            ApplicationDocumentFile::create([
                'application_uuid' => $application->uuid, 'document_requirement_id' => $document->getKey(),
                'storage' => $this->destination(), 'folder' => $storedFolder, 'file_name' => $storedName,
            ]);
        }
    }
}
