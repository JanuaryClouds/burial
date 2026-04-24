<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\FileExtension;
use Intervention\Image\ImageManager;

class ImageService
{
    protected $serverUrl;

    protected $serverApi;

    public function __construct()
    {
        $this->serverUrl = config('services.fileserver.endpoint');
        $this->serverApi = config('services.fileserver.api');
    }

    public function get(string $filename)
    {
        $filename = basename($filename);
        if (app()->isLocal()) {
            $filename = 'test-'.$filename.'.jpg.enc';
        }
        if (app()->hasDebugModeEnabled() && app()->isLocal()) {
            $filename = 'test.png';
        }

        $url = $this->serverUrl.'/burial/'.$filename;
        $request = Http::get($url);
        if ($request->failed()) {
            return null;
        }

        return $request->body();
    }

    public function post(string $filename, $file)
    {
        if (config('services.fileserver.enable.post') === false) {
            throw new \RuntimeException('Fileserver is not enabled.');
        }

        if (! $file->isValid()) {
            throw new \RuntimeException($filename.' is not a valid file.');
        }

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (! in_array($file->getMimeType(), $allowedMimeTypes, true)) {
            throw new \RuntimeException($filename.' is not a valid image file.');
        }

        $image = ImageManager::usingDriver(Driver::class)->decode($file);
        $file = $image->encodeUsingFileExtension(FileExtension::JPG);
        if (app()->isLocal()) {
            $filename = 'test-'.$filename;
        }
        $filename = Str::slug($filename).'.jpg.enc';

        $url = $this->serverUrl;
        $duplicateCheck = Http::get($url.'/burial/'.$filename);
        if ($duplicateCheck->successful()) {
            throw new \RuntimeException($filename.' already exists.');
        }

        if (! auth()->check()) {
            throw new \RuntimeException('You are not logged in.');
        }

        if (auth()->user()->tokens()->count() === 0) {
            throw new \RuntimeException('No token found. You cannot upload images without a token.');
        }

        $fileContents = $file;
        $encKey = config('services.fileserver.enc_key');
        if (empty($encKey)) {
            throw new \RuntimeException('Encryption key is empty.');
        }
        $key = base64_decode(str_replace('base64:', '', $encKey));
        if (strlen($key) !== 32) {
            throw new \RuntimeException('Encryption key is invalid. It must be 32 bytes.');
        }

        $iv = random_bytes(16);
        $cipher = 'AES-256-CBC';

        $encrypted = openssl_encrypt(
            $fileContents,
            $cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encrypted === false) {
            throw new \RuntimeException('Failed to encrypt file.');
        }

        $hmac = hash_hmac('sha256', $encrypted, $key, true);
        $payload = $iv.$hmac.$encrypted;
        // This will not work during local because personal_access_tokens from live are the only accepted tokens
        $token = auth()->user()->tokens()->first()->token.now()->format('Ymd');
        $response = Http::asMultipart()
            ->timeout(15)
            ->retry(3, 200)
            ->attach('file', $payload, $filename)
            ->post($this->serverUrl.$this->serverApi, [
                'token' => $token,
            ]);

        if ($response->failed() || (isset($response->json()['status']) && $response->json()['status'] === 'error')) {
            $ip = request()->ip();
            $browser = request()->header('User-Agent');

            activity()
                ->causedBy(auth()->user())
                ->withProperties(['ip' => $ip, 'browser' => $browser])
                ->log('Failed to upload file: '.$filename);

            throw new \RuntimeException("{$response->status()}: Failed to upload file: ".$filename);
        }

        return true;
    }
}
