<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkflowStep;
use App\Models\Handler;

class WorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $handlers = Handler::all();
        $workflowData = [
            [
              'order_no' => 1,
              'handler_id' => 1,
              'requires_extra_data' => false,
              'is_optional' => false,
              'extra_data_schema' => null,
            ],
            [
              'order_no' => 2,
              'handler_id' => 2,
              'requires_extra_data' => false,
              'is_optional' => false,
              'extra_data_schema' => null,
            ],
            [
              'order_no' => 3,
              'handler_id' => 3,
              'requires_extra_data' => false,
              'is_optional' => false,
              'extra_data_schema' => null,
            ],
            [
              'order_no' => 4,
              'handler_id' => 1,
              'requires_extra_data' => false,
              'is_optional' => false,
              'extra_data_schema' => null,
            ],
            [
              'order_no' => 5,
              'handler_id' => 1,
              'requires_extra_data' => false,
              'is_optional' => false,
              'extra_data_schema' => null,
            ],
            [
              'order_no' => 6,
              'handler_id' => 4,
              'requires_extra_data' => false,
              'is_optional' => false,
              'extra_data_schema' => null,
            ],
            [
              'order_no' => 7,
              'handler_id' => 5,
              'requires_extra_data' => false,
              'is_optional' => false,
              'extra_data_schema' => null,
            ],
            [
              'order_no' => 8,
              'handler_id' => 6,
              'requires_extra_data' => false,
              'is_optional' => false,
              'extra_data_schema' => null,
            ],
            [
                'order_no' => 9,
                'handler_id' => 7,
                'requires_extra_data' => true,
                'is_optional' => false,
                'extra_data_schema' => json_encode([
                    'OBR' => [
                        'obr_number' => 'string',
                        'date' => 'date',
                    ],
                ]),
            ],
            [
              'order_no' => 10,
              'handler_id' => 8,
              'requires_extra_data' => false,
              'is_optional' => false,
              'extra_data_schema' => null,
            ],
            [
              'order_no' => 11,
              'handler_id' => 9,
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
