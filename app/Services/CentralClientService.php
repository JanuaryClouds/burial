<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

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
        if ($user) {
            return $user;
        } else {
            $citizenData = $this->fetchCitizen($citizen_uuid);
            return $user = User::firstOrCreate(
                ['citizen_id' => $citizen_uuid],
                [
                    'citizen_id' => $citizen_uuid,
                    'first_name' => $citizenData['firstname'],
                    'middle_name' => $citizenData['middlename'],
                    'last_name' => $citizenData['lastname'],
                    'suffix' => $citizenData['suffix'],
                    'email' => $citizenData['email'],
                    'contact_number' => $citizenData['contact_number'],
                    'password' => bcrypt('funeral.password'),
                ]
            );
        }
    }
}
