<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'source',
        'is_mandatory',
    ];

    public static function burial()
    {
        return [
            [
                'name' => 'Certified True Copy of Registered Death Certificate',
                'description' => null,
                'source' => 'Local Civil Registry',
                'is_mandatory' => true,
                'is_muslim' => false,
            ],
            [
                'name' => 'Original Copy of Funeral Contract',
                'description' => null,
                'source' => 'Funeral Establishment',
                'is_mandatory' => true,
                'is_muslim' => false,
            ],
            [
                'name' => 'Photocopy of Valid Identification Card of Deceased and Claimant',
                'description' => null,
                'source' => 'Local Civil Registry',
                'is_mandatory' => true,
                'is_muslim' => false,
            ],
            [
                'name' => 'Proof of Relationship',
                'description' => 'Examples include Marriage Contract, Birth Certificate, Baptismal Certificate.',
                'source' => 'Local Civil Registry',
                'is_mandatory' => true,
                'is_muslim' => false,
            ],
            [
                'name' => 'Certificate of Burial Rites (signed by IMAM)',
                'description' => '',
                'source' => 'Muslim/Islam Religious Community',
                'is_mandatory' => true,
                'is_muslim' => true,
            ],
            [
                'name' => 'Certificate of Internment issued by Muslim Cemetery',
                'description' => '',
                'source' => 'Muslim/Islam Religious Community',
                'is_mandatory' => true,
                'is_muslim' => true,
            ],
        ];
    }

    public static function funeral()
    {
        return [
            [
                'name' => 'Certified True Copy of Registered Death Certificate',
                'description' => null,
                'source' => 'Local Civil Registry',
                'is_mandatory' => true,
            ],
            [
                'name' => 'Burial Permit',
                'description' => null,
                'source' => 'Local Civil Registry & Barangay Hall of San Miguel',
                'is_mandatory' => true,
            ],
        ];
    }
}
