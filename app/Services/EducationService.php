<?php

namespace App\Services;

use App\Models\Education;

class EducationService
{
    public function storeEducation(array $data): Education
    {
        return Education::create($data);
    }

    public function updateEducation(array $data, $education): Education
    {
        if ($education->update($data)) {
            return $education;
        }

        return null;
    }

    public function deleteEducation(Education $education): Education
    {
        if ($education->delete()) {
            return $education;
        }

        return null;
    }
}
