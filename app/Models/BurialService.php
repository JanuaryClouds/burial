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
        "barangay_id",
        "start_of_burial",
        "end_of_burial",
        "burial_service_provider",
        "collected_funds",
        "remarks",
    ];

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function provider() {
        return $this->hasOne(BurialServiceProvider::class, 'id', 'burial_service_provider');
    }

    public static function getAllBurialServices()
    {
        return self::query()->simplePaginate(10);
    }
}
