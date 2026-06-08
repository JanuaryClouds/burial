<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    protected $table = 'districts';

    protected $fillable = [
        'name',
        'remarks',
    ];

    public static function getAllDistricts()
    {
        return self::all();
    }

    /**
     * Summary of client
     *
     * @return HasMany<Client>
     */
    public function client(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Summary of barangay
     *
     * @return HasMany<Barangay>
     */
    public function barangay(): HasMany
    {
        return $this->hasMany(Barangay::class);
    }
}
