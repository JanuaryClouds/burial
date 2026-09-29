<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PsaClassificationService
{
    public string $endpoint;

    public string $version;

    public string $apiKey;

    public function __construct()
    {
        if (!config('services.psa_classification')) {
            throw new \Exception("PSA Classification not enabled");
        }

        $this->endpoint = config('services.psa_classification.endpoint');
        $this->version = config('services.psa_classification.version');
        $this->apiKey = config('services.psa_classification.api_key');
    }

    public function getRegions()
    {
        try {
            $url = $this->endpoint . '/' . $this->version . '/regions';

            $response = Http::withQueryParameters([
                'token' => $this->apiKey
            ])
                ->timeout(15)
                ->retry(3, 200)
                ->get($url);

            if ($response->failed()) {
                return [];
            }

            $decodedResponse = $response->json();

            if ($decodedResponse['count'] === 0) {
                return [];
            }

            $data = $decodedResponse['results'] ?? [];

            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getProvinces(string $regionCode)
    {
        try {
            $url = $this->endpoint . '/' . $this->version . '/provinces';

            $response = Http::withQueryParameters([
                'token' => $this->apiKey,
                'reg' => $regionCode,
            ])
                ->timeout(15)
                ->retry(3, 200)
                ->get($url);

            if ($response->failed()) {
                return [];
            }

            $decodedResponse = $response->json();

            if ($decodedResponse['count'] === 0) {
                return [];
            }

            $data = $decodedResponse['results'] ?? [];

            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getMunicipalities(string $regionCode)
    {
        try {
            $url = $this->endpoint . '/' . $this->version . '/municipalities';

            $response = Http::withQueryParameters([
                'token' => $this->apiKey,
                'reg' => $regionCode,
            ])
                ->timeout(15)
                ->retry(3, 200)
                ->get($url);

            if ($response->failed()) {
                return [];
            }

            $decodedResponse = $response->json();

            if ($decodedResponse['count'] === 0) {
                return [];
            }

            $data = $decodedResponse['results'] ?? [];

            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getBarangays(?string $provinceCode = null)
    {
        try {
            $url = $this->endpoint . '/' . $this->version . '/barangays';

            $response = Http::withQueryParameters([
                'token' => $this->apiKey,
                'prv' => $provinceCode,
            ])
                ->timeout(15)
                ->retry(3, 200)
                ->get($url);

            if ($response->failed()) {
                return [];
            }

            $decodedResponse = $response->json();

            if ($decodedResponse['count'] === 0) {
                return [];
            }

            $data = $decodedResponse['results'] ?? [];

            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}