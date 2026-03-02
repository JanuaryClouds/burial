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
     * @param string $citizen_uuid
     * @return object|User|\Illuminate\Database\Eloquent\Model
     */
    public function checkIfUser(string $citizen_uuid)
    {
        $user = User::where('citizen_id', $citizen_uuid)->first();
        $citizenData = $this->fetchCitizen($citizen_uuid);
        session(['citizen' => $this->filterData($citizenData)]);
        if ($user) {
            return $user;
        } else {
            return User::firstOrCreate([
                'citizen_id' => $citizen_uuid,
                'first_name' => $citizenData['firstname'],
                'middle_name' => $citizenData['middlename'],
                'last_name' => $citizenData['lastname'],
                'suffix' => $citizenData['suffix'],
                'email' => $citizenData['email'],
                'is_active' => true,
                'contact_number' => $citizenData['contact_number'],
                'password' => bcrypt(Str::random(32)),
            ]);
        }
    }

    /**
     * Summary of filterData
     * @param mixed $citizen citizen data to filter from
     * @return array
     */
    public function filterData($citizen)
    {
        return [
            'firstname' => $citizen['firstname'],
            'middlename' => $citizen['middlename'] ?? null,
            'lastname' => $citizen['lastname'],
            'suffix' => $citizen['suffix'] ?? null,
            'age' => $citizen['age'],
            'birthday' => $citizen['birthday'],
            'sex' => Str::ucfirst($citizen['gender']),
            'street' => $citizen['latest_address']['street'] ?? null,
            'barangay' => $citizen['latest_address']['barangay'] ?? null,
            'civil_status' => $citizen['civil_status'],
            'contact_number' => $citizen['contact_number'] ?? null,
        ];
    }
}
