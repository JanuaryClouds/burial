<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Client;
use App\Models\Interview;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::whereHas('interviews')->get();

        foreach ($clients as $client) {
            if (rand(0,1) == 1 && $client->application) {
                Assessment::factory()->create([
                    'application_id' => $client->application->uuid,
                ]);
            }
        }
    }
}
