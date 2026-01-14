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
                'handler_id' => 1,
                'description' => 'Reviewed by Ms. Maricar',
                'requires_extra_data' => false,
                'is_optional' => false,
                'extra_data_schema' => null,
            ],
            [
                'order_no' => 2,
                'handler_id' => 2,
                'description' => 'Received by Admin Staff',
                'requires_extra_data' => false,
                'is_optional' => false,
                'extra_data_schema' => null,
            ],
            [
                'order_no' => 3,
                'handler_id' => 3,
                'description' => 'Compiled Documents by Worker',
                'requires_extra_data' => true,
                'is_optional' => false,
                'extra_data_schema' => json_encode([
                    'compiled_documents*' => 'string',
                ]),
            ],
            [
                'order_no' => 4,
                'handler_id' => 1,
                'description' => 'Received by Ms. Maricar',
                'requires_extra_data' => false,
                'is_optional' => false,
                'extra_data_schema' => null,
            ],
            [
                'order_no' => 5,
                'handler_id' => 1,
                'description' => 'Processed by Ms. Maricar',
                'requires_extra_data' => false,
                'is_optional' => false,
                'extra_data_schema' => null,
            ],
            [
                'order_no' => 6,
                'handler_id' => 4,
                'description' => 'Evaluated by Ms. Emma',
                'requires_extra_data' => false,
                'is_optional' => false,
                'extra_data_schema' => null,
            ],
            [
                'order_no' => 7,
                'handler_id' => 5,
                'description' => 'Reviewed by Ms. Nikki',
                'requires_extra_data' => false,
                'is_optional' => false,
                'extra_data_schema' => null,
            ],
            [
                'order_no' => 8,
                'handler_id' => 6,
                'description' => 'Forwarded to BAO',
                'requires_extra_data' => false,
                'is_optional' => false,
                'extra_data_schema' => null,
            ],
            [
                'order_no' => 9,
                'handler_id' => 7,
                'description' => 'Fowarded to Budget Department',
                'requires_extra_data' => true,
                'is_optional' => false,
                'extra_data_schema' => json_encode([
                    'OBR' => [
                        'oBR_number*' => 'string',
                        'date*' => 'date',
                    ],
                ]),
            ],
            [
                'order_no' => 10,
                'handler_id' => 8,
                'description' => 'Received by Accounting Office',
                'requires_extra_data' => false,
                'is_optional' => false,
                'extra_data_schema' => json_encode([
                    'dv_number*' => 'string',
                ]),
            ],
            [
                'order_no' => 11,
                'handler_id' => 9,
                'description' => 'Received by Treasury Office',
                'requires_extra_data' => false,
                'is_optional' => false,
                'extra_data_schema' => json_encode([
                    'cheque_number*' => 'string',
                    'amount*' => 'string',
                ]),
            ],
            [
                'order_no' => 12,
                'handler_id' => null,
                'description' => 'Cheque available for pickup',
                'requires_extra_data' => true,
                'is_optional' => false,
                'extra_data_schema' => json_encode([
                    'date_issued*' => 'date',
                ]),
            ],
            [
                'order_no' => 13,
                'handler_id' => null,
                'description' => 'Cheque claimed',
                'requires_extra_data' => false,
                'is_optional' => false,
                'extra_data_schema' => null,
            ],
        ];

        foreach ($workflowData as $data) {
            WorkflowStep::firstOrCreate($data);
        }
    }
}
