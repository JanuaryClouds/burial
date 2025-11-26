<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'name',
        'description',
    ];

    public static function steps()
    {
        return [
            [
                'order' => 1,
                'name' => 'Fill out General Intake Sheet',
                'description' => 'Provide all required personal details, details of the deceased, family details.',
            ],
            [
                'order' => 2,
                'name' => 'Wait for SMS Interview',
                'description' => 'You will receive an SMS detailing the schedule of your interview.',
            ],
            [
                'order' => 3,
                'name' => 'Attend Interview',
                'description' => 'The interview will be held in the Taguig City Hall CSWDO Office. Make sure to bring hardcopies of all required documents.',
            ],
            [
                'order' => 4,
                'name' => 'Process Application',
                'description' => 'CSWDO will process your application once your interview is done and your application has been assessed.',
            ],
            [
                'order' => 5,
                'name' => 'Receive Assistance',
                'description' => 'Once your application is approved, you will be notified via SMS to receive your assistance.',
            ],
        ];
    }
}
