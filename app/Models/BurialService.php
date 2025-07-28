<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BurialService extends Model
{
    use HasFactory;
    protected $table = "burial_services";
    protected $BurialServiceService;
    protected $fillable = [
        "deceased_firstname",
        "deceased_lastname",
        "representative",
        "representative_contact",
        "rep_relationship",
        "burial_address",
        "start_of_burial",
        "end_of_burial",
        "burial_service_provider",
        "collected_funds",
        "remarks",
    ];

    public function barangays()
    {
        return $this->hasOne(Barangay::class);
    }

    public function provider() {
        return $this->hasOne(BurialServiceProvider::class, 'id', 'burial_service_provider');
    }

    public static function getAllBurialServices()
    {
        return self::orderBy("created_at","desc")->get();
    }

}
