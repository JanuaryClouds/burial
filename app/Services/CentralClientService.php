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
    public function fetchByClient(string $key, string $value)
    {
        $apiKey = env('API_KEY_CITIZENS_USERS');
        $api = env('API_CITIZEN_USERS');
        if (! $apiKey) {
            return null;
        }

        if (config('app.env') === 'local') {
            // Temporary: Load from local file
            $path = storage_path('app/clients.json');
            if (file_exists($path)) {
                $response = json_decode(file_get_contents($path), true);
                $citizens = $response['data'];
                $citizen = collect($citizens)->firstWhere($key, $value);

                return $citizen;
            }
        } else {
            $response = Http::withHeader('X-Secret-Key', $apiKey)->get($api); // Temporarily disable to prevent repeated requests
            if ($response->failed()) {
                return null;
            } else {
                $decodedResponse = json_decode($response, true);
                $citizen = collect($decodedResponse['data'])->firstWhere($key, $value);

                return $citizen;
            }
        }

        return null;
    }

    /**
     * Fetch Citizen
     *
     * @return array
     */
    public function fetchCitizen(string $uuid)
    {
        $client = $this->fetchByClient('user_id', $uuid);
        if ($client) {
            return $client;
        }

        return [];
    }

    /**
     * Summary of checkIfUser
     *
     * @return User|null
     */
    public function checkIfUser(string $citizen_uuid)
    {
        $citizenData = $this->fetchCitizen($citizen_uuid);
        if (empty($citizenData)) {
            return User::where('citizen_id', $citizen_uuid)->first();
        }

        session(['citizen' => $this->filterData($citizenData)]);

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
