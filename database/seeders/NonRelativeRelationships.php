<?php

namespace Database\Seeders;

use App\Models\Relationship;
use Illuminate\Database\Seeder;

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
