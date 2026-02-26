<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;


class ImageService
{
    protected $fileserverUrl;
    protected $fileserverApi;

    public function __construct()
    {
        $this->fileserverUrl = config('services.fileserver.url');
        $this->fileserverApi = config('services.fileserver.api');
    }
    
    public function get(string $filename)
    {
        $url = $this->fileserverUrl . '/burial/' . $filename;
        $request = Http::get($url);
        return $request->body();
    }
}
