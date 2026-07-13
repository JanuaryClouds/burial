<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::select('uuid')->get();

        foreach ($clients as $client) {
            Application::factory()->create([
                'client_uuid' => $client->uuid,
                'beneficiary_uuid' => Beneficiary::inRandomOrder()->first()->uuid,
            ]);
        }

        dump(Application::count() . ' Applications Seeded');
    }
}
