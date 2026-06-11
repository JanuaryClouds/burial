<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\CentralClientService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $centralClientServices = new CentralClientService;

        $uatClients = [];
        for ($uatClientsCount = 1; $uatClientsCount <= 10; $uatClientsCount++) {
            $uatClientsArray = $centralClientServices->fetchFromPortal('search', 'client'.$uatClientsCount.'_tlc');
            if (count($uatClientsArray) == 0) {
                throw new RuntimeException('No client data found for client'.$uatClientsCount.'_tlc');
            } else {
                $uatClients[] = $uatClientsArray[0];
            }
        }

        foreach ($uatClients as $uatClient) {
            User::firstOrCreate([
                'email' => $uatClient['email'],
                'citizen_uuid' => $uatClient['user_id'],
                'emp_id' => $uatClient['emp_id'],
                'first_name' => $uatClient['firstname'],
                'middle_name' => $uatClient['middlename'] ?? null,
                'last_name' => $uatClient['lastname'],
                'suffix' => $uatClient['suffix'] ?? null,
                'is_active' => true,
                'contact_number' => $uatClient['contact_number'],
                'password' => bcrypt(Str::random(32)),
            ]);
        }

        User::factory()->count(10)->create([
            'citizen_uuid' => fn () => Str::uuid()->toString(),
        ]);
    }
}
