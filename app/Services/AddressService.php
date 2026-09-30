<?php

namespace App\Services;

use App\Models\Address;

class AddressService
{
    public function index()
    {
        //
    }

    public function store(array $data): Address
    {
        return Address::create($data);
    }

    public function edit(string $uuid): Address
    {
        return Address::whereUuid($uuid)->firstOrFail();
    }

    public function update(Address $address, array $data): Address
    {
        $address->update($data);

        return $address->fresh();
    }

    public function destroy(string $uuid)
    {
        //
    }
}