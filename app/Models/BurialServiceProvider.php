<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class BurialServiceProvider extends Model
{
    use HasFactory;
    use Searchable;

    protected $table = 'burial_service_providers';

    protected $BurialServiceProviderService;

    protected $fillable = [
        'name',
        'contact_details',
        'address',
        'barangay_id',
        'remarks',
    ];

    public static function getAllProviders()
    {
        return self::all();
    }

    public function burialServices()
    {
        return $this->hasMany(BurialService::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'id');
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'contact_details' => $this->contact_details,
            'address' => $this->address,
            'barangay_id' => optional($this->barangay)->name,
            'remarks' => $this->remarks,
        ];
    }
}
