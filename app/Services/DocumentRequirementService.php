<?php

namespace App\Services;

use App\Models\DocumentRequirement;
use App\Services\Auth\AccountAccess;

class DocumentRequirementService
{
    public function __construct()
    {
        //
    }

    /**
     * Summary of store
     */
    public function store(array $data): DocumentRequirement
    {
        app(AccountAccess::class)->assertWritable();

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
     */
    public function update(DocumentRequirement $document, array $data): DocumentRequirement
    {
        app(AccountAccess::class)->assertWritable();
        $document->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'source' => $data['source'],
            'is_required' => $data['isRequired'] ?? false,
            'for_muslim' => $data['forMuslim'] ?? false,
            'is_active' => $data['isActive'] ?? true,
        ]);

        return $document;
    }
}
