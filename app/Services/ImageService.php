<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;


class ImageService
{
    protected $serverUrl;
    protected $serverApi;
    protected $token;

    public function __construct()
    {
        $this->serverUrl = config('services.fileserver.url');
        $this->serverApi = config('services.fileserver.api');
        $this->token = config('services.fileserver.token');
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

        $response = Http::asMultipart()
            ->timeout(15)
            ->retry(3, 200)
            ->attach('file', file_get_contents($file->getRealPath()), $filename)
            ->withHeaders([
                'Content-Type' => 'multipart/form-data',
                'token' => $this->token . now()->format('Ymd'),
            ])
            ->post($this->serverUrl . $this->serverApi);

        dd($response->json());
        // FIXME
        // "status" => "error"
        // "message" => "No file uploaded or upload error."

        if ($response->failed() || $response->json()['status'] === 'error') {
            throw new \RuntimeException('{$response->status()}: Failed to upload file: ' . $filename);
        }

        return true;
    }
}
