<?php

namespace App\Services;

use App\Models\Education;

class EducationService
{
    /**
     * Summary of storeEducation
     * @param array $data data to store
     * @return Education
     */
    public function storeEducation(array $data): Education
    {
        return Education::create($data);
    }

    /**
     * Summary of updateEducation
     * @param array $data data to update
     * @param mixed $education model to update
     * @return Education updated model
     */
    public function updateEducation(array $data, $education): Education
    {
        return $education->update($data);
    }

    /**
     * Summary of deleteEducation
     * @param Education $education model to delete
     * @return void if successful
     */
    public function deleteEducation(Education $education): void
    {
        $education->delete();
    }
}
