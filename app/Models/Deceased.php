<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sex;

class Deceased extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'date_of_birth',
        'date_of_death',
        'gender',
    ];
    protected $table = "deceased";

    public function burialAssistance() {
        return $this->hasMany(BurialAssistance::class, 'deceased_id', 'id');
    }

    public function gender() {
        return $this->belongsTo(Sex::class, 'gender', 'id');
    }
}
