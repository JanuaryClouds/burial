<?php

namespace Database\Seeders;

use App\Models\Relationship;
use Illuminate\Database\Seeder;

class SpouseRelationship extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spouseRelationships = [
            'Spouse',
            'Brother-in-law',
            'Sister-in-law',
            'Father-in-law',
            'Mother-in-law',
        ];

        foreach ($spouseRelationships as $relationship) {
            Relationship::firstOrCreate([
                'name' => $relationship,
                'remarks' => 'seeder generated',
            ]);
        }
    }
}
