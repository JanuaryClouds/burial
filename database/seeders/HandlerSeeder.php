<?php

namespace Database\Seeders;

use App\Models\Handler;
use Illuminate\Database\Seeder;

class HandlerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $handlerData = [
            [
                'id' => 1,
                'name' => 'Ms. Maricar',
                'type' => 'individual',
                'department' => 'CSWDO',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Admin Staff',
                'type' => 'individual',
                'department' => 'Admin',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'Out to Worker (Compliance)',
                'type' => 'individual',
                'department' => 'CSWDO',
                'is_active' => true,
            ],
            [
                'id' => 4,
                'name' => 'Ms. Emma',
                'type' => 'individual',
                'department' => 'CSWDO',
                'is_active' => true,
            ],
            [
                'id' => 5,
                'name' => 'Ms. Nikki',
                'type' => 'individual',
                'department' => 'CSWDO',
                'is_active' => true,
            ],
            [
                'id' => 6,
                'name' => 'BAO',
                'type' => 'organization',
                'department' => 'BAO',
                'is_active' => true,
            ],
            [
                'id' => 7,
                'name' => 'BUDGET',
                'type' => 'organization',
                'department' => 'BUDGET',
                'is_active' => true,
            ],
            [
                'id' => 8,
                'name' => 'Accounting',
                'type' => 'organization',
                'department' => 'Accounting',
                'is_active' => true,
            ],
            [
                'id' => 9,
                'name' => 'Treasury',
                'type' => 'organization',
                'department' => 'Treasury',
                'is_active' => true,
            ],
        ];

        foreach ($handlerData as $data) {
            Handler::firstOrCreate($data);
        }
    }
}
