<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BurialServiceProvider extends Model
{
    use HasFactory;

    protected $table = "burial_service_providers";
    protected $BurialServiceProviderService;

    protected $fillable = [
        'name',
        'contact_details',
        'address',
        'barangay_id',
        'remarks'
    ];

    public static function getAllProviders() 
    {
        return self::all();
    }

    public function servicesProvided()
    {
        // TODO: create a burial service model
        // return $this->hasMany();
    }

    public function barangay() {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }
}
