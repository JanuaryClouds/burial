<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Str;

class CentralClientService
{
    /**
     * Fetch client details by UUID from the central database.
     *
     * @param  string  $key  array key to filter from
     * @param  string  $value  array value to match
     * @param  bool  $family  get family members
     * @return array|null
     */
    public function fetchFromPortal(string $key, string $value)
    {
        $apiKey = config('services.portal.key');
        $url = config('services.portal.url');
        if (! $apiKey) {
            return null;
        }

        $response = Http::withHeader('X-Secret-Key', $apiKey)
            ->timeout(15)
            ->retry(3, 200)
            ->get($url);

        if ($response->failed()) {
            return null;
        } else {
            $decodedResponse = json_decode($response, true);
            $citizen = collect($decodedResponse['data'])->firstWhere($key, $value);

            return $citizen;
        }
    }

    /**
     * Summary of checkIfUser
     *
     * @return User|null
     */
    public function checkIfUser(string $citizen_uuid)
    {
        // Disable for local to prevent unwanted API calls
        if (app()->isProduction()) {
            $citizenData = $this->fetchFromPortal('uuid', $citizen_uuid) ?? [];
            if (! empty($citizenData)) {
                session(['citizen' => $this->filterData($citizenData)]);
            }
        }
            
        return User::firstOrCreate([
            'citizen_id' => $citizen_uuid,
        ], [
            'first_name' => $citizenData['firstname'] ?? null,
            'middle_name' => $citizenData['middlename'] ?? null,
            'last_name' => $citizenData['lastname'] ?? null,
            'suffix' => $citizenData['suffix'] ?? null,
            'email' => $citizenData['email'] ?? null,
            'is_active' => true,
            'contact_number' => $citizenData['contact_number'] ?? null,
            'password' => bcrypt(Str::random(32)),
        ]);
    }

    /**
     * Summary of filterData
     *
     * @param  mixed  $citizen  citizen data to filter from
     * @return array
     */
    public function filterData($citizen)
    {
        if (! is_array($citizen)) {
            return [];
        }

        return [
            'firstname' => $citizen['firstname'] ?? null,
            'middlename' => $citizen['middlename'] ?? null,
            'lastname' => $citizen['lastname'] ?? null,
            'suffix' => $citizen['suffix'] ?? null,
            'age' => $citizen['age'] ?? null,
            'birthday' => $citizen['birthday'] ?? null,
            'sex' => isset($citizen['gender']) ? Str::ucfirst($citizen['gender']) : null,
            'street' => $citizen['latest_address']['street'] ?? null,
            'barangay' => $citizen['latest_address']['barangay'] ?? null,
            'civil_status' => $citizen['civil_status'] ?? null,
            'contact_number' => $citizen['contact_number'] ?? null,
        ];
    }
}
