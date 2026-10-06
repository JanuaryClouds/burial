<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Database\Eloquent\Model;

class AddressService
{
    public function index()
    {
        //
    }

    public function store(array $data, Model $model): Address
    {
        $psa = app(PsaClassificationService::class);

        $address = Address::create([
            'addressable_type' => get_class($model),
            'addressable_id' => $model->uuid,
            'region_code' => $psa->parseKey($data['regionCode'])['reg'],
            'region_name' => $data['regionCode_display'],
            'province_code' => $data['provinceCode'] ? $psa->parseKey($data['provinceCode'])['prv'] : null,
            'province_name' => $data['provinceCode_display'],
            'municipality_code' => $data['municipalityCode'] ? $psa->parseKey($data['municipalityCode'])['mun'] : null,
            'municipality_name' => $data['municipalityCode_display'],
            'barangay_code' => $psa->parseKey($data['barangayCode'])['bgy'],
            'barangay_name' => $data['barangayCode_display'],
            'house_number' => $data['houseNumber'],
            'street' => $data['street'],
        ]);

        return $address;
    }

    public function edit(string $uuid): Address
    {
        return Address::whereUuid($uuid)->firstOrFail();
    }

    public function update(Address $address, array $data): Address
    {
        $psa = app(PsaClassificationService::class);

        $address->update([
            'region_code' => $psa->parseKey($data['regionCode'])['reg'],
            'region_name' => $data['regionCode_display'],
            'province_code' => $data['provinceCode'] ? $psa->parseKey($data['provinceCode'])['prv'] : null,
            'province_name' => $data['provinceCode_display'],
            'municipality_code' => $data['municipalityCode'] ? $psa->parseKey($data['municipalityCode'])['mun'] : null,
            'municipality_name' => $data['municipalityCode_display'],
            'barangay_code' => $psa->parseKey($data['barangayCode'])['bgy'],
            'barangay_name' => $data['barangayCode_display'],
            'house_number' => $data['houseNumber'],
            'street' => $data['street'],
        ]);

        return $address->fresh();
    }

    public function destroy(string $uuid)
    {
        //
    }
}
