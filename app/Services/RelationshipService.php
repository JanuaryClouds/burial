<?php

namespace App\Services;

use App\Models\Relationship;

class RelationshipService
{
    public function storeRelationship(array $data): Relationship
    {
        return Relationship::create($data);
    }

    public function updateRelationship(array $data, $relationship): void
    {
        $relationship->update($data);
    }

    public function deleteRelationship($relationship): void
    {
        $relationship->delete();
    }
}