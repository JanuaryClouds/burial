<?php

namespace App\Services;

use App\Models\DocumentRequirement;

class DocumentRequirementService
{
    public function __construct()
    {
        //
    }

    /**
     * Summary of store
     * @param array $data
     * @return DocumentRequirement
     */
    public function store(array $data): DocumentRequirement
    {
        return DocumentRequirement::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'source' => $data['source'],
            'is_required' => $data['is_required'] ?? false,
            'for_muslim' => $data['for_muslim'] ?? false,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Summary of update
     * @param DocumentRequirement $document
     * @param array $data
     * @return DocumentRequirement
     */
    public function update(DocumentRequirement $document, array $data): DocumentRequirement
    {
        $document->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'source' => $data['source'],
            'is_required' => $data['is_required'] ?? false,
            'for_muslim' => $data['for_muslim'] ?? false,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return $document;
    }
}