<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BurialAssistance extends Model
{
    use HasFactory;
    protected $fillable = [
        'tracking_no',
        'application_date',
        'swa',
        'encoder',
        'funeraria',
        'deceased_id',
        'claimant_id',
        'amount',
        'status',
        'remarks',
        'initial_checker',
    ];
}
