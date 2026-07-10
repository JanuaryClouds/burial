<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BeneficiarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientsCount = Client::count();

        Beneficiary::factory()->count($clientsCount)->create();
    }
}
