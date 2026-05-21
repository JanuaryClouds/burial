<?php

namespace App\Services;

use App\Models\Relationship;

class RelationshipService
{
    public function storeRelationship(array $data): Relationship
    {
        return Relationship::create($data);
    }

    public function updateRelationship(array $data, Relationship $relationship): void
    {
        $relationship->update($data);
    }

    public function deleteRelationship(Relationship $relationship): void
    {
        $relationship->delete();
    }

    public function restoreRelationship(Relationship $relationship): void
    {
        $relationship->restore();
    }
}
