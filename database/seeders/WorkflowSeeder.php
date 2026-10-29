<?php

namespace Database\Seeders;

use App\Models\FuneralAssistanceType;
use App\Models\User;
use App\Models\Workflow;
use App\Services\Auth\SeederAccountContext;
use App\Services\WorkflowVersionService;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $workflow = Workflow::latestVersion()->where('name', 'Funeral Assistance')->first();
        if (! $workflow) {
            $previous = auth()->user();
            auth()->setUser(User::role('superadmin')->firstOrFail());
            try {
                $workflow = SeederAccountContext::run(fn () => app(WorkflowVersionService::class)->publish([
                    'name' => 'Funeral Assistance',
                    'description' => 'Funeral Assistance budgeted by the Taguig City Local Government',
                    'stages' => array_map(fn ($stage) => [
                        'name' => $stage['name'], 'description' => $stage['description'], 'permission_id' => $stage['permission'],
                        'custom_fields' => $stage['custom_fields'] ?? [],
                    ], WorkflowStageSeeder::stages()),
                ]));
            } finally {
                $previous ? auth()->setUser($previous) : auth()->forgetUser();
            }
        }
        FuneralAssistanceType::whereNull('workflow_uuid')->update(['workflow_uuid' => $workflow->uuid]);
    }
}
