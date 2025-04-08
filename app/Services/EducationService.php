<?php

namespace App\Services;

use App\Models\Education;

class EducationService
{
    public function storeEducation(array $data): Education
    {
        return Education::create($data);
    }

    public function updateEducation(array $data, $education): void
    {
        $education->update($data);
    }

    public function deleteEducation(Education $education): void
    {
        $education->delete();
    }
}