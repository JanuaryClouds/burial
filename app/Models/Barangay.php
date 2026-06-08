<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barangay extends Model
{
    protected $table = 'barangays';

    protected $fillable = [
        'name',
        'district_id',
        'remarks',
    ];

    public static function getAllBarangays()
    {
        return self::all();
    }

    /**
     * Summary of client
     * @return HasMany<Client>
     */
    public function client(): HasMany
    {
        return $this->hasMany(Client::class, 'barangay_id', 'id');
    }

    /**
     * Summary of beneficiary
     * @return HasMany<Beneficiary>
     */
    public function beneficiary(): HasMany
    {
        return $this->hasMany(Beneficiary::class, 'barangay_id', 'id');
    }

    /**
     * Summary of district
     * @return BelongsTo<District, Barangay>
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    /**
     * Summary of claimant
     * @return HasMany<Claimant>
     */
    public function claimant(): HasMany
    {
        return $this->hasMany(Claimant::class, 'barangay_id', 'id');
    }
}
