<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Illuminate\Support\Str;

class CentralClientService
{
    /**
     * Fetch client details by UUID from the central database.
     *
     * @param  string  $value  array value to match
     * @return array Citizen Data if successfully fetched one citizen, empty array if not
     */
    public function fetchFromPortal(string $value)
    {
        if (config('services.portal.users.enable.get') === false) {
            throw new RuntimeException('User fetching from portal is disabled.');
        }

        $apiKey = config('services.portal.users.key');
        $url = config('services.portal.users.endpoint');

        if (! $url) {
            throw new RuntimeException('User fetching from portal is not configured.');
        }

        if (! $apiKey) {
            throw new RuntimeException('User fetching from portal is not configured.');
        }

        $response = Http::withHeader('X-Secret-Key', $apiKey)
            ->withQueryParameters([
                'uuid' => $value
            ])
            ->timeout(15)
            ->retry(3, 200)
            ->get($url);

        if ($response->failed()) {
            return [];
        } 

        $decodedResponse = $response->json();
        $data = $decodedResponse['data'] ?? [];

        if (count($data) !== 1) {
            activity()
                ->withProperties(['citizen_uuid' => $value, 'count' => count($data)])
                ->log('Invalid record count returned for UUID');

            return [];
        }

        return $data[0];
    }

    /**
     * Summary of checkIfUser
     *
     * @param  string  $citizen_uuid  UUID or user_id of citizen from the portal
     * @return User|null
     */
    public function checkIfUser(string $citizen_uuid)
    {
        if (! $citizen_uuid) {
            throw new RuntimeException('Missing citizen UUID');
        }

        $citizenData = [];

        $user = User::firstWhere('citizen_uuid', $citizen_uuid);
        $userEmail = $user?->email ?? '';
        if (config('services.portal.users.enable.get')) {
            if (! Str::endsWith($userEmail, [
                '@example.com',
                '@example.org',
                '@example.net'
            ])) {
                $citizenData = $this->fetchFromPortal($citizen_uuid);
            }

            if (! empty($citizenData)) {
                session(['citizen' => $this->filterData($citizenData)]);
            }
        }

        if ($user) {
            return $user;
        }

        if (empty($citizenData)) {
            return null;
        }
        
        if ($citizen_uuid !== ($citizenData['user_id'] ?? null)) {
            return null;
        }

        if (User::where('email', $citizenData['email'])->exists()) {
            return null;
        }

        try {
            return User::create([
                'citizen_uuid' => $citizenData['user_id'] ?? null,
                'first_name' => $citizenData['firstname'] ?? null,
                'middle_name' => $citizenData['middlename'] ?? null,
                'last_name' => $citizenData['lastname'] ?? null,
                'suffix' => $citizenData['suffix'] ?? null,
                'email' => $citizenData['email'] ?? null,
                'is_active' => true,
                'contact_number' => $citizenData['contact_number'] ?? null,
                'password' => bcrypt(Str::random(32)),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() !== '23000') {
                throw $e;
            }

            return null;
        }
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
            'citizen_uuid' => $citizen['user_id'] ?? null,
            'first_name' => $citizen['firstname'] ?? null,
            'middle_name' => $citizen['middlename'] ?? null,
            'last_name' => $citizen['lastname'] ?? null,
            'suffix' => $citizen['suffix'] ?? null,
            'age' => $citizen['age'] ?? null,
            'birthday' => $citizen['birthday'] ?? null,
            'sex' => isset($citizen['gender']) ? Str::ucfirst($citizen['gender']) : null,
            'street' => $citizen['street'] ?? null,
            'barangay' => $citizen['barangay'] ?? null,
            'civil_status' => $citizen['civil_status'] ?? null,
            'contact_number' => $citizen['contact_number'] ?? null,
        ];
    }
}
