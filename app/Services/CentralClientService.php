<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class CentralClientService
{
    /**
     * Fetch client details by passed key value from the central database.
     *
     * @param  string  $key  json key to match
     * @param  string  $value  array value to match
     * @return array returns the citizen data
     */
    public function fetchFromPortal(string $key, string $value): array
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
                $key => $value,
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

        return $data;
    }

    /**
     * Summary of checkIfUser
     *
     * @param  string  $key  json key to match
     * @param  string  $value  value to match
     * @param  bool  $remember  whether to remember the data to session
     * @return User|null returns the user if found
     */
    public function checkIfUser(string $key, string $value, bool $remember = false)
    {
        if ($value === '' || $key === '') {
            throw new RuntimeException('Missing key or value.');
        }

        $citizenData = [];

        $allowedKeys = ['uuid', 'emp_id'];
        if (! in_array($key, $allowedKeys, true)) {
            throw new RuntimeException("Invalid lookup key: {$key}");
        }

        $user = User::firstWhere($key === 'uuid' ? 'citizen_uuid' : $key, $value);
        $userEmail = $user?->email ?? '';
        if (config('services.portal.users.enable.get')) {
            if (! Str::endsWith($userEmail, [
                '@example.com',
                '@example.org',
                '@example.net',
            ])) {
                $citizenData = $this->fetchFromPortal($key, $value);
            }

            if (! empty($citizenData) && $remember) {
                session()->put('citizen', $this->filterData($citizenData[0]));
            }
        }

        if ($user) {
            if (! empty($citizenData)) {
                $user->fill([
                    'first_name' => $citizenData[0]['firstname'],
                    'middle_name' => $citizenData[0]['middlename'],
                    'last_name' => $citizenData[0]['lastname'],
                    'suffix' => $citizenData[0]['suffix'],
                    'email' => $citizenData[0]['email'],
                    'contact_number' => $citizenData[0]['contact_number'],
                ]);

                $user->save();
            }

            return $user;
        }

        if (empty($citizenData)) {
            return null;
        }

        if (User::where('email', $citizenData[0]['email'])->exists()) {
            return null;
        }

        try {
            return User::create([
                'citizen_uuid' => $citizenData[0]['user_id'],
                'emp_id' => $citizenData[0]['emp_id'] ?? null,
                'first_name' => $citizenData[0]['firstname'] ?? null,
                'middle_name' => $citizenData[0]['middlename'] ?? null,
                'last_name' => $citizenData[0]['lastname'] ?? null,
                'suffix' => $citizenData[0]['suffix'] ?? null,
                'email' => $citizenData[0]['email'] ?? null,
                'is_active' => true,
                'contact_number' => $citizenData[0]['contact_number'] ?? null,
                'password' => bcrypt(Str::random(32)),
            ]);
        } catch (QueryException $e) {
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
    private function filterData($citizen)
    {
        if (! is_array($citizen)) {
            return [];
        }

        return [
            'citizen_uuid' => $citizen['user_id'] ?? null,
            'emp_id' => $citizen['emp_id'] ?? null,
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
