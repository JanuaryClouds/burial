<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Models\Relationship;

class BurialService extends Model
{
    use HasFactory;
    use Searchable;
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

    public function relationship() {
        return $this->belongsTo(Relationship::class, "rep_relationship", 'id');
    }

    public static function getAllBurialServices()
    {
        return self::query()->simplePaginate(10);
    }

    public function request() {
        return $this->hasOne(BurialAssistanceRequest::class);
    }

    public function toSearchableArray() {
        return [
            'id' => $this->id,
            'deceased_firstname' => $this->deceased_firstname,
            'deceased_lastname' => $this->deceased_lastname,
            'representative' => $this->representative,
            'representative_contact' => $this->representative_contact,
            'burial_address' => $this->burial_address,
            'barangay_id' => optional($this->barangay)->name,
            'start_of_burial' => $this->start_of_burial,
            'end_of_burial' => $this->end_of_burial,
            'burial_service_provider' => optional($this->provider)->name,
            'collected_funds' => $this->collected_funds,
            'remarks' => $this->remarks,
        ];
    }
}
