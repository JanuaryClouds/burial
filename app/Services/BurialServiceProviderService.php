<?php

namespace App\Services;

use App\Models\BurialServiceProvider;

class BurialServiceProviderService
{
    public function store(array $data)
    {
        return BurialServiceProvider::create($data);
    }

    public function update(array $data)
    {
        return BurialServiceProvider::update($data);
    }
}
