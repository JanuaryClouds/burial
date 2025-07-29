<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Relationship;

class NonRelativeRelationships extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nonRelativeRelationships = [
            'Neighbor',
            'Friend',
            'Coworker',
            'Employer',
            'Others',
        ];

        foreach ($nonRelativeRelationships as $relationship) {
            Relationship::firstOrCreate([
                'name' => $relationship,
                'remarks' => 'seeder generated',
            ]);
        }
    }
}
