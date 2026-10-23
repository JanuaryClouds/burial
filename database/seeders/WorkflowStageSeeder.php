<?php

namespace Database\Seeders;

use App\Models\Workflow;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class WorkflowStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(WorkflowSeeder::class);
    }

    public static function stages(): array
    {
        return [
            // [
            //     'name' => 'Interview',
            //     'description' => 'Initial interview with the client to gather information about the deceased and the circumstances surrounding the death.',
            //     'permission' => 'Workflow Interview.Create',
            // ],
            // [
            //     'name' => 'Assessment',
            //     'description' => 'Assessment of the client’s needs and eligibility for assistance.',
            //     'permission' => 'Workflow Assessment.Create',
            // ],
            // [
            //     'name' => 'Recommendation',
            //     'description' => 'Recommendation for assistance based on the assessment.',
            //     'permission' => 'Workflow Recommendation.Create',
            // ],
            [
                'name' => 'Forward to Admin Staff',
                'description' => 'Reviewed and verification of the client\'s recommendation by an Admin.',
                'permission' => 'Workflow Admin',
            ],
            [
                'name' => 'Reviewed by Ms. Maricar',
                'description' => 'Verified the recommendee\'s eligibility and recommendation.',
                'permission' => 'Workflow Maricar',
            ],
            [
                'name' => 'Return to Admin',
                'description' => 'Returned to admin staff for further procedures.',
                'permission' => 'Workflow Admin',
            ],
            [
                'name' => 'Evaluated by Ms. Emma',
                'description' => 'Application to undergo evaluation.',
                'permission' => 'Workflow Emma',
            ],
            [
                'name' => 'Approved by Ms. Nikki',
                'description' => 'After multiple reviews and verification, approval must be given to proceed towards funding.',
                'permission' => 'Workflow Nikki',
            ],
            [
                'name' => 'Notified BAO',
                'description' => 'Verify the provided information via BAO.',
                'permission' => 'Workflow Bao',
            ],
            [
                'name' => 'Budget',
                'description' => 'Allocation of funds.',
                'permission' => 'Workflow Budget',
            ],
            [
                'name' => 'Accounting',
                'description' => 'Update the city government\'s budget before and after allocating funding for the recommended assistance.',
                'permission' => 'Workflow Accounting',
            ],
            [
                'name' => 'Treasury',
                'description' => 'Issuance of monetary funds via specified code of assistance.',
                'permission' => 'Workflow Treasury',
            ],
            [
                'name' => 'Releasing',
                'description' => 'Assistance is ready to be picked up by the client or claimant.',
                'permission' => 'Workflow Releasing',
            ],
        ];
    }
}
