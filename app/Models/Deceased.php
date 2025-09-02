<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deceased extends Model
{
    use HasFactory;
    protected $fillable = [
        'given_name',
        'middle_name',
        'last_name',
        'suffix',
        'date_of_birth',
        'date_of_death',
        'gender',
        'address',
        'barangay_id',
    ];
    protected $table = "deceased";

}
