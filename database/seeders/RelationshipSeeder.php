<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Relationship;

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
            'Auntie',
            'Sibling',
        ];

        foreach ($Relationships as $Relationship) {
            Relationship::firstOrCreate([
                'name' => $Relationship,
                'remarks'   => 'seeder generated',
            ]);
        }
    }
}