<?php

namespace App\Services;

use App\Models\Barangay;

class BarangayService
{
    /**
     * Summary of storeBarangay
     * @param array $data information to store
     * @return Barangay
     */
    public function storeBarangay(array $data): Barangay
    {
        return Barangay::create($data);
    }

    /**
     * Summary of updateBarangay
     * @param array $data information to store
     * @param mixed $barangay model to update
     * @return Barangay updated model
     */
    public function updateBarangay(array $data, $barangay): Barangay
    {
        return $barangay->update($data);
    }

    /**
     * Summary of deleteBarangay
     * @param mixed $barangay model to delete
     * @return void if successful
     */
    public function deleteBarangay($barangay): void
    {
        $barangay->delete();
    }
}
