<?php

namespace Database\Seeders;

use App\Models\WorkflowStep;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workflowData = [
            [
                'order_no' => 1,
                'description' => 'Reviewed by Ms. Maricar',
            ],
            [
                'order_no' => 2,
                'description' => 'Received by Admin Staff',
            ],
            [
                'order_no' => 3,
                'description' => 'Compiled Documents by Worker',
            ],
            [
                'order_no' => 4,
                'description' => 'Received by Ms. Maricar',
            ],
            [
                'order_no' => 5,
                'description' => 'Processed by Ms. Maricar',
            ],
            [
                'order_no' => 6,
                'description' => 'Evaluated by Ms. Emma',
            ],
            [
                'order_no' => 7,
                'description' => 'Reviewed by Ms. Nikki',
            ],
            [
                'order_no' => 8,
                'description' => 'Forwarded to BAO',
            ],
            [
                'order_no' => 9,
                'description' => 'Fowarded to Budget Department',
            ],
            [
                'order_no' => 10,
                'description' => 'Received by Accounting Office',
            ],
            [
                'order_no' => 11,
                'description' => 'Received by Treasury Office',
            ],
            [
                'order_no' => 12,
                'description' => 'Cheque available for pickup',
            ],
            [
                'order_no' => 13,
                'description' => 'Cheque claimed',
            ],
        ];

        foreach ($workflowData as $data) {
            WorkflowStep::firstOrCreate($data);
        }
    }
}
