<?php

namespace App\Services;

use App\Models\FuneralAssistanceType;
use App\Models\Workflow;
use App\Services\Auth\AccountAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FuneralAssistanceTypeService
{
    public function store(array $data): FuneralAssistanceType
    {
        app(AccountAccess::class)->assertWritable();
        $hint = filled($data['workflowUuid'] ?? null) ? Workflow::whereKey($data['workflowUuid'])->firstOrFail() : null;

        return DB::transaction(function () use ($data, $hint) {
            $this->validateSuggestion($hint);

            return FuneralAssistanceType::create([
                'name' => $data['name'], 'description' => $data['description'],
                'workflow_uuid' => $data['workflowUuid'] ?: null,
            ]);
        }, 3);
    }

    public function update(array $data, FuneralAssistanceType $type): bool
    {
        app(AccountAccess::class)->assertWritable();
        $hint = filled($data['workflowUuid'] ?? null) ? Workflow::whereKey($data['workflowUuid'])->firstOrFail() : null;

        return DB::transaction(function () use ($data, $type, $hint) {
            $this->validateSuggestion($hint);

            return $type->update(['name' => $data['name'], 'description' => $data['description'],
                'workflow_uuid' => $data['workflowUuid'] ?: null]);
        }, 3);
    }

    private function validateSuggestion(?Workflow $workflow): void
    {
        if (! $workflow) {
            return;
        }
        app(WorkflowVersionService::class)->lockGroup($workflow);
        if (! $workflow->isSelectable()) {
            throw ValidationException::withMessages(['form.workflowUuid' => 'Choose the latest active, complete workflow version or clear the suggestion.']);
        }
    }
}
