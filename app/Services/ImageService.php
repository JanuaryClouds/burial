<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class ImageService
{
    protected $serverUrl;
    protected $serverApi;

    public function __construct()
    {
        $this->serverUrl = config('services.fileserver.url');
        $this->serverApi = config('services.fileserver.api');
    }
    
    public function get(string $filename)
    {
        $filename = basename($filename);
        if (app()->environment('local')) {
            $filename = 'test-'.$filename;
        }
        $url = $this->serverUrl . '/burial/' . $filename;
        $request = Http::get($url);
        if ($request->failed()) {
            return null;
        }

        return $request->body();
    }

    public function post(string $filename, $file)
    {
        if (! $file->isValid()) {
            throw new \RuntimeException($filename . ' is not a valid file.');
        }

        $extension = $file->getClientOriginalExtension();
        if (app()->environment('local')) {
            $filename = 'test-'.$filename.'.'.$extension;
        }

        $url = $this->serverUrl;
        $duplicateCheck = Http::get($url . '/burial/' . $filename);
        if ($duplicateCheck->successful()) {
            throw new \RuntimeException($filename . ' already exists.');
        }

        if (auth()->user()->tokens()->count() === 0) {
            throw new \RuntimeException('No token found. You cannot upload images without a token.');
        }

        // This will not work during local because personal_access_tokens from live are the only accepted tokens
        $response = Http::asMultipart()
            ->timeout(15)
            ->retry(3, 200)
            ->attach('file', file_get_contents($file), $filename)
            ->post($this->serverUrl . $this->serverApi, [
                'token' => Auth::user()->tokens()->first()->token . now()->format('Ymd'),
            ]);

        if ($response->failed() || $response->json()['status'] === 'error') {
            throw new \RuntimeException('{$response->status()}: Failed to upload file: ' . $filename);
        }

        return true;
    }
}
