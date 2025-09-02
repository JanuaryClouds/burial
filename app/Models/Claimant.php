<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claimant extends Model
{
    use HasFactory;
    protected $fillable = [
        'given_name',
        'middle_name',
        'last_name',
        'suffix',
        'relationship_to_deceased',
        'mobile_number',
    ];
    protected $table = "claimants";
}
