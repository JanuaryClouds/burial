<?php

namespace App\Services;

use App\Models\Barangay;

class BarangayService
{
    public function storeBarangay(array $data): Barangay
    {
        return Barangay::create($data);
    }

    public function updateBarangay(array $data, $barangay): Barangay
    {
        if($barangay->update($data))
        {
            return $barangay;
        }
        return null;
    }

    public function deleteBarangay($barangay): Barangay
    {
        if($barangay->delete())
        {
            return $barangay;
        }
        return null;
    }
}