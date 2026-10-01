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
        if (! config('services.psa_classification')) {
            throw new \Exception('PSA Classification not enabled');
        }

        $this->endpoint = config('services.psa_classification.endpoint');
        $this->version = config('services.psa_classification.version');
        $this->apiKey = config('services.psa_classification.api_key');
    }

    public function parseKey(string $code): array
    {
        $codeArray = explode(':', $code);

        if (count($codeArray) <= 0) {
            return [];
        }

        return [
            'reg' => $codeArray[0] != 0 ? $codeArray[0] : null,
            'prv' => $codeArray[1] != 0 ? $codeArray[1] : null,
            'mun' => $codeArray[2] != 0 ? $codeArray[2] : null,
            'bgy' => $codeArray[3] != 0 ? $codeArray[3] : null,
        ];
    }

    private function callApi(string $endpoint, array $parameters = [])
    {
        try {
            $url = $this->endpoint.'/'.$this->version.'/'.$endpoint;

            $response = Http::withQueryParameters([
                'token' => $this->apiKey,
                ...$parameters,
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

    public function getRegions()
    {
        try {
            return $this->callApi(
                'regions'
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getRegionOptions(): array
    {
        return collect($this->getRegions())
            ->mapWithKeys(function ($item) {
                return [$item['reg'].':0:0:0' => $item['area_name']];
            })
            ->toArray();
    }

    public function getProvinces(string $key)
    {
        try {
            return $this->callApi(
                'provinces',
                $this->parseKey($key),
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getProvinceOptions(string $regionCode): array
    {
        return collect($this->getProvinces($regionCode))
            ->sortBy('area_name')
            ->mapWithKeys(function ($item) {
                return [$item['reg'].':'.$item['prv'].':'.'0:0' => $item['area_name']];
            })
            ->toArray();
    }

    public function getMunicipalities(string $key)
    {
        try {
            return $this->callApi(
                'municipalities',
                $this->parseKey($key),
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getMunicipalityOptions(string $key): array
    {
        // ! The `prv` key is then used for filtering barangays because using `mun` would return barangays from other municipalities
        // * The `prv` key is used here because municipalities and provinces do have unique `prv` keys

        return collect($this->getMunicipalities($key))
            ->sortBy('area_name')
            ->mapWithKeys(function ($item) {
                return [$item['reg'].':'.$item['prv'].':'.$item['mun'].':0' => $item['area_name']];
            })
            ->toArray();
    }

    public function getBarangays(string $key)
    {
        try {
            return $this->callApi(
                'barangays',
                $this->parseKey($key)
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getBarangayOptions(string $key): array
    {
        return collect($this->getBarangays($key))
            ->sortBy('area_name')
            ->mapWithKeys(function ($item) {
                return [$item['reg'].':'.$item['prv'].':'.$item['mun'].':'.$item['bgy'] => $item['area_name']];
            })
            ->toArray();
    }
}
