<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Relationship;

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
