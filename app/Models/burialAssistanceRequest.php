<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BurialAssistanceRequest extends Model
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
        "service_id",
        "remarks",
    ];

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, "barangay_id", "id");
    }

    public function relationship() {
        return $this->belongsTo(Relationship::class, "rep_relationship", 'id');
    }

    public function service() {
        return $this->belongsTo(BurialService::class);
    }

    public static function getAllBurialAssistanceRequests()
    {
        return self::orderBy("created_at", "desc")->get();
    }

    public static function getBurialAssistanceRequests($status) 
    {
        return self::query()->where('status', $status)->orderBy("created_at","desc")->simplePaginate(10);
    }

    public static function getApprovedAssistanceRequestsByDate($period) {
        $query = self::query()->where('status', 'approved');

        if ($period === "waiting") {
            $query->where('start_of_burial', '>', now('Asia/Manila'));
        } elseif ($period === "on-going") {
            $query->where('start_of_burial', '<=', now('Asia/Manila'))
                ->where('end_of_burial', '>=', now('Asia/Manila'));
        } elseif ($period === "completed") {
            $query->where('end_of_burial', '<', now('Asia/Manila'));
        }

        return $query->orderBy('start_of_burial', 'desc')->get();
    }

    public static function getBurialSchedules() {
        $schedule = self::where('status', 'approved')
        ->get()
        ->map(function ($request) {
            return [
                'title' => 'Burial for ' . $request->deceased_firstname .' '. $request->deceased_lastname,
                'start' => $request->start_of_burial,
                'end' => $request->end_of_burial,
                'url' => route('admin.burial.request.view', ['uuid' => $request->uuid]),
            ];
        });

        return $schedule;
    }
}
