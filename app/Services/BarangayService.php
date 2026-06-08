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
     * @param array $data
     * @param \App\Models\Barangay $barangay
     * @return \App\Models\Barangay
     */
    public function updateBarangay(array $data, Barangay $barangay): Barangay
    {
        $barangay->update($data);

        return $barangay->fresh();
    }

    /**
     * Summary of deleteBarangay
     * @param \App\Models\Barangay $barangay
     * @return bool
     */
    public function deleteBarangay($barangay): bool
    {
        return $barangay->delete();
    }
}
