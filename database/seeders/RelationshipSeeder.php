<?php

namespace Database\Seeders;

use App\Models\Relationship;
use Illuminate\Database\Seeder;

class RelationshipSeeder extends Seeder
{
    public function run(): void
    {
        $Relationships = [
            'Son',
            'Daughter',
            'Mother',
            'Father',
            'Grandmother',
            'Grandfather',
            'Cousin',
            'Uncle',
            'Aunt',
            'Sibling',
        ];

        foreach ($Relationships as $Relationship) {
            Relationship::firstOrCreate([
                'name' => $Relationship,
                'remarks' => 'seeder generated',
            ]);
        }
    }
}
