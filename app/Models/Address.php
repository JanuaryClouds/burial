<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    /** @use HasFactory<AddressFactory> */
    use HasFactory, HasUuid;

    protected $table = 'addresses';

    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'region_code',
        'region_name',
        'province_code',
        'province_name',
        'municipality_code',
        'municipality_name',
        'barangay_code',
        'barangay_name',
        'street',
        'house_number',
    ];

    protected $casts = [
        'street' => 'encrypted',
        'house_number' => 'encrypted',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Relationships
    |--------------------------------------------------------------------------
    |
    | Addressable relation.
    |
    */

    public function addressable()
    {
        return $this->morphTo();
    }

    /**
     * Summary of client
     *
     * @return BelongsTo<Client, Address>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Summary of beneficiary
     *
     * @return BelongsTo<Beneficiary, Address>
     */
    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Model functions
    |--------------------------------------------------------------------------
    |
    | Public functions.
    |
    */

    public function full(): string
    {
        return $this->house_number.' '.$this->street.', '.$this->barangay_name.', '
        .($this->municipality_name ?? $this->province_name).', '.$this->region_name;
    }
}
