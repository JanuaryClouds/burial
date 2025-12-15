<?php

namespace Database\Seeders;

use App\Models\BurialAssistance;
use App\Models\Claimant;
use App\Models\ClaimantChange;
use Illuminate\Database\Seeder;

class ClaimantChangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $burialAssistances = BurialAssistance::pluck('id');
        $claimants = Claimant::pluck('id');

        $claimantChanges = [
            [
                'burial_assistance_id' => $burialAssistances->random(),
                'old_claimant_id' => $claimants->random(),
                'new_claimant_id' => $claimants->random(),
                'changed_at' => '2023-09-01 00:00:00',
                'reason_for_change' => 'Seeder Generated',
            ],
            [
                'burial_assistance_id' => $burialAssistances->random(),
                'old_claimant_id' => $claimants->random(),
                'new_claimant_id' => $claimants->random(),
                'changed_at' => '2023-09-04 00:00:00',
                'reason_for_change' => 'Seeder Generated',
            ],
            [
                'burial_assistance_id' => $burialAssistances->random(),
                'old_claimant_id' => $claimants->random(),
                'new_claimant_id' => $claimants->random(),
                'changed_at' => '2023-09-03 00:00:00',
                'reason_for_change' => 'Seeder Generated',
            ],
        ];

        foreach ($claimantChanges as $change) {
            ClaimantChange::firstOrCreate($change);
        }
    }
}
