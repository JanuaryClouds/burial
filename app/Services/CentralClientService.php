<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Storage;
use function PHPUnit\Framework\isInstanceOf;

class CentralClientService
{
    public $client;

    /**
     * Fetch client details by UUID from the central database.
     *
     * @param string $uuid
     * @return array|null
     */
    public function fetchByUuid(string $uuid)
    {
        $apiKey = env('API_KEY_CITIZENS_USERS');
        $api = env('API_CITIZEN_USERS');
        if (! $apiKey) {
            return null;
        }

        if (env('APP_DEBUG')) {
            // Temporary: Load from local file
            $path = storage_path('app/clients.json');
        } else {
            $response = Http::withHeader('X-Secret-Key', $apiKey)->get($api); // Temporarily disable to prevent repeated requests
            $decodedResponse = json_decode($response, true);
            $clients = $decodedResponse['data'];
        }

        if (file_exists($path)) {
            $response = json_decode(file_get_contents($path), true);
            $clients = $response['data'];
            $this->client = collect($clients)->firstWhere('user_id', $uuid);
            return $this->client;
        }

        return null;
    }

    public function fetchFamily()
    {

    }
}
