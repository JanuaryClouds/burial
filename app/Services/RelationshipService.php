<?php

namespace App\Services;

use App\Models\Relationship;

class RelationshipService
{
    /**
     * Summary of storeRelationship
     * @param array $data information to store
     * @return Relationship created model
     */
    public function storeRelationship(array $data): Relationship
    {
        return Relationship::create($data);
    }

    /**
     * Summary of updateRelationship
     * @param array $data information to update to
     * @param mixed $relationship model to update
     * @return Relationship updated model
     */
    public function updateRelationship(array $data, $relationship): Relationship
    {
        return $relationship->update($data);
    }

    /**
     * Summary of deleteRelationship
     * @param mixed $relationship model to delete
     * @return void if successful
     */
    public function deleteRelationship($relationship): void
    {
        $relationship->delete();
    }
}
