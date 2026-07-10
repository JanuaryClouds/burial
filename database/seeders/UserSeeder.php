<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\CentralClientService;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
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
        try {
            if (config('services.portal.users.seeder.from_portal')) {
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
            } else {
                $uatClientUserIds = [
                    "fcf7c561-138b-4880-abae-e1ead49cfb94",
                    "319f4c59-4899-4bd6-9297-1411962fce7d",
                    "10c37c58-1646-4be7-8be8-1ab1b29f1a2d",
                    "cc1b37f7-89b6-4299-abfe-a137d2d18056",
                    "12c12994-9bc5-4e92-bbe3-64f3c32172d0",
                    "739da61e-e446-4d58-9643-5b846a9b3242",
                    "40f791d9-6b0b-4f69-81a2-d9dc2e38fff5",
                    "42da9815-71ec-4d0b-94c2-c4f37cf17fe1",
                    "e8a17182-a514-43ae-90dc-2906750f3276",
                    "00a13bc3-1b2a-427e-88ec-1fa08979b2e2",
                    "d2ae6d3c-c963-4905-bc33-74448ab9db11",
                    "7ab391b3-7e09-4d47-8890-4d56e6c3f1d7",
                    "69378be3-adf8-498a-b5d4-2a4fb6c88ede",
                    "bf247c90-c89d-4cf0-9625-c6731f291f23",
                    "29c20a1f-fa07-4852-85d6-778f2dd85077",
                    "b5e638ce-287a-4b04-9b66-25212cfcb1e7",
                    "e5211c8c-389e-4a32-a6b2-f88ab766faf6",
                    "88564a84-348d-4f1f-8df9-60f497bc049b",
                    "5126082f-e4ff-4f4a-89aa-4c8f16875a45",
                    "fd3cb95b-9207-4ef5-9b0f-1c2df738cd34"
                ];

                foreach ($uatClientUserIds as $index => $userId) {
                    User::firstOrCreate([
                        'email' => 'client'.$index.'tlcportal.com',
                        'citizen_uuid' => $userId,
                        'emp_id' => null,
                        'first_name' => 'Client',
                        'middle_name' => null,
                        'last_name' => 'TLC '.$index,
                        'suffix' => null,
                        'is_active' => true,
                        'contact_number' => null,
                        'password' => bcrypt(Str::random(32)),
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::info('Could not fetch client information from portal. Aborting to seeder.');

            User::factory()->count(10)->create([
                'citizen_uuid' => fn () => Str::uuid()->toString(),
            ]);
        }
    }
}
