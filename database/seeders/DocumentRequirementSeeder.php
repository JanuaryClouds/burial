<?php

namespace Database\Seeders;

use App\Models\DocumentRequirement;
use Illuminate\Database\Seeder;

class DocumentRequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documents = [
            [
                'name' => 'Certified True Copy of Registered Death Certificate',
                'source' => 'Local Civil Registry',
                'is_required' => true,
                'for_muslim' => false,
            ],
            [
                'name' => 'Original Copy of Funeral Contract',
                'source' => 'Funeral Establishment',
                'is_required' => true,
                'for_muslim' => false,
            ],
            [
                'name' => 'Photocopy of Valid ID of Deceased',
                'source' => 'Local Civil Registry',
                'is_required' => true,
                'for_muslim' => false,
            ],
            [
                'name' => 'Proof of Relationship',
                'description' => 'Examples include Marriage Contract, Birth Certificate, Baptismal Certificate.',
                'source' => 'Local Civil Registry',
                'is_required' => true,
                'for_muslim' => false,
            ],
            [
                'name' => 'Certificate of Burial Rites with signature of IMAM',
                'source' => 'Muslim/Islam Religious Community',
                'is_required' => true,
                'for_muslim' => true,
            ],
            [
                'name' => 'Certificate of Interment issued by Muslim Cemetery',
                'source' => 'Muslim/Islam Religious Community',
                'is_required' => true,
                'for_muslim' => true,
            ],
            [
                'name' => 'Burial Permit',
                'source' => 'Local Civil Registry',
                'is_required' => true,
                'for_muslim' => true,
            ],
        ];

        foreach ($documents as $document) {
            DocumentRequirement::firstOrCreate([
                'name' => $document['name'],
                'description' => $document['description'] ?? null,
                'source' => $document['source'],
                'is_required' => $document['is_required'],
                'for_muslim' => $document['for_muslim'],
            ]);
        }
    }
}
