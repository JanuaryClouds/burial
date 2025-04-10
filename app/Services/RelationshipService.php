<?php

namespace App\Services;

use App\Models\Relationship;

class RelationshipService
{
    public function storeRelationship(array $data): Relationship
    {
        return Relationship::create($data);
    }

    public function updateRelationship(array $data, $relationship): Relationship
    {
        if($relationship->update($data))
        {
            return $relationship;
        }
        return null;
    }

    public function deleteRelationship($relationship): Relationship
    {
        if($relationship->delete())
        {
            return $relationship;
        }
        return null;
    }
}