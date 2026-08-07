<?php

namespace Database\Seeders;

use App\Models\Workflow;
use App\Models\WorkflowStage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkflowStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $primaryWorkflow = Workflow::where('name', 'Funeral Assistance')->first();

        foreach($this->stages() as $stage) {
            WorkflowStage::firstOrCreate([
                'workflow_uuid' => $primaryWorkflow->uuid,
                'name' => $stage['name'],
                'description' => $stage['description'],
            ]);
        }
    }

    public static function stages(): array {
        return [
            [
                'name' => 'Interview',
                'description' => 'Initial interview with the client to gather information about the deceased and the circumstances surrounding the death.',
                'permission' => array_values(RoleSeeder::queryPermission('name', 'interview')),
            ],
            [
                'name' => 'Assessment',
                'description' => 'Assessment of the client’s needs and eligibility for assistance.',
                'permission' => array_values(RoleSeeder::queryPermission('name', 'assessment')),
            ],
            [
                'name' => 'Recommendation',
                'description' => 'Recommendation for assistance based on the assessment.',
                'permission' => array_values(RoleSeeder::queryPermission('name', 'recommendation')),
            ],
            [
                'name' => 'Forward to Admin Staff',
                'description' => 'Reviewed and verification of the client\'s recommendation by an Admin.',
                'permission' => array_values(RoleSeeder::queryPermission('name', 'admin')),
            ],
            [
                'name' => 'Reviewed by Ms. Maricar',
                'description' => 'Verified the recommendee\'s eligibility and recommendation.',
                'permission' => array_values(RoleSeeder::queryPermission('name', 'maricar')),
            ],
            [
                'name' => 'Return to Admin',
                'description' => 'Returned to admin staff for further procedures.',
                'permission' => array_values(RoleSeeder::queryPermission('name', 'admin')),
            ],
            [
                'name' => 'Evaluated by Ms. Emma',
                'description' => 'Application to undergo evaluation.',
                'permission' => array_values(RoleSeeder::queryPermission('name', 'emma')),
            ],
            [
                'name' => 'Approved by Ms. Nikki',
                'description' => 'After multiple reviews and verification, approval must be given to proceed towards funding.',
                'permission' => array_values(RoleSeeder::queryPermission('name', 'nikki')),
            ],
            [
                'name' => 'Notified BAO',
                'description' => 'Verify the provided information via BAO.',
                'permission' => array_values(RoleSeeder::queryPermission('name', 'bao')),
            ],
            [
                'name' => 'Budget',
                'description' => 'Allocation of funds.',
                'permission' => array_values(RoleSeeder::queryPermission('name', 'budget')),
            ],
            [
                'name' => 'Accounting',
                'description' => 'Update the city government\'s budget before and after allocating funding for the recommended assistance.',
                'permission' => array_values(RoleSeeder::queryPermission('name', 'accounting')),
            ],
            [
                'name' => 'Treasury',
                'description' => 'Issuance of monetary funds via specified code of assistance.',
                'permission' => array_values(RoleSeeder::queryPermission('name', 'treasury')),
            ],
            [
                'name' => 'Releasing',
                'description' => 'Assistance is ready to be picked up by the client or claimant.',
                'permission' => array_values(RoleSeeder::queryPermission('name', 'releasing')),
            ],
            [
                'name' => 'Close',
                'description' => 'Client/Claimant has received the funeral assistance.',
                'permission' => array_values(RoleSeeder::queryPermission('name', 'closing')),
            ],
        ];
    }
}
