<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class burialAssistanceRequest extends Model
{
    use HasFactory;

    protected $table = "burial_assistance_requests";
    protected $BurialAssistanceRequestService;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        "uuid",
        "deceased_firstname",
        "deceased_lastname",
        "representative",
        "representative_contact",
        "rep_relationship",
        "burial_address",
        "barangay_id",
        "start_of_burial",
        "end_of_burial",
        "type_of_assistance",
        "remarks",
    ];

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public static function getAllBurialAssistanceRequests()
    {
        return self::orderBy("created_at", "desc")->get();
    }

    public static function getBurialAssistanceRequests($status) 
    {
        return self::query()->where('status', $status)->orderBy("created_at","desc")->simplePaginate(10);
    }
}
