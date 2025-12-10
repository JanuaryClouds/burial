<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sex;

class Deceased extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'burial_assistance_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'date_of_birth',
        'date_of_death',
        'gender',
        'address',
        'barangay_id',
        'religion_id',
    ];
    protected $table = "deceased";

    public function burialAssistance() {
        return $this->belongsTo(BurialAssistance::class, 'burial_assistance_id', 'id');
    }

    public function gender() {
        return $this->belongsTo(Sex::class, 'gender', 'id');
    }

    public function barangay() {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'id');
    }

    public function religion() {
        return $this->belongsTo(Religion::class, 'religion_id', 'id');
    }
}
