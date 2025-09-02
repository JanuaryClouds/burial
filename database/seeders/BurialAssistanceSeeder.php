<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Deceased;
use App\Models\Claimant;
use App\Models\BurialAssistance;

class BurialAssistanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deceased = Deceased::pluck('id');
        $claimant = Claimant::pluck('id');

        $burialAssistanceData = [
            [
                'tracking_no' => '2025-001',
                'application_date' => '2023-09-01',
                'swa' => 'SWA001',
                'encoder' => null,
                'funeraria' => 'Funeraria A',
                'deceased_id' => $deceased->random(),
                'claimant_id' => $claimant->random(),
                'amount' => '5000',
                'status' => 'pending',
                'remarks' => null,
                'initial_checker' => null,
            ],
            [
                'tracking_no' => '2025-002',
                'application_date' => '2023-09-01',
                'swa' => 'SWA002',
                'encoder' => null,
                'funeraria' => 'Funeraria B',
                'deceased_id' => $deceased->random(),
                'claimant_id' => $claimant->random(),
                'amount' => '7000',
                'status' => 'processing',
                'remarks' => "Urgent",
                'initial_checker' => null,
            ],
            [
                'tracking_no'=> '2025-003',
                'application_date' => '2023-09-02',
                'swa' => 'SWA003',
                'encoder' => null,
                'funeraria' => 'Funeraria C',
                'deceased_id' => $deceased->random(),
                'claimant_id' => $claimant->random(),
                'amount' => '6000',
                'status' => 'approved',
                'remarks' => null,
                'initial_checker' => null,
            ]
        ];

        foreach ($burialAssistanceData as $data) {
            BurialAssistance::firstOrCreate($data);
        }
    }
}
