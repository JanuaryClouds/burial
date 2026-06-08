<?php

namespace App\Services;

use App\Models\Barangay;

class BarangayService
{
    public function storeBarangay(array $data): Barangay
    {
        return Barangay::create($data);
    }

    /**
     * Summary of updateBarangay
     */
    public function updateBarangay(array $data, Barangay $barangay): Barangay
    {
        $barangay->update($data);

        return $barangay->fresh();
    }

    /**
     * Summary of deleteBarangay
     *
     * @param  Barangay  $barangay
     */
    public function deleteBarangay($barangay): bool
    {
        return $barangay->delete();
    }
}
